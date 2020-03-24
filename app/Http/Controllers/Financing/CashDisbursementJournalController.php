<?php

namespace App\Http\Controllers\Financing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Financing\JournalInfo;
use App\Models\Financing\JournalAccounts;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
class CashDisbursementJournalController extends Controller
{

    public function index()
    {
        return Reference::collection($this->GetCashDisbursementJournals()->get())            
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {

        if($request->input('payment_method_id') == 2)
        {
            Validator::make($request->all(),
                [
                    'bank_id' => 'required|not_in:0',
                    'amount' => 'required|not_in:0',
                ],  ['not_in' => 'The :attribute field is required.']
            )->setAttributeNames([
                'bank_id' => 'Bank',
                'amount'=>'Check Amount'
                ]
            )->validate();
        }

        Validator::make($request->all(),
            [
                'check_date' => 'required_if:payment_method_id,2',
                'check_no' => 'required_if:payment_method_id,2',
                'department_id' => 'required|not_in:0',
                'particular_id' => 'required|not_in:0',
                'payment_method_id' => 'required|not_in:0',
                'date_txn' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.',
                'required_if' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'department_id' => 'Department',
                'particular_id' => 'Particular',
                'check_no' => 'Check Number',
                'check_date' => 'Check Date',
                'payment_method_id' => 'Payment Method',
                'date_txn' => 'Transaction Date']
            )->validate();

            $ref_type_count = 1 + COUNT( JournalInfo::where('ref_type',$request->input('ref_type'))->get() );

        $cashdisbursement = new JournalInfo();
        $particular=explode('-',$request->input('particular_id'));
        if($particular[0]=='C'){
            $cashdisbursement->customer_id=$particular[1];
            $cashdisbursement->supplier_id=0;
        }else{
            $cashdisbursement->customer_id=0;
            $cashdisbursement->supplier_id=$particular[1];
        }
        $cashdisbursement->date_created = Carbon::now();
        $cashdisbursement->department_id = $request->input('department_id');
        $cashdisbursement->payment_method_id = $request->input('payment_method_id');
        $cashdisbursement->bank_id = $request->input('bank_id');
        $cashdisbursement->ref_type = $request->input('ref_type');
        $cashdisbursement->ref_no = str_pad($ref_type_count, 8, "0", STR_PAD_LEFT) ;
        $cashdisbursement->amount = $request->input('amount');
        $cashdisbursement->check_no = $request->input('check_no');
        $cashdisbursement->date_txn = date("Y-m-d", strtotime($request->input('date_txn')));
        $cashdisbursement->check_date = date("Y-m-d", strtotime($request->input('check_date')));
        $cashdisbursement->remarks = $request->input('remarks');
        $cashdisbursement->book_type = 'CDJ';
        if($cashdisbursement->save()){ // MAKE SURE JOURNAL IS SAVED FIRST
            $entries = $request->input('journalentry');
            foreach($entries as $entry){
                $entries_dataSet[] = [
                    'journal_id' => $cashdisbursement->journal_id,
                    'account_id' => $entry['account_id'],
                    'memo' => $entry['memo'],
                    'dr_amount' => $entry['dr_amount'],
                    'cr_amount' => $entry['cr_amount']
                ];
            }
            DB::table('journal_accounts')->insert($entries_dataSet);
            $gentxn = JournalInfo::findOrFail($cashdisbursement->journal_id);
            $gentxn->txn_no = 'TXN-'.date('Ymd').'-'.$cashdisbursement->journal_id;
            $gentxn->save();
        }

        return ( new Reference( $this->GetCashDisbursementJournals($cashdisbursement->journal_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function showFiles($id)
    {
        $data['accounts'] = JournalAccounts::select(
            'account_titles.account_no',
            'account_titles.account_title',
            'journal_accounts.journal_account_id',
            'journal_accounts.memo',
            'journal_accounts.dr_amount',
            'journal_accounts.cr_amount',

            )
        ->where('journal_id',$id)
        ->leftJoin('account_titles', 'account_titles.account_id', '=', 'journal_accounts.account_id')
        ->get();
        $data["dr_total"] = JournalAccounts::where('journal_id',$id)->sum('dr_amount');
        $data["cr_total"] = JournalAccounts::where('journal_id',$id)->sum('cr_amount');
        return $data;
    }


    public function cancelJournal($id)
    {
        $journal = JournalInfo::findOrFail($id);
        if($journal->is_active == TRUE) { 
            $journal->is_active = FALSE;
        }else{
            $journal->is_active = TRUE; 
        }
        $journal->save();
        return ( new Reference( $this->GetCashDisbursementJournals($id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }


    public function GetCashDisbursementJournals($id=null, $start=null, $end= null) 
    {
        $journals = JournalInfo::select(
            'journal_info.*',
            'payment_methods.payment_method',
            DB::raw('DATE_FORMAT(journal_info.date_txn,"%m/%d/%Y")as date_txn'),
            DB::raw('DATE_FORMAT(journal_info.check_date,"%m/%d/%Y")as check_date'),
            DB::raw('CONCAT(journal_info.ref_type,"-",journal_info.ref_no) as reference_no'),
            DB::raw('CONCAT(IF(NOT ISNULL(customers.customer_id),CONCAT("C-",customers.customer_id),""),IF(NOT ISNULL(suppliers.supplier_id),CONCAT("S-",suppliers.supplier_id),"")) as particular_id'),
            DB::raw("CONCAT_WS(' ',user_accounts.user_fname,user_accounts.user_lname) as posted_by"),
            DB::raw('CONCAT_WS(" ",IFNULL(customers.customer_name,""),IFNULL(suppliers.supplier_name,"")) as particular'),
            DB::raw('"" as accounts, "" as dr_total, "" as cr_total')
            )
            ->leftJoin('customers', 'customers.customer_id', '=', 'journal_info.customer_id')
            ->leftJoin('suppliers', 'suppliers.supplier_id', '=', 'journal_info.supplier_id')
            ->leftJoin('payment_methods', 'payment_methods.payment_method_id', '=', 'journal_info.payment_method_id')
            ->leftJoin('user_accounts', 'user_accounts.user_id', '=', 'journal_info.created_by_user')
            ->where('journal_info.is_deleted',FALSE)
            ->where('journal_info.book_type','CDJ')
            ->orderBy('journal_info.journal_id','desc')
            ;

            if($start != null && $end != null){
                $journals->whereBetween('journal_info.date_txn', [$start, $end]);
            }
            if($id != null){
                $journals->where('journal_id',$id);
            }

        return $journals;
    }

}

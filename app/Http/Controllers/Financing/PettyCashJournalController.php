<?php

namespace App\Http\Controllers\Financing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Financing\JournalInfo;
use App\Models\Financing\JournalAccounts;
use App\Models\Settings\GeneralConfiguration;
use App\Models\References\AccountTitle;
use App\Models\References\Batch;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
class PettyCashJournalController extends Controller
{

    public function index($as_of_date = null,$department_id=null)
    {
        
        return Reference::collection($this->GetPettyCashList(null,$as_of_date,$department_id)->get())
        ->response()
        ->setStatusCode(200);
    }

    public function get_totals($as_of_date = null,$department_id=null)
    {
        $balance = DB::select( DB::raw("
        SELECT
        (CASE WHEN x.`account_type_id` = 1 OR x.account_type_id=5 THEN
        IFNULL(((x.dr_amount) - (x.cr_amount)),0)
        ELSE
        IFNULL(((x.cr_amount) - (x.dr_amount)),0)
        END) as balance
        FROM
            (SELECT
            petty_cash_account_id,
            ja.journal_id,
            ac.account_type_id,
            SUM(ja.dr_amount) AS dr_amount,
            SUM(ja.cr_amount) AS cr_amount,
            ji.date_txn,
            ji.department_id
            
            FROM `account_integration` AS ai

        LEFT JOIN journal_accounts AS ja ON ja.account_id=ai.petty_cash_account_id
        LEFT JOIN account_titles AS atitles ON atitles.account_id=ai.petty_cash_account_id
        LEFT JOIN account_classes AS ac ON ac.`account_class_id`=atitles.`account_class_id`
        LEFT JOIN journal_info AS ji ON ji.journal_id=ja.`journal_id` AND ji.is_active=TRUE AND ji.is_deleted=FALSE

        WHERE ji.is_replenished=FALSE AND ji.date_txn <= :as_of_date  AND ji.department_id=:department_id) AS x 
        "), 
        array(
            'as_of_date' => $as_of_date,
            'department_id' => $department_id,
        ) );

        $unreplenished = DB::select( DB::raw("
            SELECT 
            SUM(ji.amount) AS unreplenished_expense
            
            FROM journal_info ji 
            WHERE ji.is_replenished=0
            AND ji.book_type='PCV'
            AND ji.is_active=TRUE
            AND ji.is_deleted=FALSE
            AND ji.date_txn <= :as_of_date  AND ji.department_id=:department_id
        
            "), 
            array(
                'as_of_date' => $as_of_date,
                'department_id' => $department_id
            ) );

            $data['unreplenished_amount'] = $unreplenished[0]->unreplenished_expense;
            $data['balance_amount'] = $balance[0]->balance;
        return  $data ;
    }

    public function get_totalsexcept($as_of_date = null,$department_id=null,$journal_id)
    {
        $balance = DB::select( DB::raw("
        SELECT
        (CASE WHEN x.`account_type_id` = 1 OR x.account_type_id=5 THEN
        IFNULL(((x.dr_amount) - (x.cr_amount)),0)
        ELSE
        IFNULL(((x.cr_amount) - (x.dr_amount)),0)
        END) as balance
        FROM
            (SELECT
            petty_cash_account_id,
            ja.journal_id,
            ac.account_type_id,
            SUM(ja.dr_amount) AS dr_amount,
            SUM(ja.cr_amount) AS cr_amount,
            ji.date_txn,
            ji.department_id
            
            FROM `account_integration` AS ai

        LEFT JOIN journal_accounts AS ja ON ja.account_id=ai.petty_cash_account_id
        LEFT JOIN account_titles AS atitles ON atitles.account_id=ai.petty_cash_account_id
        LEFT JOIN account_classes AS ac ON ac.`account_class_id`=atitles.`account_class_id`
        LEFT JOIN journal_info AS ji ON ji.journal_id=ja.`journal_id` AND ji.is_active=TRUE AND ji.is_deleted=FALSE

        WHERE ji.is_replenished=FALSE AND ji.date_txn <= :as_of_date  AND ji.department_id=:department_id 
        AND ji.journal_id != :journal_id
        ) AS x 
        "), 
        array(
            'as_of_date' => $as_of_date,
            'department_id' => $department_id,
            'journal_id' => $journal_id,
        ) );

            $data['balance_amount'] = $balance[0]->balance;
        return  $data ;
    }


    public function show($id)
    {
        return ( new Reference($this->GetPettyCashList($id,null,null)->get()[0]))
            ->response()
            ->setStatusCode(200);
        
    }
    public function pettycashaccounts() // USED IN SELECT2 OPTIONS
    {
        $accounttitlesoptions = AccountTitle::select(
            'account_titles.account_id',
            'account_titles.account_title',
            )
            ->leftJoin('account_classes', 'account_classes.account_class_id', '=', 'account_titles.account_class_id')
            ->where('account_titles.is_active',TRUE)
            ->where('account_titles.is_deleted',FALSE)
            ->where('account_classes.account_type_id','5')
            ->orderBy('account_titles.account_title','asc');

        return Reference::collection($accounttitlesoptions->get())
        ->response()
        ->setStatusCode(200);
    }

    public function create(Request $request)
    {
        Validator::make($request->all(),
            [
                'department_id' => 'required|not_in:0',
                'supplier_id' => 'required|not_in:0',
                'account_id' => 'required|not_in:0',
                'date_txn' => 'required|not_in:0',
                'amount' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'department_id' => 'Department',
                'supplier_id' => 'Supplier',
                'account_id' => 'Expense Account',
                'amount' => 'Expense Amount',
                'date_txn' => 'Transaction Date']
            )->validate();

        $pettycash = new JournalInfo();
        $ref_no_count = 1 + COUNT( JournalInfo::where('book_type','PCV')->get() );
        $pettycash->customer_id=0;
        $pettycash->ref_no= 'PCV-'.str_pad($ref_no_count, 5, "0", STR_PAD_LEFT);
        $pettycash->date_created = Carbon::now();
        $pettycash->supplier_id= $request->input('supplier_id');
        $pettycash->department_id = $request->input('department_id');
        $pettycash->book_type = 'PCV';
        $pettycash->date_txn = date("Y-m-d", strtotime($request->input('date_txn')));
        $pettycash->amount = $request->input('amount');
        $pettycash->remarks = $request->input('remarks');
        
        if($pettycash->save()){ // MAKE SURE JOURNAL IS SAVED FIRST
            $companysettings = GeneralConfiguration::select('petty_cash_account_id')->where('integration_id',1)->get()[0];

            $accounts = new JournalAccounts();
            $accounts->journal_id= $pettycash->journal_id;
            $accounts->account_id= $request->input('account_id');
            $accounts->dr_amount= $request->input('amount');
            $accounts->cr_amount= 0;
            $accounts->save();

            $accounts = new JournalAccounts();
            $accounts->journal_id= $pettycash->journal_id;
            $accounts->account_id= $companysettings->petty_cash_account_id;
            $accounts->dr_amount= 0;
            $accounts->cr_amount= $request->input('amount');
            $accounts->save();


            $gentxn = JournalInfo::findOrFail($pettycash->journal_id);
            $gentxn->txn_no = 'PCV-'.date('Ymd').'-'.$pettycash->journal_id;
            $gentxn->save();
        }

        return ( new Reference( $this->GetPettyCashList($pettycash->journal_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function update(Request $request,$id)
    {
        Validator::make($request->all(),
            [
                'department_id' => 'required|not_in:0',
                'supplier_id' => 'required|not_in:0',
                'account_id' => 'required|not_in:0',
                'date_txn' => 'required|not_in:0',
                'amount' => 'required|not_in:0',
            ],
            [
                'not_in' => 'The :attribute field is required.'
            ])
            ->setAttributeNames([
                'department_id' => 'Department',
                'supplier_id' => 'Supplier',
                'account_id' => 'Expense Account',
                'amount' => 'Expense Amount',
                'date_txn' => 'Transaction Date']
            )->validate();

        $pettycash = JournalInfo::findOrFail($id);
        $pettycash->date_modified = Carbon::now();
        $pettycash->supplier_id= $request->input('supplier_id');
        $pettycash->department_id = $request->input('department_id');
        $pettycash->date_txn = date("Y-m-d", strtotime($request->input('date_txn')));
        $pettycash->amount = $request->input('amount');
        $pettycash->remarks = $request->input('remarks');
        
        if($pettycash->save()){ // MAKE SURE JOURNAL IS SAVED FIRST
            $companysettings = GeneralConfiguration::select('petty_cash_account_id')->where('integration_id',1)->get()[0];

            $old_accounts = JournalAccounts::where('journal_id', $id);
            $old_accounts->delete();

            $accounts = new JournalAccounts();
            $accounts->journal_id= $pettycash->journal_id;
            $accounts->account_id= $request->input('account_id');
            $accounts->dr_amount= $request->input('amount');
            $accounts->cr_amount= 0;
            $accounts->save();

            $accounts = new JournalAccounts();
            $accounts->journal_id= $pettycash->journal_id;
            $accounts->account_id= $companysettings->petty_cash_account_id;
            $accounts->dr_amount= 0;
            $accounts->cr_amount= $request->input('amount');
            $accounts->save();


            $gentxn = JournalInfo::findOrFail($pettycash->journal_id);
            $gentxn->txn_no = 'PCV-'.date('Ymd').'-'.$pettycash->journal_id;
            $gentxn->save();
        }

        return ( new Reference( $this->GetPettyCashList($pettycash->journal_id)->get()[0] ))
                ->response()
                ->setStatusCode(201);
    }

    public function delete($id)
    {   
        $pcv = JournalInfo::findOrFail($id);
        $pcv->is_deleted = 1;
        $pcv->is_active = 0;
        $pcv->save();
        return ( new Reference( $pcv ) )
            ->response()
            ->setStatusCode(200);
    }

    public function replenish(Request $request)
    {   

        if($request->input('payment_method_id') == 2)
        {
            Validator::make($request->all(),
                [
                    'bank_id' => 'required|not_in:0',
                    'check_no' => 'required|not_in:0',
                    'check_date' => 'required|not_in:0',
                ],  ['not_in' => 'The :attribute field is required.']
            )->setAttributeNames([
                'bank_id' => 'Bank',
                'check_no'=>'Check Number',
                'check_date' => 'Check Date',
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
            'account_id_credit' => 'required|not_in:0',
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
            'account_id_credit' => 'Credit Entry Account']
        )->validate();

        $on_or_before = $request->input('on_or_before');
        $department_id = $request->input('department_id');

        $batch = new Batch();
        $batch->date_replenished = Carbon::now();
        $batch->save();
        $batchtxn = Batch::findOrFail($batch->batch_id);
        $batchtxn->batch_no = 'PCVB-'.date('Ymd').'-'.$batch->batch_id;
        $batchtxn->save();

        $unreplenished = DB::select( DB::raw("
            SELECT 
            SUM(ji.amount) AS unreplenished_expense
            
            FROM journal_info ji 
            WHERE ji.is_replenished=0
            AND ji.book_type='PCV'
            AND ji.is_active=TRUE
            AND ji.is_deleted=FALSE
            AND ji.date_txn <= :as_of_date  AND ji.department_id=:department_id
        
            "), 
            array(
                'as_of_date' => $on_or_before,
                'department_id' => $department_id
            ) );

        JournalInfo::where('date_txn', '<=', $on_or_before)
        ->where('department_id', '=', $department_id)
        ->where('is_active', '=', TRUE)
        ->where('is_deleted', '=', FALSE)
        ->where('book_type', '=', 'PCV')
        ->update(['is_replenished' => TRUE , 'batch_id' => $batch->batch_id ]);

        $particular=explode('-',$request->input('particular_id'));
        $transaction = new JournalInfo();
        $transaction->supplier_id=$particular[1];
        $transaction->department_id=$department_id;
        $transaction->remarks= "To Replenish Petty Cash on or before $on_or_before";
        $transaction->book_type='CDJ';
        $transaction->is_replenished=TRUE;

        $transaction->payment_method_id=$request->input('payment_method_id');
        if($request->input('payment_method_id')== 2){
            $transaction->check_no=$request->input('check_no');
            $transaction->check_date = date("Y-m-d", strtotime($request->input('check_date')));
        }

        $transaction->amount = $unreplenished[0]->unreplenished_expense;
        $transaction->date_txn = date("Y-m-d", strtotime(Carbon::now()));
        $transaction->date_created = Carbon::now();
        $transaction->save();

        $journal_id = $transaction->journal_id;
        $transactiontxn = JournalInfo::findOrFail($journal_id);
        $transactiontxn->txn_no = 'TXN-'.date('Ymd').'-'.$journal_id;
        $transactiontxn->save();

        $companysettings = GeneralConfiguration::select('petty_cash_account_id')->where('integration_id',1)->get()[0];

        $journalaccounts = new JournalAccounts();
        $journalaccounts->account_id = $companysettings->petty_cash_account_id;
        $journalaccounts ->journal_id = $journal_id;
        $journalaccounts ->dr_amount = $unreplenished[0]->unreplenished_expense;
        $journalaccounts ->cr_amount = 0;
        $journalaccounts->save();
        $journalaccounts = new JournalAccounts();
        $journalaccounts->account_id = $request->input('account_id_credit');
        $journalaccounts ->journal_id = $journal_id;
        $journalaccounts ->dr_amount = 0;
        $journalaccounts ->cr_amount = $unreplenished[0]->unreplenished_expense;
        $journalaccounts->save();

        return ( new Reference( $transaction ) )
            ->response()
            ->setStatusCode(200);

    }

    public function GetPettyCashList($id=null,$as_of_date=null,$department_id=null) // List only, without on hand
    {
        $pettycashlist = JournalInfo::select(
            'journal_info.txn_no',
            DB::raw('DATE_FORMAT(journal_info.date_txn,"%m/%d/%Y")as date_txn'),
            'journal_info.supplier_id',
            'journal_info.remarks',
            'journal_info.amount',
            'journal_info.ref_no',
            'journal_info.journal_id',
            'journal_info.department_id',
            'journal_accounts.account_id',
            'suppliers.supplier_name',
            'departments.department_name')
            ->leftJoin('suppliers', 'suppliers.supplier_id', '=', 'journal_info.supplier_id')
            ->leftJoin('journal_accounts', 'journal_accounts.journal_id', '=', 'journal_info.journal_id')
            ->leftJoin('account_titles', 'account_titles.account_id', '=', 'journal_accounts.account_id')
            ->leftJoin('account_classes', 'account_classes.account_class_id', '=', 'account_titles.account_class_id')
            ->leftJoin('departments', 'departments.department_id', '=', 'journal_info.department_id')
            ->where('journal_info.is_active',TRUE)
            ->where('journal_info.is_deleted',FALSE)
            ->where('journal_info.book_type','PCV')
            ->where('journal_info.is_replenished',FALSE)
            ->where('account_classes.account_type_id',5)
            ->orderBy('journal_info.journal_id','desc');

            if($id != null){
                $pettycashlist->where('journal_info.journal_id',$id);
            }
            if( $department_id != 0){
                    $pettycashlist->where('journal_info.department_id',$department_id);
            }
            if($as_of_date != null){
                $pettycashlist->whereDate('journal_info.date_txn', '<=', $as_of_date);
            }
        return $pettycashlist;
    }
}

<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reference;
use App\Models\Accounting\TempJournalAccounts;
use App\Models\Accounting\TempJournalInfo;
use App\Models\Transactions\PaymentInfo;
use DB;
use Carbon\Carbon;

class ArPaymentController extends Controller
{
    public function getSentPayment($from, $to)
    {
        $payments = PaymentInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_refchecktype', 'b_refchecktype.check_type_id', '=', 'b_payment_info.check_type_id')
                            ->where('is_canceled', 0)
                            ->orderBy('payment_id', 'desc');
        if($from != null && $to != null){
            $payments->whereRaw('DATE(payment_date) BETWEEN DATE("'.$from.'") AND DATE("'.$to.'")');
        }

        return Reference::collection($payments->get());
    }

    public function getJournalInfo($start_date, $end_date)
    {
        return DB::select('CALL get_payment_to_accounting_info("'.$start_date.'","'.$end_date.'")');
    }

    public function insertJournalInfo(Request $request)
    {
        $infos = $request->input('info');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $info_dataSet = [];
        foreach($infos as $info)
        {
            $journal_info = new TempJournalInfo;
            $journal_info->txn_no = $info['txn_no'];
            $journal_info->department_id = $info['department_id'];
            $journal_info->customer_id = $info['customer_id'];
            $journal_info->date_txn = Carbon::now();
            $journal_info->payment_method_id = $info['payment_method'];
            $journal_info->ref_no = $info['ref_no'];
            $journal_info->amount = $info['amount'];
            $journal_info->is_sales = $info['is_sales'];
            $journal_info->journal_id = $info['journal_id'];
            $journal_info->payment_id = $info['payment_id'];
            $journal_info->book_type_id = 1;

            if($journal_info->save())
            {
                $temp_journal_id = $journal_info->temp_journal_id;
                DB::select('CALL insert_payment_to_account_details('.$temp_journal_id.', "'.$start_date.'", "'.$end_date.'", '.$info['tenant_id'].', '.$info['payment_id'].')');
            }

            $payment = PaymentInfo::findOrFail($info['payment_id']);
            $payment->is_sent = 1;
            $payment->save();
            
        }
        return response()->json(['message' => 'Successfully sent to accounting.']);
    }
}

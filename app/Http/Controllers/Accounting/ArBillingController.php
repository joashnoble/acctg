<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reference;
use App\Models\Accounting\TempJournalAccounts;
use App\Models\Accounting\TempJournalInfo;
use App\Models\Transactions\BillingInfo;
use App\Models\References\BillingPeriod;
use DB;
use Carbon\Carbon;


class ArBillingController extends Controller
{
    public function getSentBilling($period_id)
    {
        $billings = BillingInfo::select('*', 'b_billing_info.is_sent')
                                ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_billing_info.tenant_id')
                                ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                                ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_info.month_id')
                                ->leftJoin('b_refbillingperiod', 'b_refbillingperiod.period_id', '=', 'b_billing_info.period_id')
                                ->where('b_billing_info.is_deleted', 0);
        if($period_id != 0){
            $billings->where('b_billing_info.period_id', $period_id);
        }

        return Reference::collection($billings->get());
    }

    public function getJournalInfo($period_id)
    {
        $infos = DB::select('CALL get_ar_to_accounting_info('.$period_id.')');
        
        foreach($infos as $info)
        {
            $journal_info = new TempJournalInfo;
            $journal_info->txn_no = $info->txn_no;
            $journal_info->department_id = $info->department_id;
            $journal_info->customer_id = $info->customer_id;
            $journal_info->date_txn = Carbon::now();
            $journal_info->payment_method_id = $info->payment_method;
            $journal_info->ref_no = $info->ref_no;
            $journal_info->amount = $info->amount;
            $journal_info->is_sales = $info->is_sales;
            $journal_info->journal_id = $info->journal_id;
            $journal_info->book_type_id = 0;

            if($journal_info->save())
            {
                $temp_journal_id = $journal_info->temp_journal_id;
                DB::select('CALL insert_ar_to_accounting_details('.$info->tenant_id.','.$temp_journal_id.', '.$period_id.')');
            }

            $billing = BillingInfo::findOrFail($info->billing_id);
            $billing->is_sent = 1;
            $billing->save();
        }
        return response()->json(['message' => 'Successfully sent to accounting.']);
    }

    public function insertJournalInfo(Request $request)
    {
        $infos = $request->input('info');
        $info_dataSet = [];

        
    }
}

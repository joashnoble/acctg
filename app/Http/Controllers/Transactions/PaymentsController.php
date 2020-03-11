<?php

namespace App\Http\Controllers\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions\PaymentInfo;
use App\Models\Transactions\PaymentDetails;
use App\Models\Transactions\ContractOtherFees;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($date_from = null, $date_to = null)
    {
        $payments = PaymentInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_refchecktype', 'b_refchecktype.check_type_id', '=', 'b_payment_info.check_type_id')
                            ->where('is_canceled', 0)->orderBy('payment_id', 'desc');
        if($date_from != null && $date_to != null){
            $payments->whereRaw('DATE(payment_date) BETWEEN DATE("'.$date_from.'") AND DATE("'.$date_to.'")');
        }
        return Reference::collection($payments->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        Validator::make($request->all(),
            [
                'reference_no' => 'required',
                'tenant_id' => 'required|not_in:0',
                'payment_type' => 'required',
                'check_no' => 'required_if:payment_type,1',
                'check_date' => 'required_if:payment_type,1',
            ],  ['not_in' => 'The :attribute field is required.', 'required_if' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'contract_id' => 'contract']
        )->validate();

        if($request->input('payment_type') == 1)
        {
            Validator::make($request->all(),
                [
                    'check_type_id' => 'required|not_in:0',
                ],  ['not_in' => 'The :attribute field is required.']
            )->setAttributeNames([
                'check_type_id' => 'check type']
            )->validate();
        }

        $payment_info = new PaymentInfo;
        $payment_details = new PaymentDetails;

        $payment_info->transaction_no = DB::raw("CreateTransactionNo()");
        $payment_info->reference_no = $request->input('reference_no');
        $payment_info->tenant_id = $request->input('tenant_id');
        $payment_info->payment_type = $request->input('payment_type');
        $payment_info->amount_paid = $request->input('amount');
        $payment_info->payment_date = date("Y-m-d h:i:s ", strtotime($request->input('payment_date')));
        $payment_info->check_type_id = $request->input('check_type_id');
        $payment_info->check_no = $request->input('check_no');
        $payment_info->check_date = date("Y-m-d", strtotime($request->input('check_date')));
        $payment_info->deposit_no = $request->input('deposit_no');
        $payment_info->deposit_date = date("Y-m-d", strtotime($request->input('deposit_date')));
        $payment_info->deposit_status = $request->input('deposit_status');
        // $payment_info->balance_paid = $request->input('balance_paid');
        // $payment_info->advance = $request->input('advance');
        // $payment_info->carried_advance = $request->input('carried_advance');
        $payment_info->used_advances = $request->input('used_advances');
        // $payment_info->wtax_amount = $request->input('wtax_amount');
        $payment_info->discount = $request->input('discount');
        $payment_info->remarks = $request->input('remarks');

        $payment_info->created_datetime = Carbon::now();
        $payment_info->created_by = Auth::user()->id;
        
        if($payment_info->save()){
            $payment_id = $payment_info->payment_id;
            $payment_details_dataSet = [];
            
            // $payment_details = $request->input('payment_details');
            // foreach($payment_details as $detail){
            //     $payment_details_dataSet[] = [
            //         'payment_id' => $payment_id,
            //         'billing_id' => $detail['billing_id'],
            //         'discount' => $detail['discount'],
            //         'amount_paid' => $detail['amount_paid'],
            //         'is_discounted' => $detail['is_discounted'],
            //         'wtax_amount' => $detail['wtax_amount']
            //     ];
            // }
            // DB::table('b_payment_details')->insert($payment_details_dataSet);
            
            if($payment_info->used_advances > 0)
            {
                $fee = new ContractOtherFees;
                $fee->tenant_id = $request->input('tenant_id');
                $fee->payment_id = $payment_info->payment_id;
                $fee->fee_type_id = 1;
                $fee->fee_debit = $request->input('used_advances');
                $fee->created_datetime = Carbon::now();
                $fee->created_by = Auth::user()->id;
                $fee->save();
            }
        }
        
        $data = PaymentInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_refchecktype', 'b_refchecktype.check_type_id', '=', 'b_payment_info.check_type_id')
                            ->findOrFail($payment_info->payment_id);

        return ( new Reference( $data ) )
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment = PaymentInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_refchecktype', 'b_refchecktype.check_type_id', '=', 'b_payment_info.check_type_id')
                            ->findOrFail($id);


        return ( new Reference( $payment ) )
                ->response()
                ->setStatusCode(200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage for deleting.
     * preventing force delete rather update the is_deleted = true
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $payment = PaymentInfo::findOrFail($id);
        $payment->is_canceled = 1;
        $payment->canceled_datetime = Carbon::now();
        $payment->canceled_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        if($payment->save())
        {
            ContractOtherFees::where('payment_id', $id)->delete();
        }


        return ( new Reference( $payment ) )
            ->response()
            ->setStatusCode(200);
    }

    public function getPayments($month_id, $app_year, $tenant_id)
    {
        $payments = PaymentInfo::select(
                                'transaction_no',
                                'reference_no',
                                'payment_type',
                                'amount_paid as payment',
                                DB::raw('"Payment" as trans_type'),
                                'payment_date',
                                'ct.check_type_desc',
                                'check_no',
                                'check_date'
        )
                                ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')
                                ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$app_year.'-'.$month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$app_year.'-'.$month_id.'-20")')
                                ->where('is_canceled', 0)
                                ->where('tenant_id', $tenant_id)
                                ->where('amount_paid', '>', 0);
        
        // $discounts = PaymentInfo::select(
        //                         'transaction_no',
        //                         'reference_no',
        //                         'payment_type',
        //                         'discount as payment',
        //                         DB::raw('"Discount" as trans_type'),
        //                         'payment_date',
        //                         'ct.check_type_desc',
        //                         'check_no',
        //                         'check_date'
        // )
        //                         ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')
        //                         ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$app_year.'-'.$month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$app_year.'-'.$month_id.'-20")')
        //                         ->where('is_canceled', 0)
        //                         ->where('tenant_id', $tenant_id)
        //                         ->where('discount', '>', 0);

        $result = $payments->get();

        return ( new Reference( $result ) )
            ->response()
            ->setStatusCode(200);
    }

    public function latePayment($month_id, $app_year, $tenant_id, $department_id)
    {
        return DB::select("select GetLatePayment(".$month_id.", ".$app_year.", ".$tenant_id.", ".$department_id.") as latePayment");
    }

    public function getPaymentsForInterest($month_id, $app_year, $tenant_id)
    {
        $payments = PaymentInfo::select(
                                DB::raw('IFNULL(SUM(amount_paid), 0) + IFNULL(SUM(discount), 0) as payment')
                    )
                                ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$app_year.'-'.$month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$app_year.'-'.$month_id.'-20")')
                                ->where('is_canceled', 0)
                                ->where('tenant_id', $tenant_id)
                                ->get();
        return ( new Reference( $payments ) )
                    ->response()
                    ->setStatusCode(200);
    }

    public function getAdvance($tenant_id)
    {
        // $advance['advance'] = PaymentInfo::where('tenant_id', $tenant_id)
        //                         ->where('is_canceled', 0)
        //                         ->orderBy('payment_date', 'desc')
        //                         ->limit(1)
        //                         ->get();

        $advance['contract_advance'] = DB::select("select GetContractAdvances(".$tenant_id.") as contract_advance");


        return ( new Reference( $advance ) )
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkIfUsed($id)
    {
        $exists = 'false';

        // if(BillingPeriod::leftJoin('b_billing_info', 'b_billing_info.period_id', '=', 'b_refbillingperiod.period_id')
        //     ->where('b_billing_info.billing_id', '=', $id)
        //     ->where('b_billing_info.is_deleted', 0)
        //     ->where('b_refbillingperiod.is_closed', 1)
        //     ->exists()) {
        //     $exists = 'true';
        // }
        
        return $exists;
    }

    function get_numeric_value($str){
        return (float)str_replace(',','',$str);
    }

    function convertNumberToWord($num = false)
    {
        $num = str_replace(array(',', ' '), '' , trim($num));
        if(! $num) {
            return false;
        }
        $num = (int) $num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
            'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
            'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? '' . $list1[$hundreds] . ' hundred' . ( $hundreds == 1 ? '' : '' ) . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ( $tens < 20 ) {
                $tens = ($tens ? '' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int)($tens / 10);
                $tens = '' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = '' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? '' . $list3[$levels] . '' : '' );
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }


    function convertDecimalToWords($num){
        // $num=$this->get_numeric_value($num);  //returned an error ex. .70 returns as and seven centavos only

        if(substr_count($num,".")>0){ //this a decimal number
            $arr=explode(".",$num);
            if($arr[1] > 0){
                    return $this->convertNumberToWord($arr[0])."and ".$this->convertNumberToWord($arr[1])."centavos only";
                } else{
                    return $this->convertNumberToWord($num)." pesos only";
                }
        }else{
            return $this->convertNumberToWord($num)." pesos only";
        }
    }
}

<?php

namespace App\Http\Controllers\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions\ChargeSlip;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;


class ChargeSlipsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($app_year = null, $month_id = null)
    {
        $charge_slips = ChargeSlip::select(
                                'b_charge_slip.*',
                                'b_tenants.trade_name',
                                'b_refcharges.charge_desc'
                            )
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_charge_slip.tenant_id')
                            ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_charge_slip.charge_id')
                            ->where('b_charge_slip.is_deleted',0);
        
        if($app_year != null && $month_id != null)
        {
            $charge_slips->where('b_charge_slip.app_year', $app_year)->where('b_charge_slip.month_id', $month_id);
        }

        return Reference::collection($charge_slips->orderBy('charge_slip_id', 'DESC')
        ->get());
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
                'tenant_id' => 'required|not_in:0',
                'charge_slip_type' => 'required',
                'charge_id' => 'required|not_in:0',
                'charge_slip_amount' => 'required|not_in:0',
                'charge_slip_datetime' => 'required',
                'charge_slip_category_type' => 'required|not_in:0',
                'charge_slip_category_others_desc' => 'required_if:charge_slip_category_type,3',
                'charge_slip_gravity_type' => 'required|not_in:0',
                'charge_slip_gravity_others_desc' => 'required_if:charge_slip_gravity_type,3',
            ],  ['not_in' => 'The :attribute field is required.', 'required_if' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'charge_slip_amount' => 'amount',
            'charge_id' => 'charge',
            'charge_slip_datetime' => 'datetime',
            'charge_slip_category_type' => 'category',
            'charge_slip_category_others_desc' => 'other category desc',
            'charge_slip_gravity_type' => 'gravity',
            'charge_slip_gravity_others_desc' => 'offense desc',]
        )->validate();

        $charge_slip = new ChargeSlip();
        $charge_slip->charge_slip_no = DB::select("select CreateChargeSlipNo() as charge_slip_no")[0]->charge_slip_no;
        $charge_slip->tenant_id = $request->input('tenant_id');
        $charge_slip->charge_slip_type = $request->input('charge_slip_type');
        $charge_slip->charge_id = $request->input('charge_id');
        $charge_slip->app_year = $request->input('app_year');
        $charge_slip->month_id = $request->input('month_id');
        $charge_slip->rate = $request->input('rate');
        $charge_slip->reading = $request->input('reading');
        $charge_slip->charge_slip_amount = $request->input('charge_slip_amount');
        $charge_slip->charge_slip_datetime = $request->input('charge_slip_datetime');
        $charge_slip->charge_ref_wp_no = $request->input('charge_ref_wp_no');
        $charge_slip->charge_slip_nature = $request->input('charge_slip_nature');
        $charge_slip->charge_slip_category_type = $request->input('charge_slip_category_type');
        $charge_slip->charge_slip_category_others_desc = $request->input('charge_slip_category_others_desc');
        $charge_slip->charge_slip_gravity_type = $request->input('charge_slip_gravity_type');
        $charge_slip->charge_slip_gravity_others_desc = $request->input('charge_slip_gravity_others_desc');
        $charge_slip->created_datetime = Carbon::now();
        $charge_slip->created_by = Auth::user()->id;

        $charge_slip->save();

        return $this->show($charge_slip->charge_slip_id);
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
        $charge_slip = ChargeSlip::select(
                                    'b_charge_slip.*',
                                    'b_tenants.trade_name',
                                    'b_refcharges.charge_desc'
                                )
                                ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_charge_slip.tenant_id')
                                ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_charge_slip.charge_id')
                                ->findOrFail($id);

        return ( new Reference( $charge_slip ) )
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
        Validator::make($request->all(),
            [
                'tenant_id' => 'required|not_in:0',
                'charge_slip_type' => 'required',
                'charge_id' => 'required|not_in:0',
                'charge_slip_amount' => 'required|not_in:0',
                'charge_slip_datetime' => 'required',
                'charge_slip_category_type' => 'required|not_in:0',
                'charge_slip_category_others_desc' => 'required_if:charge_slip_category_type,3',
                'charge_slip_gravity_type' => 'required|not_in:0',
                'charge_slip_gravity_others_desc' => 'required_if:charge_slip_gravity_type,3',
            ],  ['not_in' => 'The :attribute field is required.', 'required_if' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'charge_slip_amount' => 'amount',
            'charge_id' => 'charge',
            'charge_slip_datetime' => 'datetime',
            'charge_slip_category_type' => 'category',
            'charge_slip_category_others_desc' => 'other category desc',
            'charge_slip_gravity_type' => 'gravity',
            'charge_slip_gravity_others_desc' => 'offense desc',]
        )->validate();

        $charge_slip = ChargeSlip::findOrFail($id);
        $charge_slip->tenant_id = $request->input('tenant_id');
        $charge_slip->charge_slip_type = $request->input('charge_slip_type');
        $charge_slip->charge_id = $request->input('charge_id');
        $charge_slip->app_year = $request->input('app_year');
        $charge_slip->month_id = $request->input('month_id');
        $charge_slip->rate = $request->input('rate');
        $charge_slip->reading = $request->input('reading');
        $charge_slip->charge_slip_amount = $request->input('charge_slip_amount');
        $charge_slip->charge_slip_datetime = $request->input('charge_slip_datetime');
        $charge_slip->charge_ref_wp_no = $request->input('charge_ref_wp_no');
        $charge_slip->charge_slip_nature = $request->input('charge_slip_nature');
        $charge_slip->charge_slip_category_type = $request->input('charge_slip_category_type');
        $charge_slip->charge_slip_category_others_desc = $request->input('charge_slip_category_others_desc');
        $charge_slip->charge_slip_gravity_type = $request->input('charge_slip_gravity_type');
        $charge_slip->charge_slip_gravity_others_desc = $request->input('charge_slip_gravity_others_desc');
        $charge_slip->modified_datetime = Carbon::now();
        $charge_slip->modified_by = Auth::user()->id;

        $charge_slip->save();

        return $this->show($id);
    }

    public function approval($id)
    {
        $charge_slip = ChargeSlip::findOrFail($id);
        $charge_slip->status = 1;
        $charge_slip->approved_by = Auth::user()->id;
        $charge_slip->approved_datetime = Carbon::now();
        $charge_slip->save();

        return $this->show($id);
    }

    public function disapproval($id)
    {
        $charge_slip = ChargeSlip::findOrFail($id);
        $charge_slip->status = 2;
        $charge_slip->disapproved_by = Auth::user()->id;
        $charge_slip->disapproved_datetime = Carbon::now();
        $charge_slip->save();

        return $this->show($id);
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
        $charge_slip = ChargeSlip::findOrFail($id);
        $charge_slip->is_deleted = 1;
        $charge_slip->deleted_datetime = Carbon::now();
        $charge_slip->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $charge_slip->save();

        return ( new Reference( $charge_slip ) )
            ->response()
            ->setStatusCode(200);
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
}

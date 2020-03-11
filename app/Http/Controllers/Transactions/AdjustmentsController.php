<?php

namespace App\Http\Controllers\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions\Adjustment;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class AdjustmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($period_id = null)
    {
        $adjustments = Adjustment::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_adjustments.tenant_id')
                            ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_adjustments.month_id')
                            ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_adjustments.charge_id')
                            ->where('b_adjustments.is_deleted', 0);
        if($period_id != null){
            $adjustments->where('b_adjustments.period_id', $period_id);
        }
        return Reference::collection($adjustments->orderBy('adjustment_id', 'desc')->get());
    }

    public function ApprovedAdjustments($tenant_id, $period_id)
    {
        $adjustments = Adjustment::leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_adjustments.charge_id')
                            ->where('b_adjustments.is_deleted', 0)
                            ->where('b_adjustments.period_id', $period_id)
                            ->where('b_adjustments.tenant_id', $tenant_id)
                            ->where('b_adjustments.is_approved', 1)
                            ->get();

        return Reference::collection($adjustments);
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
                'charge_id' => 'required|not_in:0',
                'amount' => 'required|not_in:0',
                'adjustment_type' => 'required',
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'charge_id' => 'charge']
        )->validate();

        $adjustment = new Adjustment;
        $adjustment->adjustment_no = DB::select("select CreateAdjustmentNo() as adjustment_no")[0]->adjustment_no;
        $adjustment->tenant_id = $request->input('tenant_id');
        $adjustment->charge_id = $request->input('charge_id');
        $adjustment->period_id = $request->input('period_id');
        $adjustment->month_id = $request->input('month_id');
        $adjustment->app_year = $request->input('app_year');
        $adjustment->adjustment_type = $request->input('adjustment_type');
        $adjustment->adjustment_rate = $request->input('adjustment_rate');
        $adjustment->adjustment_reading = $request->input('adjustment_reading');
        $adjustment->amount = $request->input('amount');
        $adjustment->notes = $request->input('notes');

        $adjustment->created_datetime = Carbon::now();
        $adjustment->created_by = Auth::user()->id;

        $adjustment->save();

        $data = Adjustment::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_adjustments.tenant_id')
                ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_adjustments.month_id')
                ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_adjustments.charge_id')
                ->findOrFail($adjustment->adjustment_id);

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
        $adjustment = Adjustment::findOrFail($id);

        return ( new Reference( $adjustment ) )
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
                'charge_id' => 'required|not_in:0',
                'amount' => 'required|not_in:0',
                'adjustment_type' => 'required',
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'charge_id' => 'charge']
        )->validate();
        
        $adjustment = Adjustment::findOrFail($id);
        $adjustment->tenant_id = $request->input('tenant_id');
        $adjustment->charge_id = $request->input('charge_id');
        $adjustment->period_id = $request->input('period_id');
        $adjustment->month_id = $request->input('month_id');
        $adjustment->app_year = $request->input('app_year');
        $adjustment->adjustment_type = $request->input('adjustment_type');
        $adjustment->adjustment_rate = $request->input('adjustment_rate');
        $adjustment->adjustment_reading = $request->input('adjustment_reading');
        $adjustment->amount = $request->input('amount');
        $adjustment->notes = $request->input('notes');

        $adjustment->modified_datetime = Carbon::now();
        $adjustment->modified_by = Auth::user()->id;

        $adjustment->save();

        $data = Adjustment::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_adjustments.tenant_id')
                ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_adjustments.month_id')
                ->findOrFail($adjustment->adjustment_id);

        return ( new Reference( $data ) )
                ->response()
                ->setStatusCode(201);
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
        $adjustment = Adjustment::findOrFail($id);
        $adjustment->is_deleted = 1;
        $adjustment->deleted_datetime = Carbon::now();
        $adjustment->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $adjustment->save();

        return ( new Reference( $adjustment ) )
            ->response()
            ->setStatusCode(200);
    }

    public function getAdjustments($month_id, $app_year, $tenant_id)
    {
        $adjustments = Adjustment::select(
                                'adjustment_no',
                                'amount',
                                DB::raw('IF(adjustment_type = 0, "IN", "OUT") as adjustment_type')
        )
                                ->where('month_id', $month_id)
                                ->where('app_year', $app_year)
                                ->where('tenant_id', $tenant_id)
                                ->where('is_deleted', 0)
                                ->get();

        return ( new Reference( $adjustments ) )
            ->response()
            ->setStatusCode(200);
    }

    public function getTotalAdjustment($tenant_id, $month_id, $app_year)
    {
        $adjustments = Adjustment::select(
                                DB::raw('IFNULL(SUM(a.amount),0) as adjustment_in'),
                                DB::raw('IFNULL(SUM(b.amount),0) as adjustment_out')
        )
                                ->leftJoin(DB::raw('(SELECT 
                                                        IFNULL(amount,0) as amount,
                                                        adjustment_id
                                                    FROM b_adjustments 
                                                    WHERE adjustment_type = 0) a'), 
                                            function($join) {
                                                $join->on('a.adjustment_id', '=', 'b_adjustments.adjustment_id');
                                })
                                ->leftJoin(DB::raw('(SELECT 
                                                        IFNULL(amount,0) as amount,
                                                        adjustment_id
                                                    FROM b_adjustments 
                                                    WHERE adjustment_type = 1) b'), 
                                            function($join) {
                                                $join->on('b.adjustment_id', '=', 'b_adjustments.adjustment_id');
                                })
                                ->where('b_adjustments.month_id', $month_id)
                                ->where('b_adjustments.app_year', $app_year)
                                ->where('b_adjustments.tenant_id', $tenant_id)
                                ->where('b_adjustments.is_deleted', 0)
                                ->get();

        return ( new Reference( $adjustments ) )
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
}

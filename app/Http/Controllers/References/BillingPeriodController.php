<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\BillingPeriod;
use App\Models\Transactions\BillingInfo;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class BillingPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $periods = BillingPeriod::join('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                    ->where('is_deleted', 0)->orderBy('period_id', 'desc')->get();
        return Reference::collection($periods);
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
            'period_start_date' => 'required',
            'period_end_date' => 'required',
            'month_id' => 'required',
            'app_year' => 'required',
            'period_due_date' => 'required'
        ]
        )->setAttributeNames([
            'app_year' => 'applicable year',
            'month_id' => 'applicable month'
        ]
        )->validate();

        $period = new BillingPeriod();
        $period->period_start_date = date("Y-m-d",strtotime($request->input('period_start_date')));
        $period->period_end_date = date("Y-m-d",strtotime($request->input('period_end_date')));
        $period->month_id = $request->input('month_id');
        $period->app_year = date("Y",strtotime($request->input('app_year')));
        $period->period_due_date = date("Y-m-d",strtotime($request->input('period_due_date')));
        $period->created_datetime = Carbon::now();
        $period->created_by = Auth::user()->id;

        $period->save();
        
        $data = BillingPeriod::join('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                            ->findOrFail($period->period_id);
        //return json based from the resource data
        return ( new Reference( $data ))
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
        //$period = BillingPeriod::select('*',DB::raw("CONCAT(app_year,'-01','-01') as app_year"))->findOrFail($id);

        $period = BillingPeriod::findOrFail($id);

        return ( new Reference( $period ) )
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
        $period = BillingPeriod::findOrFail($id);

        Validator::make($request->all(),
        [
            'period_start_date' => 'required',
            'period_end_date' => 'required',
            'month_id' => 'required',
            'app_year' => 'required',
            'period_due_date' => 'required'
        ]
        )->validate();

        
        $period->period_start_date = date("Y-m-d",strtotime($request->input('period_start_date')));
        $period->period_end_date = date("Y-m-d",strtotime($request->input('period_end_date')));
        $period->month_id = $request->input('month_id');
        $period->app_year = date("Y",strtotime($request->input('app_year')));
        $period->period_due_date = date("Y-m-d",strtotime($request->input('period_due_date')));
        $period->modified_datetime = Carbon::now();
        $period->modified_by = Auth::user()->id;

        $period->save();
        
        $data = BillingPeriod::join('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                            ->findOrFail($period->period_id);
        //return json based from the resource data
        return ( new Reference( $data ))
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
        $period = BillingPeriod::findOrFail($id);
        $period->is_deleted = 1;
        $period->deleted_datetime = Carbon::now();
        $period->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $period->save();

        return ( new Reference( $period ) )
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

        if(BillingInfo::where('period_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        return $exists;
    }
}

<?php

namespace App\Http\Controllers\References;

use Illuminate\Http\Request;
use App\Models\Transactions\PaymentInfo;
use App\Models\References\Tenants;
use App\Models\Transactions\ContractInfo;
use App\Models\Transactions\Adjustment;
use App\Http\Controllers\Controller;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Auth;
use DB;
use Session;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($payment_type, Request $request)
    {   
        $tenants = Tenants::select(DB::raw('count(*) as no_of_tenants'))
                            ->where('is_deleted', 0)
                            ->get();

        $contracts = ContractInfo::select(DB::raw('count(*) as no_of_contracts'))
                            ->where('is_deleted', 0)
                            ->get();

        $from = Carbon::now();
        $to = Carbon::now()->addDays(45);
        $expiring_contracts = ContractInfo::select(DB::raw('count(*) as no_of_expiring_contracts'))
                            ->where('is_deleted', 0)
                            ->whereBetween('termination_date', [$from, $to])
                            ->get();

        $new_contracts = ContractInfo::select(DB::raw('count(*) as no_of_new_contracts'))
                            ->where('is_deleted', 0)
                            ->where('is_renewal', 0)
                            ->where('renewed', 0)
                            ->get();

        $pending_contracts = ContractInfo::select(
                                        'contract_no',
                                        'trade_name',
                                        'department_desc',
                                        'location_desc'
        )
                                        ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                                        ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                                        ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                                        ->where('b_contract_info.is_deleted', 0)
                                        ->where('b_contract_info.status', 0)
                                        ->get();
        
        $pending_adjustments = Adjustment::select(
                                        'adjustment_no',
                                        'trade_name',
                                        'charge_desc',
                                        DB::raw('IF(adjustment_type=0, "IN", "OUT") as adjustment_type'),
                                        'amount'
        )
                                        ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_adjustments.tenant_id')
                                        ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_adjustments.charge_id')
                                        ->where('b_adjustments.is_deleted', 0)
                                        ->where('b_adjustments.is_approved', 0)
                                        ->get();
        
        $dashboard['payment_line'] = $this->getPaymentLine($payment_type, true);
        $dashboard['tenants'] = Reference::collection($tenants);
        $dashboard['contracts'] = Reference::collection($contracts);
        $dashboard['expiring_contracts'] = Reference::collection($expiring_contracts);
        $dashboard['new_contracts'] = Reference::collection($new_contracts);
        $dashboard['pending_contracts'] = Reference::collection($pending_contracts);
        $dashboard['pending_adjustments'] = Reference::collection($pending_adjustments);

        return $dashboard;
    }

    public function getPaymentLine($payment_type, $is_string = true)
    {
        $cash = '';
        for($i=1; $i <= 12; $i++)
        {
            $cash = $cash."SELECT
                                IFNULL(SUM(amount_paid), 0) as amount
                            FROM b_payment_info 
                            WHERE MONTH(payment_date) = $i AND YEAR(payment_date) = YEAR(NOW()) AND payment_type = 0 AND is_canceled = 0";
            if($i != 12)
            {
                $cash = $cash." UNION ALL ";
            }
        }

        $check = '';
        for($i=1; $i <= 12; $i++)
        {
            $check = $check."SELECT
                                IFNULL(SUM(amount_paid), 0) as amount
                            FROM b_payment_info 
                            WHERE MONTH(payment_date) = $i AND YEAR(payment_date) = YEAR(NOW()) AND payment_type = 1 AND is_canceled = 0";
            if($i != 12)
            {
                $check = $check." UNION ALL ";
            }
        }

        $online = '';
        for($i=1; $i <= 12; $i++)
        {
            $online = $online."SELECT
                                IFNULL(SUM(amount_paid), 0) as amount
                            FROM b_payment_info 
                            WHERE MONTH(payment_date) = $i AND YEAR(payment_date) = YEAR(NOW()) AND payment_type = 2 AND is_canceled = 0";
            if($i != 12)
            {
                $online = $online." UNION ALL ";
            }
        }


        $data['cash'] = DB::select($cash);
        $data['check'] = DB::select($check);
        $data['online'] = DB::select($online);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\References\BillingPeriod;
use App\Models\Transactions\BillingInfo;
use App\Models\Transactions\ContractInfo;
use App\Models\Transactions\ContractSchedule;
use App\Models\Transactions\ContractUtilCharges;
use App\Models\Transactions\ContractMiscCharges;
use App\Models\Transactions\ContractOthrCharges;
use App\Models\Transactions\ContractOtherFees;
use App\Models\Transactions\BillingSchedule;
use App\Models\Transactions\BillingMiscCharges;
use App\Models\Transactions\BillingOthrCharges;
use App\Models\Transactions\BillingUtilCharges;
use App\Models\Transactions\BillingAdjustments;
use App\Models\Utilities\CompanySettings;
use App\Models\Transactions\ChargeSlip;
use App\Models\Transactions\Adjustment;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class BillingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($period_id = null, $department_id = null)
    {
        $billings = BillingInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_billing_info.tenant_id')
                            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                            ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_info.month_id')
                            ->where('b_billing_info.is_deleted', 0);     
        if($period_id != null){
            $billings->where('b_billing_info.period_id', $period_id);
        }
        if($department_id != null && $department_id != 0){
            $billings->where('b_contract_info.department_id', $department_id);
        }
        return Reference::collection($billings->get());
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
                'contract_id' => 'required|not_in:0',
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'contract_id' => 'contract']
        )->validate();

        $billing_info = new BillingInfo;

        $billing_info->billing_no = DB::raw("CreateBillingNo()");
        $billing_info->period_id = $request->input('period_id');
        $billing_info->tenant_id = $request->input('tenant_id');
        $billing_info->contract_id = $request->input('contract_id');
        $billing_info->month_id = $request->input('month_id');
        $billing_info->app_year = $request->input('app_year');
        $billing_info->due_date = date("Y-m-d", strtotime($request->input('due_date')));
        $billing_info->total_discounted_rent = $request->input('total_discounted_rent');
        $billing_info->total_fixed_rent = $request->input('total_fixed_rent');
        $billing_info->total_util_charges = $request->input('total_util_charges');
        $billing_info->total_misc_charges = $request->input('total_misc_charges');
        $billing_info->total_othr_charges = $request->input('total_othr_charges');
        $billing_info->sub_total = $request->input('sub_total');
        $billing_info->vatable_amount = $request->input('vatable_amount');
        $billing_info->discounted_vatable_amount = $request->input('discounted_vatable_amount');
        $billing_info->vat_percent = $request->input('vat_percent');
        $billing_info->total_vat = $request->input('total_vat');
        $billing_info->interested_amount = $request->input('interested_amount');
        $billing_info->interest_percent = $request->input('interest_percent');
        $billing_info->interest_total = $request->input('interest_total');
        $billing_info->penaltied_amount = $request->input('penaltied_amount');
        $billing_info->penalty_percent = $request->input('penalty_percent');
        $billing_info->penalty_total = $request->input('penalty_total');
        $billing_info->discounted_total_amount_due = $request->input('discounted_total_amount_due');
        $billing_info->total_amount_due = $request->input('total_amount_due');
        $billing_info->total_adjusted_in = $request->input('total_adjusted_in');
        $billing_info->total_adjusted_out = $request->input('total_adjusted_out');
        $billing_info->wtax_amount = $request->input('wtax_amount');
        $billing_info->wtax_percent = $request->input('wtax_percent');
        $billing_info->electric_wtax_amount = $request->input('electric_wtax_amount');
        $billing_info->electric_wtax_percent = $request->input('electric_wtax_percent');

        $billing_info->created_datetime = Carbon::now();
        $billing_info->created_by = Auth::user()->id;

        if($billing_info->save()){
            $billing_id = $billing_info->billing_id;
            $schedules_dataSet = [];
            $utilities_dataSet = [];
            $miscs_dataSet = [];
            $others_dataSet = [];
            $adjustments_dataSet = [];
            
            $schedules = $request->input('schedules');
            foreach($schedules as $schedule){
                $schedules_dataSet[] = [
                    'billing_id' => $billing_id,
                    'month_id' => $schedule['month_id'],
                    'app_year' => $schedule['app_year'],
                    'discounted_line_total' => $schedule['discounted_amount_due'],
                    'line_total' => $schedule['amount_due'],
                    'is_vatted' => $schedule['is_vatted'],
                    'billing_schedule_notes' => $schedule['contract_schedule_notes']
                ];
            }

            $utilities = $request->input('utilities');
            foreach($utilities as $utility){
                $utilities_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $utility['charge_id'],
                    'billing_util_rate' => $utility['contract_rate'],
                    'billing_util_reading' => $utility['contract_default_reading'],
                    'billing_util_line_total' => $utility['contract_line_total'],
                    'billing_util_is_vatted' => $utility['contract_is_vatted'],
                    'billing_util_notes' => $utility['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $miscs = $request->input('miscellaneous');
            foreach($miscs as $misc){
                $miscs_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $misc['charge_id'],
                    'billing_misc_rate' => $misc['contract_rate'],
                    'billing_misc_reading' => $misc['contract_default_reading'],
                    'billing_misc_line_total' => $misc['contract_line_total'],
                    'billing_misc_is_vatted' => $misc['contract_is_vatted'],
                    'billing_misc_notes' => $misc['contract_notes'],
                    'sort_key' => $misc['sort_key']
                ];
            }

            $others = $request->input('other');
            foreach($others as $other){
                $others_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $other['charge_id'],
                    'billing_othr_rate' => $other['contract_rate'],
                    'billing_othr_reading' => $other['contract_default_reading'],
                    'billing_othr_line_total' => $other['contract_line_total'],
                    'billing_othr_is_vatted' => $other['contract_is_vatted'],
                    'billing_othr_notes' => $other['contract_notes'],
                    'sort_key' => $other['sort_key']
                ];
            }

            $adjustments = $request->input('adjustments');
            foreach($adjustments as $adjustment){
                $adjustments_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $adjustment['charge_id'],
                    'billing_adjustment_rate' => $adjustment['contract_rate'],
                    'billing_adjustment_reading' => $adjustment['contract_default_reading'],
                    'billing_adjustment_line_total' => $adjustment['contract_line_total'],
                    'billing_adjustment_is_vatted' => $adjustment['contract_is_vatted'],
                    'billing_adjustment_type' => $adjustment['adjustment_type'],
                    'billing_adjustment_notes' => $adjustment['contract_notes'],
                    'sort_key' => $adjustment['sort_key']
                ];
            }

            DB::table('b_billing_schedule')->insert($schedules_dataSet);
            DB::table('b_billing_util_charges')->insert($utilities_dataSet);
            DB::table('b_billing_misc_charges')->insert($miscs_dataSet);
            DB::table('b_billing_othr_charges')->insert($others_dataSet);
            DB::table('b_billing_adjustments')->insert($adjustments_dataSet);
        }
        $data = BillingInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_billing_info.tenant_id')
                            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                            ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_info.month_id')
                            ->findOrFail($billing_info->billing_id);
        return ( new Reference( $data ) )
                ->response()
                ->setStatusCode(201);
    }

    public function BatchCreate($period_id)
    {
        $electric_charge_id = CompanySettings::findOrFail(1)->electric_charge_id;
        $period = BillingPeriod::findOrFail($period_id);
        $contracts = ContractSchedule::select(
                                        'ci.*',
                                        't.vat_percent',
                                        't.is_withhold_electricity'
                                    )
                                    ->leftJoin('b_contract_info as ci', 'ci.contract_id', '=', 'b_contract_schedule.contract_id')
                                    ->leftJoin('b_refdepartments as rd', 'rd.department_id', '=', 'ci.department_id')
                                    ->leftJoin('b_tenants as t', 't.tenant_id', '=', 'ci.tenant_id')
                                    ->where('app_year', $period->app_year)
                                    ->where('month_id', $period->month_id)
                                    ->where('ci.is_deleted', 0)
                                    ->where('ci.is_active', 1)
                                    ->where('ci.status', 1)
                                    ->whereRaw('ci.contract_id not in (SELECT contract_id FROM b_billing_info WHERE period_id = '.$period_id.' AND is_deleted = 0)')
                                    ->get();

        foreach($contracts as $contract)
        {
            DB::statement(DB::raw('set @row=0'));
            $schedules = ContractSchedule::select(
                            'b_contract_schedule.*',
                            'b_refmonths.*',
                            DB::raw("@row := @row + 1 as count")
                        )
                        ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_contract_schedule.month_id')
                        ->where('app_year', $period->app_year)
                        ->where('b_contract_schedule.month_id', $period->month_id)
                        ->where('contract_id', $contract->contract_id)
                        ->get();

            $util_charges = ContractUtilCharges::select(
                            'contract_util_id',
                            'contract_id',
                            'contract_util_rate as contract_rate',
                            'contract_util_default_reading as contract_default_reading',
                            'contract_util_is_vatted as contract_is_vatted',
                            DB::raw('contract_util_rate * contract_util_default_reading as total'),
                            'contract_util_notes as contract_notes',
                            'sort_key',
                            'b_refcharges.charge_id',
                            'b_refcharges.charge_desc'
                        )
                        ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_util_charges.charge_id')
                        ->where('contract_id', $contract->contract_id)->get();

            $misc_charges = ContractMiscCharges::select(
                            'contract_misc_id',
                            'contract_id',
                            'contract_misc_rate as contract_rate',
                            'contract_misc_default_reading as contract_default_reading',
                            'contract_misc_is_vatted as contract_is_vatted',
                            DB::raw('contract_misc_rate * contract_misc_default_reading as total'),
                            'contract_misc_notes as contract_notes',
                            'sort_key',
                            'b_refcharges.charge_id',
                            'b_refcharges.charge_desc'
                        )
                        ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_misc_charges.charge_id')
                        ->where('contract_id', $contract->contract_id)->get();

            $other = ContractOthrCharges::select(
                            'contract_othr_id',
                            'contract_id',
                            'contract_othr_rate as contract_rate',
                            'contract_othr_default_reading as contract_default_reading',
                            'contract_othr_is_vatted as contract_is_vatted',
                            DB::raw('contract_othr_rate * contract_othr_default_reading as total'),
                            'contract_othr_notes as contract_notes',
                            'sort_key',
                            'b_refcharges.charge_id',
                            'b_refcharges.charge_desc'
                        )
                        ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_othr_charges.charge_id')
                        ->where('contract_id', $contract->contract_id);
            $charge_slip = ChargeSlip::select(
                            DB::raw('0 as contract_othr_id'),
                            DB::raw('0 as contract_id'),
                            'rate',
                            'reading',
                            DB::raw('0 as contract_is_vatted'),
                            DB::raw('rate * reading as total'),
                            DB::raw('charge_slip_nature as contract_notes'),
                            DB::raw('0 as sort_key'),
                            'b_refcharges.charge_id',
                            'b_refcharges.charge_desc'
                        )
                        ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_charge_slip.charge_id')
                        ->where('status', 1)
                        ->where('tenant_id', $contract->tenant_id)
                        ->where('app_year', $period->app_year)
                        ->where('month_id', $period->month_id);

            $othr_charges = $other->unionAll($charge_slip)->get();

            $adjustments = Adjustment::leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_adjustments.charge_id')
                        ->where('b_adjustments.is_deleted', 0)
                        ->where('b_adjustments.period_id', $period_id)
                        ->where('b_adjustments.tenant_id', $contract->tenant_id)
                        ->where('b_adjustments.is_approved', 1)
                        ->get();

            $billing_info = new BillingInfo;

            $billing_info->billing_no = DB::raw("CreateBillingNo()");
            $billing_info->period_id = $period_id;
            $billing_info->tenant_id = $contract->tenant_id;
            $billing_info->contract_id = $contract->contract_id;
            $billing_info->month_id = $period->month_id;
            $billing_info->app_year = $period->app_year;
            $billing_info->due_date = $period->period_due_date;
            $billing_info->total_fixed_rent = $schedules[0]->fixed_rent;
            // $billing_info->total_util_charges = $total_util_charges;
            // $billing_info->total_misc_charges = $total_misc_charges;
            // $billing_info->total_othr_charges = $total_othr_charges;
            // $billing_info->sub_total = $contract->contract_fixed_rent + $total_util_charges + $total_misc_charges + $total_othr_charges;
            // $billing_info->vatable_amount = $request->input('vatable_amount');
            //// $billing_info->discounted_vatable_amount = $request->input('discounted_vatable_amount');
            // $billing_info->vat_percent = $request->input('vat_percent');
            // $billing_info->total_vat = $request->input('total_vat');
            // $billing_info->interested_amount = $request->input('interested_amount');
            // $billing_info->interest_percent = $request->input('interest_percent');
            // $billing_info->interest_total = $request->input('interest_total');
            // $billing_info->penaltied_amount = $request->input('penaltied_amount');
            // $billing_info->penalty_percent = $request->input('penalty_percent');
            // $billing_info->penalty_total = $request->input('penalty_total');
            //// $billing_info->discounted_total_amount_due = $request->input('discounted_total_amount_due');
            // $billing_info->total_amount_due = $request->input('total_amount_due');
            //// $billing_info->total_adjusted_in = $request->input('total_adjusted_in');
            //// $billing_info->total_adjusted_out = $request->input('total_adjusted_out');
            $billing_info->wtax_amount = $billing_info->total_fixed_rent * 0.05;
            $billing_info->wtax_percent = 5;
            $billing_info->created_datetime = Carbon::now();
            $billing_info->created_by = Auth::user()->id;

            if($billing_info->save()){
                $billing_id = $billing_info->billing_id;
                $schedules_dataSet = [];
                $utilities_dataSet = [];
                $miscs_dataSet = [];
                $others_dataSet = [];
                $adjustments_dataSet = [];
                $vatable_amount = 0;
                $total_util_charges = 0;
                $total_misc_charges = 0;
                $total_othr_charges = 0;
                $total_adjusted_in = 0;
                $total_adjusted_out = 0;
                $electric_wtax_amount = 0;
                
                foreach($schedules as $schedule){
                    $schedules_dataSet[] = [
                        'billing_id' => $billing_id,
                        'month_id' => $schedule->month_id,
                        'app_year' => $schedule->app_year,
                        'discounted_line_total' => $schedule->discounted_amount_due,
                        'line_total' => $schedule->amount_due,
                        'is_vatted' => $schedule->is_vatted,
                        'billing_schedule_notes' => $schedule->contract_schedule_notes
                    ];
                    if($schedule->is_vatted)
                    {
                        $vatable_amount += $schedule->amount_due;
                    }
                }
    
                foreach($util_charges as $utility){
                    $utilities_dataSet[] = [
                        'billing_id' => $billing_id,
                        'charge_id' => $utility->charge_id,
                        'billing_util_rate' => $utility->contract_rate,
                        'billing_util_reading' => $utility->contract_default_reading,
                        'billing_util_line_total' => $utility->total,
                        'billing_util_is_vatted' => $utility->contract_is_vatted,
                        'billing_util_notes' => $utility->contract_notes,
                        'sort_key' => $utility->sort_key
                    ];
                    if($utility->contract_is_vatted)
                    {
                        $vatable_amount += $utility->total;
                    }
                    if($contract->is_withhold_electricity == 1)
                    {
                        if($utility->charge_id == $electric_charge_id)
                        {
                            $electric_wtax_amount += $utility->total;
                        }
                    }
                    $total_util_charges += $utility->total;
                }
    
                foreach($misc_charges as $misc){
                    $miscs_dataSet[] = [
                        'billing_id' => $billing_id,
                        'charge_id' => $misc->charge_id,
                        'billing_misc_rate' => $misc->contract_rate,
                        'billing_misc_reading' => $misc->contract_default_reading,
                        'billing_misc_line_total' => $misc->total,
                        'billing_misc_is_vatted' => $misc->contract_is_vatted,
                        'billing_misc_notes' => $misc->contract_notes,
                        'sort_key' => $misc->sort_key
                    ];
                    if($misc->contract_is_vatted)
                    {
                        $vatable_amount += $misc->total;
                    }
                    if($contract->is_withhold_electricity == 1)
                    {
                        if($misc->charge_id == $electric_charge_id)
                        {
                            $electric_wtax_amount += $misc->total;
                        }
                    }
                    $total_misc_charges += $misc->total;
                }
    
                foreach($othr_charges as $other){
                    $others_dataSet[] = [
                        'billing_id' => $billing_id,
                        'charge_id' => $other->charge_id,
                        'billing_othr_rate' => $other->contract_rate,
                        'billing_othr_reading' => $other->contract_default_reading,
                        'billing_othr_line_total' => $other->total,
                        'billing_othr_is_vatted' => $other->contract_is_vatted,
                        'billing_othr_notes' => $other->contract_notes,
                        'sort_key' => $other->sort_key
                    ];
                    if($other->contract_is_vatted)
                    {
                        $vatable_amount += $other->total;
                    }
                    if($contract->is_withhold_electricity == 1)
                    {
                        if($other->charge_id == $electric_charge_id)
                        {
                            $electric_wtax_amount += $other->total;
                        }
                    }
                    $total_othr_charges += $other->total;
                }
                // return $adjustments;
                foreach($adjustments as $adjustment){
                    $adjustments_dataSet[] = [
                        'billing_id' => $billing_id,
                        'charge_id' => $adjustment->charge_id,
                        'billing_adjustment_rate' => $adjustment->adjustment_rate,
                        'billing_adjustment_reading' => $adjustment->adjustment_reading,
                        'billing_adjustment_line_total' => $adjustment->amount,
                        'billing_adjustment_notes' => $adjustment->notes,
                        'sort_key' => $adjustment->sort
                    ];
                    if($adjustment->adjustment_type == 0)
                    {
                        $total_adjusted_in += $adjustment->amount;
                    }
                    else
                    {
                        $total_adjusted_out += $adjustment->amount;
                    }
                }
                
                $billing_info->total_util_charges = $total_util_charges;
                $billing_info->total_misc_charges = $total_misc_charges;
                $billing_info->total_othr_charges = $total_othr_charges;
                $billing_info->total_adjusted_in = $total_adjusted_in;
                $billing_info->total_adjusted_out = $total_adjusted_out;
                $billing_info->vatable_amount = $vatable_amount;
                $billing_info->vat_percent = $contract->vat_percent;
                $billing_info->electric_wtax_amount = $electric_wtax_amount * 0.02;
                $billing_info->electric_wtax_percent = 2;
                $billing_info->total_vat = $vatable_amount * ($contract->vat_percent/100);
                $billing_info->sub_total = $billing_info->total_fixed_rent + $total_util_charges + $total_misc_charges + $total_othr_charges + $billing_info->total_vat;

                $prevbalance = DB::select("select GetPreviousBalance(".$period->month_id.", ".$period->app_year.", ".$contract->tenant_id.", ".$contract->department_id.") as prevBalance");
                $latepayment = DB::select("select GetLatePayment(".$period->month_id.", ".$period->app_year.", ".$contract->tenant_id.", ".$contract->department_id.") as latePayment");

                $billing_info->interested_amount = max($prevbalance[0]->prevBalance, 0);
                $billing_info->interest_percent = 3;
                $billing_info->interest_total = max($prevbalance[0]->prevBalance, 0) * 0.03;
                $billing_info->penaltied_amount = $latepayment[0]->latePayment;
                $billing_info->penalty_percent = 3;
                $billing_info->penalty_total = $latepayment[0]->latePayment * 0.03;
                // $billing_info->discounted_total_amount_due = $request->input('discounted_total_amount_due');
                $billing_info->total_amount_due = $billing_info->sub_total + $billing_info->interest_total + $billing_info->penalty_total + $total_adjusted_in - $total_adjusted_out;
                $billing_info->save();

    
                DB::table('b_billing_schedule')->insert($schedules_dataSet);
                DB::table('b_billing_util_charges')->insert($utilities_dataSet);
                DB::table('b_billing_misc_charges')->insert($miscs_dataSet);
                DB::table('b_billing_othr_charges')->insert($others_dataSet);
                DB::table('b_billing_adjustments')->insert($adjustments_dataSet);
            }
        }
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

    public function scheduleAndCharges($id)
    {
        DB::statement(DB::raw('set @row=0'));
        $schedules = BillingSchedule::select(
                        'b_billing_schedule.*',
                        'b_billing_schedule.billing_schedule_notes as contract_schedule_notes',
                        'discounted_line_total as discounted_amount_due',
                        'line_total as amount_due',
                        'b_refmonths.*',
                        DB::raw("@row := @row + 1 as count")
                    )
                    ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_schedule.month_id')
                    ->where('billing_id', $id)->get();
        $util_charges = BillingUtilCharges::select(
                        'billing_util_id',
                        'billing_id',
                        'billing_util_rate as contract_rate',
                        'billing_util_reading as contract_default_reading',
                        'billing_util_is_vatted as contract_is_vatted',
                        'billing_util_notes as contract_notes',
                        'billing_util_line_total',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_util_charges.charge_id')
                    ->where('billing_id', $id)->orderBy('sort_key', 'asc')->get();
        $misc_charges = BillingMiscCharges::select(
                        'billing_misc_id',
                        'billing_id',
                        'billing_misc_rate as contract_rate',
                        'billing_misc_reading as contract_default_reading',
                        'billing_misc_is_vatted as contract_is_vatted',
                        'billing_misc_notes as contract_notes',
                        'billing_misc_line_total',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_misc_charges.charge_id')
                    ->where('billing_id', $id)->orderBy('sort_key', 'asc')->get();
        $othr_charges = BillingOthrCharges::select(
                        'billing_othr_id',
                        'billing_id',
                        'billing_othr_rate as contract_rate',
                        'billing_othr_reading as contract_default_reading',
                        'billing_othr_is_vatted as contract_is_vatted',
                        'billing_othr_notes as contract_notes',
                        'billing_othr_line_total',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_othr_charges.charge_id')
                    ->where('billing_id', $id)->orderBy('sort_key', 'asc')->get();

        $adjustments = BillingAdjustments::select(
                        'billing_adjustment_id',
                        'billing_id',
                        'billing_adjustment_rate as contract_rate',
                        'billing_adjustment_reading as contract_default_reading',
                        'billing_adjustment_is_vatted as contract_is_vatted',
                        'billing_adjustment_type as adjustment_type',
                        'billing_adjustment_notes as contract_notes',
                        'billing_adjustment_line_total',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_adjustments.charge_id')
                    ->where('billing_id', $id)->orderBy('sort_key', 'asc')->get();

        $billing['schedules'] = Reference::collection($schedules);
        $billing['util_charges'] = Reference::collection($util_charges);
        $billing['misc_charges'] = Reference::collection($misc_charges);
        $billing['othr_charges'] = Reference::collection($othr_charges);
        $billing['adjustments'] = Reference::collection($adjustments);
        return $billing;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $soa = false)
    {
        $billing = BillingInfo::select('*', 'b_billing_info.vat_percent')
                        ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                        ->leftJoin('b_refbillingperiod', 'b_refbillingperiod.period_id', '=', 'b_billing_info.period_id')
                        ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id');

        if($soa == true){
            $billing
                ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id');
        }

        return ( new Reference( $billing->findOrFail($id) ) )
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
                'contract_id' => 'required|not_in:0',
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'tenant',
            'contract_id' => 'contract']
        )->validate();
        
        $billing_info = BillingInfo::findOrFail($request->input('billing_id'));

        $billing_info->period_id = $request->input('period_id');
        $billing_info->tenant_id = $request->input('tenant_id');
        $billing_info->contract_id = $request->input('contract_id');
        $billing_info->month_id = $request->input('month_id');
        $billing_info->app_year = $request->input('app_year');
        $billing_info->due_date = date("Y-m-d", strtotime($request->input('due_date')));
        $billing_info->total_discounted_rent = $request->input('total_discounted_rent');
        $billing_info->total_fixed_rent = $request->input('total_fixed_rent');
        $billing_info->total_util_charges = $request->input('total_util_charges');
        $billing_info->total_misc_charges = $request->input('total_misc_charges');
        $billing_info->total_othr_charges = $request->input('total_othr_charges');
        $billing_info->sub_total = $request->input('sub_total');
        $billing_info->vatable_amount = $request->input('vatable_amount');
        $billing_info->discounted_vatable_amount = $request->input('discounted_vatable_amount');
        $billing_info->vat_percent = $request->input('vat_percent');
        $billing_info->total_vat = $request->input('total_vat');
        $billing_info->interested_amount = $request->input('interested_amount');
        $billing_info->interest_percent = $request->input('interest_percent');
        $billing_info->interest_total = $request->input('interest_total');
        $billing_info->penaltied_amount = $request->input('penaltied_amount');
        $billing_info->penalty_percent = $request->input('penalty_percent');
        $billing_info->penalty_total = $request->input('penalty_total');
        $billing_info->total_amount_due = $request->input('total_amount_due');
        $billing_info->discounted_total_amount_due = $request->input('discounted_total_amount_due');
        $billing_info->total_adjusted_in = $request->input('total_adjusted_in');
        $billing_info->total_adjusted_out = $request->input('total_adjusted_out');
        $billing_info->wtax_amount = $request->input('wtax_amount');
        $billing_info->wtax_percent = $request->input('wtax_percent');
        $billing_info->electric_wtax_amount = $request->input('electric_wtax_amount');
        $billing_info->electric_wtax_percent = $request->input('electric_wtax_percent');

        $billing_info->modified_datetime = Carbon::now();
        $billing_info->modified_by = Auth::user()->id;

        if($billing_info->save()){
            $billing_id = $billing_info->billing_id;
            $schedules_dataSet = [];
            $utilities_dataSet = [];
            $miscs_dataSet = [];
            $others_dataSet = [];
            $adjustments_dataSet = [];
            
            $old_schedules = BillingSchedule::where('billing_id', $billing_id);
            $old_schedules->delete();

            $old_utilities = BillingUtilCharges::where('billing_id', $billing_id);
            $old_utilities->delete();

            $old_miscs = BillingMiscCharges::where('billing_id', $billing_id);
            $old_miscs->delete();

            $old_other = BillingOthrCharges::where('billing_id', $billing_id);
            $old_other->delete();

            $old_adjustments = BillingAdjustments::where('billing_id', $billing_id);
            $old_adjustments->delete();

            $schedules = $request->input('schedules');
            foreach($schedules as $schedule){
                $schedules_dataSet[] = [
                    'billing_id' => $billing_id,
                    'month_id' => $schedule['month_id'],
                    'app_year' => $schedule['app_year'],
                    'discounted_line_total' => $schedule['discounted_amount_due'],
                    'line_total' => $schedule['amount_due'],
                    'is_vatted' => $schedule['is_vatted'],
                    'billing_schedule_notes' => $schedule['contract_schedule_notes'],
                ];
            }

            $utilities = $request->input('utilities');
            foreach($utilities as $utility){
                $utilities_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $utility['charge_id'],
                    'billing_util_rate' => $utility['contract_rate'],
                    'billing_util_reading' => $utility['contract_default_reading'],
                    'billing_util_line_total' => $utility['contract_line_total'],
                    'billing_util_is_vatted' => $utility['contract_is_vatted'],
                    'billing_util_notes' => $utility['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $miscs = $request->input('miscellaneous');
            foreach($miscs as $misc){
                $miscs_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $misc['charge_id'],
                    'billing_misc_rate' => $misc['contract_rate'],
                    'billing_misc_reading' => $misc['contract_default_reading'],
                    'billing_misc_line_total' => $misc['contract_line_total'],
                    'billing_misc_is_vatted' => $misc['contract_is_vatted'],
                    'billing_misc_notes' => $misc['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $others = $request->input('other');
            foreach($others as $other){
                $others_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $other['charge_id'],
                    'billing_othr_rate' => $other['contract_rate'],
                    'billing_othr_reading' => $other['contract_default_reading'],
                    'billing_othr_line_total' => $other['contract_line_total'],
                    'billing_othr_is_vatted' => $other['contract_is_vatted'],
                    'billing_othr_notes' => $other['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $adjustments = $request->input('adjustments');
            foreach($adjustments as $adjustment){
                $adjustments_dataSet[] = [
                    'billing_id' => $billing_id,
                    'charge_id' => $adjustment['charge_id'],
                    'billing_adjustment_rate' => $adjustment['contract_rate'],
                    'billing_adjustment_reading' => $adjustment['contract_default_reading'],
                    'billing_adjustment_line_total' => $adjustment['contract_line_total'],
                    'billing_adjustment_is_vatted' => $adjustment['contract_is_vatted'],
                    'billing_adjustment_type' => $adjustment['adjustment_type'],
                    'billing_adjustment_notes' => $adjustment['contract_notes'],
                    'sort_key' => $adjustment['sort_key']
                ];
            }

            DB::table('b_billing_schedule')->insert($schedules_dataSet);
            DB::table('b_billing_util_charges')->insert($utilities_dataSet);
            DB::table('b_billing_misc_charges')->insert($miscs_dataSet);
            DB::table('b_billing_othr_charges')->insert($others_dataSet);
            DB::table('b_billing_adjustments')->insert($adjustments_dataSet);
        }
        $data = BillingInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_billing_info.tenant_id')
                            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                            ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_info.month_id')
                            ->findOrFail($billing_info->billing_id);
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
        $billing = BillingInfo::findOrFail($id);
        $billing->is_deleted = 1;
        $billing->deleted_datetime = Carbon::now();
        $billing->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $billing->save();

        return ( new Reference( $billing ) )
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

    public function prevBalance($month_id, $app_year, $tenant_id, $department_id){
        // $response['balance'] = DB::select("select GetPreviousBalance(".$month_id.", ".$app_year.", ".$tenant_id.")");
        return DB::select("select GetPreviousBalance(".$month_id.", ".$app_year.", ".$tenant_id.", ".$department_id.") as prevBalance");
    }

    public function prevSubTotal($month_id, $app_year, $tenant_id){
        // $response['balance'] = DB::select("select GetPreviousBalance(".$month_id.", ".$app_year.", ".$tenant_id.")");
        return DB::select("select GetPreviousSubTotal(".$month_id.", ".$app_year.", ".$tenant_id.") as subTotal");
    }

    public function asOfBalance($month_id, $app_year, $tenant_id){
        // $response['balance'] = DB::select("select GetPreviousBalance(".$month_id.", ".$app_year.", ".$tenant_id.")");
        return DB::select("select GetAsOfBalance(".$month_id.", ".$app_year.", ".$tenant_id.") as asOfBalance");
    }

    public function getBillingBalance($tenant_id){
        $billings = DB::select("SELECT 
                                    SUM(IFNULL(total_amount_due,0)) - IFNULL(bpi.amount_paid,0) as total_outstanding_balance
                                FROM b_billing_info as bbi 
                                LEFT JOIN (
                                    SELECT SUM(IFNULL(amount_paid,0)) as amount_paid, tenant_id FROM b_payment_info WHERE is_canceled = 0 and tenant_id = ".$tenant_id."
                                ) as bpi USING(tenant_id)
                                WHERE bbi.is_deleted = 0 AND bbi.tenant_id =".$tenant_id);
        return $billings[0]->total_outstanding_balance;
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

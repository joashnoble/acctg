<?php

namespace App\Http\Controllers\Transactions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions\BillingInfo;
use App\Models\Transactions\ContractInfo;
use App\Models\Transactions\ContractSchedule;
use App\Models\Transactions\ContractUtilCharges;
use App\Models\Transactions\ContractMiscCharges;
use App\Models\Transactions\ContractOthrCharges;
use App\Models\Transactions\ContractOtherFees;
use App\Models\Transactions\ChargeSlip;
use App\Models\Transactions\StartBilling;
use App\Models\Transactions\StopBilling;
use App\Models\Utilities\CompanySettings;
use App\Models\Accounting\TempJournalAccounts;
use App\Models\Accounting\TempJournalInfo;
use App\Models\References\Customers;
use App\Models\References\FeeType;
use App\Http\Resources\Reference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Auth;

class ContractsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($tenant_id = null)
    {
        $contracts = ContractInfo::select(
                                    'b_contract_info.*',
                                    'b_refdepartments.*',
                                    'b_reflocations.*',
                                    'b_refcontracttype.*',
                                    'b_refcategory.*',
                                    'b_refnatureofbusiness.*',
                                    'b_tenants.*',
                                    DB::raw("IFNULL(a.c_advance_rent, 0) as c_advance_rent"),
                                    DB::raw("IFNULL(a.c_security_deposit, 0) as c_security_deposit"),
                                    DB::raw("IFNULL(a.c_electric_meter_deposit, 0) as c_electric_meter_deposit"),
                                    DB::raw("IFNULL(a.c_water_meter_deposit, 0) as c_water_meter_deposit"),
                                    DB::raw("IFNULL(a.c_construction_deposit, 0) as c_construction_deposit"),
                                    DB::raw("'' as start_billing"),
                                    DB::raw("'' as stop_billing")
                            )
                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refcontracttype', 'b_refcontracttype.contract_type_id', '=', 'b_contract_info.contract_type_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            ->leftJoin(
                                DB::raw("
                                        (SELECT
                                            tenant_id,
                                            SUM(IF(x.fee_type_id = 1, x.amount, 0)) as c_advance_rent,
                                            SUM(IF(x.fee_type_id = 2, x.amount, 0)) as c_security_deposit,
                                            SUM(IF(x.fee_type_id = 3, x.amount, 0)) as c_electric_meter_deposit,
                                            SUM(IF(x.fee_type_id = 4, x.amount, 0)) as c_water_meter_deposit,
                                            SUM(IF(x.fee_type_id = 5, x.amount, 0)) as c_construction_deposit
                                        FROM
                                            (
                                                SELECT
                                                    cof.tenant_id,
                                                    fee_type_id,
                                                    IFNULL(SUM(fee_credit), 0) - IFNULL(SUM(fee_debit), 0) as amount
                                                FROM
                                                    b_contract_other_fees AS cof
                                                LEFT JOIN
                                                    b_payment_info as pi ON pi.payment_id = cof.payment_id
                                                LEFT JOIN
                                                    temp_journal_info AS tji ON tji.fee_id = cof.fee_id
                                                WHERE
                                                    if(cof.payment_id != 0, if(pi.is_sent = 1, 1, 0), tji.is_journal_posted) = 1
                                                GROUP BY  fee_type_id, tenant_id
                                            ) 
                                        AS x
                                        GROUP BY tenant_id
                                ) a" ), 'b_contract_info.tenant_id', '=', 'a.tenant_id')
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->where('b_contract_info.is_deleted', 0);
        if($tenant_id != null){
            $contracts->where('b_contract_info.tenant_id', $tenant_id);
            $contracts->where('b_contract_info.is_active', 1);
            $contracts->where('b_contract_info.status', 1);
        }  

        return Reference::collection($contracts->orderBy('b_contract_info.contract_id','desc')->get());
    }

    public function getFeeType($tenant_id = null)
    {
        $fee_type = FeeType::select(
                                'b_reffeetype.*',
                                DB::raw($tenant_id. ' as tenant_id'),
                                DB::raw("IFNULL(SUM(cof.fee_credit), 0) - IFNULL(SUM(cof.fee_debit), 0) as amount"),
                                DB::raw('"" as history')
                            )
                            ->leftJoin('b_contract_other_fees as cof', function($join) use ($tenant_id){
                                $join->on('cof.fee_type_id', '=', 'b_reffeetype.fee_type_id')
                                    ->leftJoin('temp_journal_info as tji', 'tji.fee_id', '=', 'cof.fee_id')
                                    ->leftJoin('b_payment_info', 'b_payment_info.payment_id', '=', 'cof.payment_id')
                                    ->whereRaw('if(cof.payment_id != 0, if(b_payment_info.is_sent = 1, 1, 0), is_journal_posted) = 1')
                                    ->where('cof.tenant_id', $tenant_id)
                                    ;
                              })
                            ->groupBy('fee_type_id')
                            ->get();

        return Reference::collection($fee_type);
    }

    public function saveFees(Request $request)
    {
        Validator::make($request->all(),
            [
                'fee_no' => 'required',
                'amount' => 'required'
            ]
        )->validate();

        $fee = new ContractOtherFees;
        $fee->tenant_id = $request->input('tenant_id');
        $fee->fee_type_id = $request->input('fee_type_id');
        $fee->fee_no = $request->input('fee_no');
        $fee->fee_credit = $request->input('amount');
        $fee->fee_notes = $request->input('fee_notes');
        $fee->created_datetime = Carbon::now();
        $fee->created_by = Auth::user()->id;

        if($fee->save())
        {   
            $company = CompanySettings::findOrFail(1);
            $customer = Customers::where('tenant_id', $request->input('tenant_id'))->firstOrFail();

            $journal_info = new TempJournalInfo;
            $journal_info->department_id = $company->account_department_id;;
            $journal_info->customer_id = $customer->customer_id;
            $journal_info->remarks = $request->input('fee_notes');
            $journal_info->date_txn = Carbon::now();
            $journal_info->payment_method_id = 0;
            $journal_info->ref_no = $request->input('fee_no');
            $journal_info->amount = $request->input('amount');
            $journal_info->is_sales = 0;
            $journal_info->journal_id = 0;
            $journal_info->book_type_id = 2;
            $journal_info->fee_id = $fee->fee_id;

            if($journal_info->save())
            {
                $temp_journal_id = $journal_info->temp_journal_id;
                DB::select('CALL insert_fees_to_accounting_details('.$fee->fee_id.', '.$temp_journal_id.')');
            }
        }
        

        return ( new Reference( $fee ) )
            ->response()
            ->setStatusCode(200);
    }

    public function contractFeesHistory($tenant_id, $fee_type_id)
    {
        DB::statement(DB::raw('set @balance=0'));
        DB::statement(DB::raw('set @pending_balance=0'));
        $contract_fees = ContractOtherFees::select(
                                                'fee_debit',
                                                'fee_credit',
                                                'fee_notes',
                                                'b_contract_other_fees.created_datetime',
                                                DB::raw('if(b_contract_other_fees.payment_id != 0, if(b_payment_info.is_sent = 1, 1, 0), is_journal_posted) as is_journal_posted'),
                                                DB::raw('if(if(b_contract_other_fees.payment_id != 0, if(b_payment_info.is_sent = 1, 1, 0), is_journal_posted) = 1, @balance:= @balance + (fee_credit - fee_debit), @balance:= @balance + 0) as balance'),
	                                            DB::raw('if(if(b_contract_other_fees.payment_id != 0, if(b_payment_info.is_sent = 1, 1, 0), is_journal_posted) = 0, @pending_balance:= @pending_balance + (fee_credit - fee_debit), @pending_balance:= @pending_balance + 0) as pending_balance
                                                ')
                                            )
                                            ->leftJoin('b_payment_info', 'b_payment_info.payment_id', '=', 'b_contract_other_fees.payment_id')
                                            ->leftJoin('temp_journal_info', 'temp_journal_info.fee_id', '=', 'b_contract_other_fees.fee_id')
                                            ->where('b_contract_other_fees.tenant_id', $tenant_id)
                                            ->where('fee_type_id', $fee_type_id)
                                            ->get();

        return Reference::collection($contract_fees);
    }

    public function scheduleAndCharges($id)
    {
        DB::statement(DB::raw('set @row=0'));
        $schedules = ContractSchedule::select(
                        'b_contract_schedule.*',
                        'b_refmonths.*',
                        DB::raw("@row := @row + 1 as count")
                    )
                    ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_contract_schedule.month_id')
                    ->where('contract_id', $id)->get();
        $util_charges = ContractUtilCharges::select(
                        'contract_util_id',
                        'contract_id',
                        'contract_util_rate as contract_rate',
                        'contract_util_default_reading as contract_default_reading',
                        'contract_util_is_vatted as contract_is_vatted',
                        'contract_util_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_util_charges.charge_id')
                    ->where('contract_id', $id)->orderBy('sort_key', 'asc')->get();
        $misc_charges = ContractMiscCharges::select(
                        'contract_misc_id',
                        'contract_id',
                        'contract_misc_rate as contract_rate',
                        'contract_misc_default_reading as contract_default_reading',
                        'contract_misc_is_vatted as contract_is_vatted',
                        'contract_misc_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_misc_charges.charge_id')
                    ->where('contract_id', $id)->orderBy('sort_key', 'asc')->get();
        $othr_charges = ContractOthrCharges::select(
                        'contract_othr_id',
                        'contract_id',
                        'contract_othr_rate as contract_rate',
                        'contract_othr_default_reading as contract_default_reading',
                        'contract_othr_is_vatted as contract_is_vatted',
                        'contract_othr_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_othr_charges.charge_id')
                    ->where('contract_id', $id)->orderBy('sort_key', 'asc')->get();

        $contracts['schedules'] = Reference::collection($schedules);
        $contracts['util_charges'] = Reference::collection($util_charges);
        $contracts['misc_charges'] = Reference::collection($misc_charges);
        $contracts['othr_charges'] = Reference::collection($othr_charges);
        return $contracts;
    }

    public function scheduleAndChargesBilling($id, $app_year = NULL, $month_id = NULL, $tenant_id = NULL)
    {
        DB::statement(DB::raw('set @row=0'));
        $schedules = ContractSchedule::select(
                        'b_contract_schedule.*',
                        'b_refmonths.*',
                        DB::raw("@row := @row + 1 as count")
                    )
                    ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_contract_schedule.month_id')
                    ->where('app_year', $app_year)
                    ->where('b_contract_schedule.month_id', $month_id)
                    ->where('contract_id', $id)
                    ->get();
        $util_charges = ContractUtilCharges::select(
                        'contract_util_id',
                        'contract_id',
                        'contract_util_rate as contract_rate',
                        'contract_util_default_reading as contract_default_reading',
                        'contract_util_is_vatted as contract_is_vatted',
                        'contract_util_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_util_charges.charge_id')
                    ->where('contract_id', $id)->get();
        $misc_charges = ContractMiscCharges::select(
                        'contract_misc_id',
                        'contract_id',
                        'contract_misc_rate as contract_rate',
                        'contract_misc_default_reading as contract_default_reading',
                        'contract_misc_is_vatted as contract_is_vatted',
                        'contract_misc_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_misc_charges.charge_id')
                    ->where('contract_id', $id)->get();
        $othr_charges = ContractOthrCharges::select(
                        'contract_othr_id',
                        'contract_id',
                        'contract_othr_rate as contract_rate',
                        'contract_othr_default_reading as contract_default_reading',
                        'contract_othr_is_vatted as contract_is_vatted',
                        'contract_othr_notes as contract_notes',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_contract_othr_charges.charge_id')
                    ->where('contract_id', $id);
        $charge_slip = ChargeSlip::select(
                        DB::raw('0 as contract_othr_id'),
                        DB::raw('0 as contract_id'),
                        'rate',
                        'reading',
                        DB::raw('0 as contract_is_vatted'),
                        DB::raw('charge_slip_nature as contract_notes'),
                        DB::raw('0 as sort_key'),
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_charge_slip.charge_id')
                    ->where('status', 1)
                    ->where('tenant_id', $tenant_id)
                    ->where('app_year', $app_year)
                    ->where('month_id', $month_id);
        
        // return $charge_slip->get();
        $contracts['schedules'] = Reference::collection($schedules);
        $contracts['util_charges'] = Reference::collection($util_charges);
        $contracts['misc_charges'] = Reference::collection($misc_charges);
        $contracts['othr_charges'] = Reference::collection($othr_charges->unionAll($charge_slip)->get());
        return $contracts;
    }

    public function renew(Request $request, $id)
    {
        Validator::make($request->all(),
            [
                'tenant_id' => 'required|not_in:0',
                'department_id' => 'required|not_in:0',
                'nature_of_business_id' => 'required|not_in:0',
                'location_id' => 'required|not_in:0',
                'contract_type_id' => 'required|not_in:0',
                'category_id' => 'required|not_in:0',
                'contract_terms' => 'required|not_in:0|numeric',
                'commencement_date' => 'required',
                'termination_date' => 'required',
                'start_billing_date' => 'required',
                'contract_floor_area' => 'required|not_in:0|numeric',
                'contract_fixed_rent' => 'required|not_in:0|numeric',
                'schedules' => 'required'
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'lessee/tenant',
            'department_id' => 'department',
            'nature_of_business_id' => 'nature of business',
            'location_id' => 'location',
            'contract_type_id' => 'contract type',
            'category_id' => 'category',
            'contract_floor_area' => 'app floor area',
            'contract_fixed_rent' => 'basic rental',
            'schedules' => 'schedule of payment']
        )->validate();

        $contract = ContractInfo::findOrFail($id);
        $contract->renewed = 1;
        $contract->save();

        $contract_info = new ContractInfo;
        $contract_schedule = new ContractSchedule;
        $contract_util_charges = new ContractUtilCharges;
        $contract_misc_charges = new ContractMiscCharges;
        $contract_othr_charges = new ContractOthrCharges;

        $contract_info->contract_no = DB::raw("CreateContractNo()");
        $contract_info->tenant_id = $request->input('tenant_id');
        $contract_info->contract_billing_address = $request->input('contract_billing_address');
        $contract_info->contract_type_id = $request->input('contract_type_id');
        $contract_info->category_id = $request->input('category_id');
        $contract_info->commencement_date = date("Y-m-d", strtotime($request->input('commencement_date')));
        $contract_info->termination_date = date('Y-m-d', strtotime($request->input('termination_date')));
        $contract_info->start_billing_date = date('Y-m-d', strtotime($request->input('start_billing_date')));
        $contract_info->location_id = $request->input('location_id');
        $contract_info->contract_signatory = $request->input('contract_signatory');
        $contract_info->contract_terms = $request->input('contract_terms');
        $contract_info->schedule_type = $request->input('schedule_type');
        $contract_info->contract_approved_merch = $request->input('contract_approved_merch');
        $contract_info->contract_fixed_rent = $request->input('contract_fixed_rent');
        $contract_info->contract_discounted_rent = $request->input('contract_discounted_rent');
        $contract_info->contract_advance_rent = $request->input('contract_advance_rent');
        $contract_info->water_meter_deposit = $request->input('water_meter_deposit');
        $contract_info->construction_deposit = $request->input('construction_deposit');
        $contract_info->power_meter_deposit = $request->input('power_meter_deposit');
        $contract_info->escalation_notes = $request->input('escalation_notes');
        $contract_info->contract_remarks = $request->input('contract_remarks');
        $contract_info->security_deposit = $request->input('security_deposit');
        $contract_info->escalation_type = $request->input('escalation_type');
        $contract_info->contract_escalation_percent = $request->input('contract_escalation_percent');
        $contract_info->contract_floor_area = $request->input('contract_floor_area');
        $contract_info->department_id = $request->input('department_id');
        $contract_info->nature_of_business_id = $request->input('nature_of_business_id');
        $contract_info->is_renewal = 1;
        $contract_info->is_active = $request->input('is_active');

        $contract_info->oec_electricity = $request->input('oec_electricity');
        $contract_info->oec_water = $request->input('oec_water');
        $contract_info->oec_marketing = $request->input('oec_marketing');
        $contract_info->oec_processing = $request->input('oec_processing');
        $contract_info->oec_aircondition = $request->input('oec_aircondition');
        $contract_info->oec_cusa = $request->input('oec_cusa');
        $contract_info->oec_repair_maintenance = $request->input('oec_repair_maintenance');
        $contract_info->oec_expense_utilities = $request->input('oec_expense_utilities');
        $contract_info->oec_security_deposit = $request->input('oec_security_deposit');

        $contract_info->created_datetime = Carbon::now();
        $contract_info->created_by = Auth::user()->id;
        
        if($contract_info->save()){
            $contract_id = $contract_info->contract_id;
            $schedules_dataSet = [];
            $utilities_dataSet = [];
            $miscs_dataSet = [];
            $others_dataSet = [];
            
            $schedules = $request->input('schedules');
            foreach($schedules as $schedule){
                $schedules_dataSet[] = [
                    'contract_id' => $contract_id,
                    'month_id' => $schedule['month_id'],
                    'app_year' => $schedule['app_year'],
                    'discounted_rent' => $schedule['discounted_rent'],
                    'fixed_rent' => $schedule['fixed_rent'],
                    'escalation_percent' => $schedule['escalation_percent'],
                    'escalation_amount' => $schedule['escalation_amount'],
                    'discounted_amount_due' => $schedule['discounted_amount_due'],
                    'amount_due' => $schedule['amount_due'],
                    'is_vatted' => $schedule['is_vatted'],
                    'contract_schedule_notes' => $schedule['contract_schedule_notes']
                ];
            }

            $utilities = $request->input('utilities');
            foreach($utilities as $utility){
                $utilities_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $utility['charge_id'],
                    'contract_util_rate' => $utility['contract_rate'],
                    'contract_util_default_reading' => $utility['contract_default_reading'],
                    'contract_util_is_vatted' => $utility['contract_is_vatted'],
                    'contract_util_notes' => $utility['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $miscs = $request->input('miscellaneous');
            foreach($miscs as $misc){
                $miscs_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $misc['charge_id'],
                    'contract_misc_rate' => $misc['contract_rate'],
                    'contract_misc_default_reading' => $misc['contract_default_reading'],
                    'contract_misc_is_vatted' => $misc['contract_is_vatted'],
                    'contract_misc_notes' => $misc['contract_notes'],
                    'sort_key' => $misc['sort_key']
                ];
            }

            $others = $request->input('other');
            foreach($others as $other){
                $others_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $other['charge_id'],
                    'contract_othr_rate' => $other['contract_rate'],
                    'contract_othr_default_reading' => $other['contract_default_reading'],
                    'contract_othr_is_vatted' => $other['contract_is_vatted'],
                    'contract_othr_notes' => $other['contract_notes'],
                    'sort_key' => $other['sort_key']
                ];
            }

            DB::table('b_contract_schedule')->insert($schedules_dataSet);
            DB::table('b_contract_util_charges')->insert($utilities_dataSet);
            DB::table('b_contract_misc_charges')->insert($miscs_dataSet);
            DB::table('b_contract_othr_charges')->insert($others_dataSet);
        }

        $data = ContractInfo::select(
                                    'b_contract_info.*',
                                    'b_refdepartments.*',
                                    'b_reflocations.*',
                                    'b_refcontracttype.*',
                                    'b_refcategory.*',
                                    'b_refnatureofbusiness.*',
                                    'b_tenants.*',
                                    DB::raw("IFNULL(a.c_advance_rent, 0) as c_advance_rent"),
                                    DB::raw("IFNULL(a.c_security_deposit, 0) as c_security_deposit"),
                                    DB::raw("IFNULL(a.c_electric_meter_deposit, 0) as c_electric_meter_deposit"),
                                    DB::raw("IFNULL(a.c_water_meter_deposit, 0) as c_water_meter_deposit"),
                                    DB::raw("IFNULL(a.c_construction_deposit, 0) as c_construction_deposit"),
                                    DB::raw("'' as start_billing"),
                                    DB::raw("'' as stop_billing")
                            )
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            ->leftJoin('b_refcontracttype', 'b_refcontracttype.contract_type_id', '=', 'b_contract_info.contract_type_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin(
                                DB::raw("
                                        (SELECT
                                            tenant_id,
                                            SUM(IF(x.fee_type_id = 1, x.amount, 0)) as c_advance_rent,
                                            SUM(IF(x.fee_type_id = 2, x.amount, 0)) as c_security_deposit,
                                            SUM(IF(x.fee_type_id = 3, x.amount, 0)) as c_electric_meter_deposit,
                                            SUM(IF(x.fee_type_id = 4, x.amount, 0)) as c_water_meter_deposit,
                                            SUM(IF(x.fee_type_id = 5, x.amount, 0)) as c_construction_deposit
                                        FROM
                                            (
                                                SELECT
                                                    cof.tenant_id,
                                                    fee_type_id,
                                                    IFNULL(SUM(fee_credit), 0) - IFNULL(SUM(fee_debit), 0) as amount
                                                FROM
                                                    b_contract_other_fees AS cof
                                                LEFT JOIN
                                                    b_payment_info as pi ON pi.payment_id = cof.payment_id
                                                LEFT JOIN
                                                    temp_journal_info AS tji ON tji.fee_id = cof.fee_id
                                                WHERE
                                                    if(cof.payment_id != 0, if(pi.is_sent = 1, 1, 0), tji.is_journal_posted) = 1
                                                GROUP BY  fee_type_id, tenant_id
                                            ) 
                                        AS x
                                        GROUP BY tenant_id
                                ) a" ), 'b_contract_info.tenant_id', '=', 'a.tenant_id')
                            ->findOrFail($contract_info->contract_id);

        return ( new Reference( $data ) )
            ->response()
            ->setStatusCode(201);
    }

    public function BillingForms($contract_id)
    {
        $data['start_billing'] = StartBilling::where('contract_id', $contract_id)
                                            ->where('is_cancelled', 0)
                                            ->get();

        $data['stop_billing'] = StopBilling::where('contract_id', $contract_id)
                                            ->where('is_cancelled', 0)
                                            ->get();                             
        return $data;
    }

    public function StartBillingForm(Request $request)
    {
        Validator::make($request->all(),
        [
            'trade_name' => 'required',
            'company_name' => 'required',
            'contract_floor_area' => 'required',
            'contract_fixed_rent' => 'required',
            'oec_cusa' => 'required',
            'oec_aircondition' => 'required',
            'oec_processing' => 'required'
        ])->setAttributeNames([
            'contract_floor_area' => 'floor area',
            'contract_fixed_rent' => 'monthly rental',
            'oec_cusa' => 'CUSA',
            'oec_aircondition' => 'aircondition',
            'oec_processing' => 'contract processing fee']
        )->validate();

        $start_billing = new StartBilling;
        $start_billing->start_billing_no = DB::select("select CreateStartBillingNo() as start_billing_no")[0]->start_billing_no;
        $start_billing->contract_id = $request->input('contract_id');
        $start_billing->date = date('Y-m-d h:i:s', strtotime($request->input('date')));
        $start_billing->trade_name = $request->input('trade_name');
        $start_billing->company_name = $request->input('company_name');
        $start_billing->start_billing_date = date('Y-m-d h:i:s', strtotime($request->input('start_billing_date')));
        $start_billing->contract_floor_area = $request->input('contract_floor_area');
        $start_billing->contract_fixed_rent = $request->input('contract_fixed_rent');
        $start_billing->oec_cusa = $request->input('oec_cusa');
        $start_billing->oec_aircondition = $request->input('oec_aircondition');
        $start_billing->oec_processing = $request->input('oec_processing');
        $start_billing->contract_remarks = $request->input('contract_remarks');
        $start_billing->created_by = Auth::user()->id;
        $start_billing->created_datetime = Carbon::now();

        $start_billing->save();

        return $start_billing;
    }

    public function CancelStartBilling($id)
    {
        $start_billing = StartBilling::findOrFail($id);
        $start_billing->is_cancelled = 1;
        $start_billing->cancelled_by = Auth::user()->id;
        $start_billing->cancelled_datetime = Carbon::now();
        $start_billing->save();
        
        return $start_billing;
    }

    public function StopBillingForm(Request $request)
    {
        Validator::make($request->all(),
        [
            'trade_name' => 'required',
            'company_name' => 'required',
            'security_deposit' => 'required',
            'power_meter_deposit' => 'required',
            'accounting_department' => 'required'
        ])->validate();

        $stop_billing = new StopBilling;
        $stop_billing->stop_billing_no = DB::select("select CreateStopBillingNo() as stop_billing_no")[0]->stop_billing_no;
        $stop_billing->contract_id = $request->input('contract_id');
        $stop_billing->date = date('Y-m-d h:i:s', strtotime($request->input('date')));
        $stop_billing->trade_name = $request->input('trade_name');
        $stop_billing->company_name = $request->input('company_name');
        $stop_billing->termination_date = date('Y-m-d h:i:s', strtotime($request->input('termination_date')));
        $stop_billing->security_deposit = $request->input('security_deposit');
        $stop_billing->power_meter_deposit = $request->input('power_meter_deposit');
        $stop_billing->notes = $request->input('notes =');
        $stop_billing->accounting_department = $request->input('accounting_department');
        $stop_billing->accounting_department_other = $request->input('accounting_department_other');
        $stop_billing->created_by = Auth::user()->id;
        $stop_billing->created_datetime = Carbon::now();

        $stop_billing->save();

        return $stop_billing;
    }

    public function CancelStopBilling($id)
    {
        $stop_billing = StopBilling::findOrFail($id);
        $stop_billing->is_cancelled = 1;
        $stop_billing->cancelled_by = Auth::user()->id;
        $stop_billing->cancelled_datetime = Carbon::now();
        $stop_billing->save();
        
        return $stop_billing;
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
                'department_id' => 'required|not_in:0',
                'nature_of_business_id' => 'required|not_in:0',
                'location_id' => 'required|not_in:0',
                'contract_type_id' => 'required|not_in:0',
                'category_id' => 'required|not_in:0',
                'contract_terms' => 'required|not_in:0|numeric',
                'commencement_date' => 'required',
                'termination_date' => 'required',
                'start_billing_date' => 'required',
                'contract_floor_area' => 'required|not_in:0|numeric',
                'contract_fixed_rent' => 'required|not_in:0|numeric',
                'schedules' => 'required'
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'lessee/tenant',
            'department_id' => 'department',
            'nature_of_business_id' => 'nature of business',
            'location_id' => 'location',
            'contract_type_id' => 'contract type',
            'category_id' => 'category',
            'contract_floor_area' => 'app floor area',
            'contract_fixed_rent' => 'basic rental',
            'schedules' => 'schedule of payment']
        )->validate();

        $contract_info = new ContractInfo;
        $contract_schedule = new ContractSchedule;
        $contract_util_charges = new ContractUtilCharges;
        $contract_misc_charges = new ContractMiscCharges;
        $contract_othr_charges = new ContractOthrCharges;

        $contract_info->contract_no = DB::raw("CreateContractNo()");
        $contract_info->tenant_id = $request->input('tenant_id');
        $contract_info->contract_billing_address = $request->input('contract_billing_address');
        $contract_info->contract_type_id = $request->input('contract_type_id');
        $contract_info->category_id = $request->input('category_id');
        $contract_info->commencement_date = date("Y-m-d", strtotime($request->input('commencement_date')));
        $contract_info->termination_date = date('Y-m-d', strtotime($request->input('termination_date')));
        $contract_info->start_billing_date = date('Y-m-d', strtotime($request->input('start_billing_date')));
        $contract_info->location_id = $request->input('location_id');
        $contract_info->contract_signatory = $request->input('contract_signatory');
        $contract_info->schedule_type = $request->input('schedule_type');
        $contract_info->contract_terms = $request->input('contract_terms');
        $contract_info->contract_approved_merch = $request->input('contract_approved_merch');
        $contract_info->contract_fixed_rent = $request->input('contract_fixed_rent');
        $contract_info->contract_discounted_rent = $request->input('contract_discounted_rent');
        $contract_info->contract_advance_rent = $request->input('contract_advance_rent');
        $contract_info->water_meter_deposit = $request->input('water_meter_deposit');
        $contract_info->construction_deposit = $request->input('construction_deposit');
        $contract_info->power_meter_deposit = $request->input('power_meter_deposit');
        $contract_info->escalation_notes = $request->input('escalation_notes');
        $contract_info->contract_remarks = $request->input('contract_remarks');
        $contract_info->security_deposit = $request->input('security_deposit');
        $contract_info->escalation_type = $request->input('escalation_type');
        $contract_info->contract_escalation_percent = $request->input('contract_escalation_percent');
        $contract_info->contract_floor_area = $request->input('contract_floor_area');
        $contract_info->department_id = $request->input('department_id');
        $contract_info->nature_of_business_id = $request->input('nature_of_business_id');
        $contract_info->is_renewal = $request->input('is_renewal');
        $contract_info->is_active = $request->input('is_active');

        $contract_info->oec_electricity = $request->input('oec_electricity');
        $contract_info->oec_water = $request->input('oec_water');
        $contract_info->oec_marketing = $request->input('oec_marketing');
        $contract_info->oec_processing = $request->input('oec_processing');
        $contract_info->oec_aircondition = $request->input('oec_aircondition');
        $contract_info->oec_cusa = $request->input('oec_cusa');
        $contract_info->oec_repair_maintenance = $request->input('oec_repair_maintenance');
        $contract_info->oec_expense_utilities = $request->input('oec_expense_utilities');
        $contract_info->oec_security_deposit = $request->input('oec_security_deposit');

        $contract_info->created_datetime = Carbon::now();
        $contract_info->created_by = Auth::user()->id;
        
        if($contract_info->save()){
            $contract_id = $contract_info->contract_id;
            $schedules_dataSet = [];
            $utilities_dataSet = [];
            $miscs_dataSet = [];
            $others_dataSet = [];
            
            $schedules = $request->input('schedules');
            foreach($schedules as $schedule){
                $schedules_dataSet[] = [
                    'contract_id' => $contract_id,
                    'month_id' => $schedule['month_id'],
                    'app_year' => $schedule['app_year'],
                    'discounted_rent' => $schedule['discounted_rent'],
                    'fixed_rent' => $schedule['fixed_rent'],
                    'escalation_percent' => $schedule['escalation_percent'],
                    'escalation_amount' => $schedule['escalation_amount'],
                    'discounted_amount_due' => $schedule['discounted_amount_due'],
                    'amount_due' => $schedule['amount_due'],
                    'is_vatted' => $schedule['is_vatted'],
                    'contract_schedule_notes' => $schedule['contract_schedule_notes']
                ];
            }

            $utilities = $request->input('utilities');
            foreach($utilities as $utility){
                $utilities_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $utility['charge_id'],
                    'contract_util_rate' => $utility['contract_rate'],
                    'contract_util_default_reading' => $utility['contract_default_reading'],
                    'contract_util_is_vatted' => $utility['contract_is_vatted'],
                    'contract_util_notes' => $utility['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $miscs = $request->input('miscellaneous');
            foreach($miscs as $misc){
                $miscs_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $misc['charge_id'],
                    'contract_misc_rate' => $misc['contract_rate'],
                    'contract_misc_default_reading' => $misc['contract_default_reading'],
                    'contract_misc_is_vatted' => $misc['contract_is_vatted'],
                    'contract_misc_notes' => $misc['contract_notes'],
                    'sort_key' => $misc['sort_key']
                ];
            }

            $others = $request->input('other');
            foreach($others as $other){
                $others_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $other['charge_id'],
                    'contract_othr_rate' => $other['contract_rate'],
                    'contract_othr_default_reading' => $other['contract_default_reading'],
                    'contract_othr_is_vatted' => $other['contract_is_vatted'],
                    'contract_othr_notes' => $other['contract_notes'],
                    'sort_key' => $other['sort_key']
                ];
            }

            DB::table('b_contract_schedule')->insert($schedules_dataSet);
            DB::table('b_contract_util_charges')->insert($utilities_dataSet);
            DB::table('b_contract_misc_charges')->insert($miscs_dataSet);
            DB::table('b_contract_othr_charges')->insert($others_dataSet);
        }

        $data = ContractInfo::select(
                                    'b_contract_info.*',
                                    'b_refdepartments.*',
                                    'b_reflocations.*',
                                    'b_refcontracttype.*',
                                    'b_refcategory.*',
                                    'b_refnatureofbusiness.*',
                                    'b_tenants.*',
                                    DB::raw("IFNULL(a.c_advance_rent, 0) as c_advance_rent"),
                                    DB::raw("IFNULL(a.c_security_deposit, 0) as c_security_deposit"),
                                    DB::raw("IFNULL(a.c_electric_meter_deposit, 0) as c_electric_meter_deposit"),
                                    DB::raw("IFNULL(a.c_water_meter_deposit, 0) as c_water_meter_deposit"),
                                    DB::raw("IFNULL(a.c_construction_deposit, 0) as c_construction_deposit"),
                                    DB::raw("'' as start_billing"),
                                    DB::raw("'' as stop_billing")
                            )
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            ->leftJoin('b_refcontracttype', 'b_refcontracttype.contract_type_id', '=', 'b_contract_info.contract_type_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin(
                                DB::raw("
                                        (SELECT
                                            tenant_id,
                                            SUM(IF(x.fee_type_id = 1, x.amount, 0)) as c_advance_rent,
                                            SUM(IF(x.fee_type_id = 2, x.amount, 0)) as c_security_deposit,
                                            SUM(IF(x.fee_type_id = 3, x.amount, 0)) as c_electric_meter_deposit,
                                            SUM(IF(x.fee_type_id = 4, x.amount, 0)) as c_water_meter_deposit,
                                            SUM(IF(x.fee_type_id = 5, x.amount, 0)) as c_construction_deposit
                                        FROM
                                            (
                                                SELECT
                                                    cof.tenant_id,
                                                    fee_type_id,
                                                    IFNULL(SUM(fee_credit), 0) - IFNULL(SUM(fee_debit), 0) as amount
                                                FROM
                                                    b_contract_other_fees AS cof
                                                LEFT JOIN
                                                    b_payment_info as pi ON pi.payment_id = cof.payment_id
                                                LEFT JOIN
                                                    temp_journal_info AS tji ON tji.fee_id = cof.fee_id
                                                WHERE
                                                    if(cof.payment_id != 0, if(pi.is_sent = 1, 1, 0), tji.is_journal_posted) = 1
                                                GROUP BY  fee_type_id, tenant_id
                                            ) 
                                        AS x
                                        GROUP BY tenant_id
                                ) a" ), 'b_contract_info.tenant_id', '=', 'a.tenant_id')
                            ->findOrFail($contract_info->contract_id);

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
        $contract = ContractInfo::select(
                                        'b_contract_info.*',
                                        'b_tenants.*',
                                        DB::raw("IFNULL(a.c_advance_rent, 0) as c_advance_rent"),
                                        DB::raw("IFNULL(a.c_security_deposit, 0) as c_security_deposit"),
                                        DB::raw("IFNULL(a.c_electric_meter_deposit, 0) as c_electric_meter_deposit"),
                                        DB::raw("IFNULL(a.c_water_meter_deposit, 0) as c_water_meter_deposit"),
                                        DB::raw("IFNULL(a.c_construction_deposit, 0) as c_construction_deposit")
                                )
                                ->join('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                                ->leftJoin(
                                    DB::raw("
                                            (SELECT
                                                tenant_id,
                                                SUM(IF(x.fee_type_id = 1, x.amount, 0)) as c_advance_rent,
                                                SUM(IF(x.fee_type_id = 2, x.amount, 0)) as c_security_deposit,
                                                SUM(IF(x.fee_type_id = 3, x.amount, 0)) as c_electric_meter_deposit,
                                                SUM(IF(x.fee_type_id = 4, x.amount, 0)) as c_water_meter_deposit,
                                                SUM(IF(x.fee_type_id = 5, x.amount, 0)) as c_construction_deposit
                                            FROM
                                                (
                                                    SELECT
                                                        cof.tenant_id,
                                                        fee_type_id,
                                                        IFNULL(SUM(fee_credit), 0) - IFNULL(SUM(fee_debit), 0) as amount
                                                    FROM
                                                        b_contract_other_fees AS cof
                                                    LEFT JOIN
														b_payment_info as pi ON pi.payment_id = cof.payment_id
                                                    LEFT JOIN
                                                        temp_journal_info AS tji ON tji.fee_id = cof.fee_id
                                                    WHERE
                                                        if(cof.payment_id != 0, if(pi.is_sent = 1, 1, 0), tji.is_journal_posted) = 1
                                                    GROUP BY  fee_type_id, tenant_id
                                                ) 
                                            AS x
                                            GROUP BY tenant_id
                                    ) a" ), 'b_contract_info.tenant_id', '=', 'a.tenant_id')
                                ->findOrFail($id);

        return ( new Reference( $contract ) )
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
                'department_id' => 'required|not_in:0',
                'nature_of_business_id' => 'required|not_in:0',
                'location_id' => 'required|not_in:0',
                'contract_type_id' => 'required|not_in:0',
                'category_id' => 'required|not_in:0',
                'contract_terms' => 'required|not_in:0|numeric',
                'commencement_date' => 'required',
                'termination_date' => 'required',
                'start_billing_date' => 'required',
                'contract_floor_area' => 'required|not_in:0|numeric',
                'contract_fixed_rent' => 'required|not_in:0|numeric',
                'schedules' => 'required'
            ],  ['not_in' => 'The :attribute field is required.']
        )->setAttributeNames([
            'tenant_id' => 'lessee/tenant',
            'department_id' => 'department',
            'nature_of_business_id' => 'nature of business',
            'location_id' => 'location',
            'contract_type_id' => 'contract type',
            'category_id' => 'category',
            'contract_floor_area' => 'app floor area',
            'contract_fixed_rent' => 'basic rental',
            'schedules' => 'schedule of payment']
        )->validate();

        $contract_info = ContractInfo::findOrFail($request->input('contract_id'));
        $contract_schedule = new ContractSchedule;
        $contract_util_charges = new ContractUtilCharges;
        $contract_misc_charges = new ContractMiscCharges;
        $contract_othr_charges = new ContractOthrCharges;

        $contract_info->tenant_id = $request->input('tenant_id');
        $contract_info->contract_billing_address = $request->input('contract_billing_address');
        $contract_info->contract_type_id = $request->input('contract_type_id');
        $contract_info->category_id = $request->input('category_id');
        $contract_info->commencement_date = date("Y-m-d", strtotime($request->input('commencement_date')));
        $contract_info->termination_date = date('Y-m-d', strtotime($request->input('termination_date')));
        $contract_info->start_billing_date = date('Y-m-d', strtotime($request->input('start_billing_date')));
        $contract_info->location_id = $request->input('location_id');
        $contract_info->contract_signatory = $request->input('contract_signatory');
        $contract_info->schedule_type = $request->input('schedule_type');
        $contract_info->contract_terms = $request->input('contract_terms');
        $contract_info->contract_approved_merch = $request->input('contract_approved_merch');
        $contract_info->contract_fixed_rent = $request->input('contract_fixed_rent');
        $contract_info->contract_discounted_rent = $request->input('contract_discounted_rent');
        $contract_info->contract_advance_rent = $request->input('contract_advance_rent');
        $contract_info->water_meter_deposit = $request->input('water_meter_deposit');
        $contract_info->construction_deposit = $request->input('construction_deposit');
        $contract_info->power_meter_deposit = $request->input('power_meter_deposit');
        $contract_info->escalation_notes = $request->input('escalation_notes');
        $contract_info->contract_remarks = $request->input('contract_remarks');
        $contract_info->security_deposit = $request->input('security_deposit');
        $contract_info->escalation_type = $request->input('escalation_type');
        $contract_info->contract_escalation_percent = $request->input('contract_escalation_percent');
        $contract_info->contract_floor_area = $request->input('contract_floor_area');
        $contract_info->department_id = $request->input('department_id');
        $contract_info->nature_of_business_id = $request->input('nature_of_business_id');
        $contract_info->is_renewal = $request->input('is_renewal');
        $contract_info->is_active = $request->input('is_active');

        $contract_info->oec_electricity = $request->input('oec_electricity');
        $contract_info->oec_water = $request->input('oec_water');
        $contract_info->oec_marketing = $request->input('oec_marketing');
        $contract_info->oec_processing = $request->input('oec_processing');
        $contract_info->oec_aircondition = $request->input('oec_aircondition');
        $contract_info->oec_cusa = $request->input('oec_cusa');
        $contract_info->oec_repair_maintenance = $request->input('oec_repair_maintenance');
        $contract_info->oec_expense_utilities = $request->input('oec_expense_utilities');
        $contract_info->oec_security_deposit = $request->input('oec_security_deposit');

        $contract_info->modified_datetime = Carbon::now();
        $contract_info->modified_by = Auth::user()->id;
        
        if($contract_info->save()){
            $contract_id = $contract_info->contract_id;
            $schedules_dataSet = [];
            $utilities_dataSet = [];
            $miscs_dataSet = [];
            $others_dataSet = [];
            
            $old_schedules = ContractSchedule::where('contract_id', $contract_id);
            $old_schedules->delete();

            $old_utilities = ContractUtilCharges::where('contract_id', $contract_id);
            $old_utilities->delete();

            $old_miscs = ContractMiscCharges::where('contract_id', $contract_id);
            $old_miscs->delete();

            $old_other = ContractOthrCharges::where('contract_id', $contract_id);
            $old_other->delete();

            $schedules = $request->input('schedules');
            foreach($schedules as $schedule){
                $schedules_dataSet[] = [
                    'contract_id' => $contract_id,
                    'month_id' => $schedule['month_id'],
                    'app_year' => $schedule['app_year'],
                    'discounted_rent' => $schedule['discounted_rent'],
                    'fixed_rent' => $schedule['fixed_rent'],
                    'escalation_percent' => $schedule['escalation_percent'],
                    'escalation_amount' => $schedule['escalation_amount'],
                    'discounted_amount_due' => $schedule['discounted_amount_due'],
                    'amount_due' => $schedule['amount_due'],
                    'is_vatted' => $schedule['is_vatted'],
                    'contract_schedule_notes' => $schedule['contract_schedule_notes']
                ];
            }

            $utilities = $request->input('utilities');
            foreach($utilities as $utility){
                $utilities_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $utility['charge_id'],
                    'contract_util_rate' => $utility['contract_rate'],
                    'contract_util_default_reading' => $utility['contract_default_reading'],
                    'contract_util_is_vatted' => $utility['contract_is_vatted'],
                    'contract_util_notes' => $utility['contract_notes'],
                    'sort_key' => $utility['sort_key']
                ];
            }

            $miscs = $request->input('miscellaneous');
            foreach($miscs as $misc){
                $miscs_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $misc['charge_id'],
                    'contract_misc_rate' => $misc['contract_rate'],
                    'contract_misc_default_reading' => $misc['contract_default_reading'],
                    'contract_misc_is_vatted' => $misc['contract_is_vatted'],
                    'contract_misc_notes' => $misc['contract_notes'],
                    'sort_key' => $misc['sort_key']
                ];
            }

            $others = $request->input('other');
            foreach($others as $other){
                $others_dataSet[] = [
                    'contract_id' => $contract_id,
                    'charge_id' => $other['charge_id'],
                    'contract_othr_rate' => $other['contract_rate'],
                    'contract_othr_default_reading' => $other['contract_default_reading'],
                    'contract_othr_is_vatted' => $other['contract_is_vatted'],
                    'contract_othr_notes' => $other['contract_notes'],
                    'sort_key' => $other['sort_key']
                ];
            }

            DB::table('b_contract_schedule')->insert($schedules_dataSet);
            DB::table('b_contract_util_charges')->insert($utilities_dataSet);
            DB::table('b_contract_misc_charges')->insert($miscs_dataSet);
            DB::table('b_contract_othr_charges')->insert($others_dataSet);
        }

        $data = ContractInfo::select(
                                    'b_contract_info.*',
                                    'b_refdepartments.*',
                                    'b_reflocations.*',
                                    'b_refcontracttype.*',
                                    'b_refcategory.*',
                                    'b_refnatureofbusiness.*',
                                    'b_tenants.*',
                                    DB::raw("IFNULL(a.c_advance_rent, 0) as c_advance_rent"),
                                    DB::raw("IFNULL(a.c_security_deposit, 0) as c_security_deposit"),
                                    DB::raw("IFNULL(a.c_electric_meter_deposit, 0) as c_electric_meter_deposit"),
                                    DB::raw("IFNULL(a.c_water_meter_deposit, 0) as c_water_meter_deposit"),
                                    DB::raw("IFNULL(a.c_construction_deposit, 0) as c_construction_deposit"),
                                    DB::raw("'' as start_billing"),
                                    DB::raw("'' as stop_billing")
                            )
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            ->leftJoin('b_refcontracttype', 'b_refcontracttype.contract_type_id', '=', 'b_contract_info.contract_type_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin(
                                DB::raw("
                                        (SELECT
                                            tenant_id,
                                            SUM(IF(x.fee_type_id = 1, x.amount, 0)) as c_advance_rent,
                                            SUM(IF(x.fee_type_id = 2, x.amount, 0)) as c_security_deposit,
                                            SUM(IF(x.fee_type_id = 3, x.amount, 0)) as c_electric_meter_deposit,
                                            SUM(IF(x.fee_type_id = 4, x.amount, 0)) as c_water_meter_deposit,
                                            SUM(IF(x.fee_type_id = 5, x.amount, 0)) as c_construction_deposit
                                        FROM
                                            (
                                                SELECT
                                                    cof.tenant_id,
                                                    fee_type_id,
                                                    IFNULL(SUM(fee_credit), 0) - IFNULL(SUM(fee_debit), 0) as amount
                                                FROM
                                                    b_contract_other_fees AS cof
                                                LEFT JOIN
                                                    b_payment_info as pi ON pi.payment_id = cof.payment_id
                                                LEFT JOIN
                                                    temp_journal_info AS tji ON tji.fee_id = cof.fee_id
                                                WHERE
                                                    if(cof.payment_id != 0, if(pi.is_sent = 1, 1, 0), tji.is_journal_posted) = 1
                                                GROUP BY  fee_type_id, tenant_id
                                            ) 
                                        AS x
                                        GROUP BY tenant_id
                                ) a" ), 'b_contract_info.tenant_id', '=', 'a.tenant_id')
                            ->findOrFail($contract_info->contract_id);

        return ( new Reference($data) )
            ->response()
            ->setStatusCode(201);
        
    }
    
    public function preterminate(Request $request, $id)
    {
        $contract = ContractInfo::findOrFail($id);
        $contract->status = 3;
        $contract->pre_terminated_by = Auth::user()->id;
        $contract->pre_terminated_datetime = Carbon::now();
        $contract->pre_terminated_remarks = $request->input('pre_terminated_remarks');
        $contract->save();
        
        return ( new Reference( $contract ) )
            ->response()
            ->setStatusCode(200);
    }

    public function terminate(Request $request, $id)
    {
        $contract = ContractInfo::findOrFail($id);
        $contract->status = 4;
        $contract->terminated_by = Auth::user()->id;
        $contract->terminated_datetime = Carbon::now();
        $contract->terminated_remarks = $request->input('terminated_remarks');
        $contract->save();
        
        return ( new Reference( $contract ) )
            ->response()
            ->setStatusCode(200);
    }

    public function activate(Request $request, $id)
    {
        $contract = ContractInfo::findOrFail($id);
        $contract->status = 1;
        $contract->activated_by = Auth::user()->id;
        $contract->activated_datetime = Carbon::now();
        $contract->activated_remarks = $request->input('activated_remarks');
        $contract->save();
        
        return ( new Reference( $contract ) )
            ->response()
            ->setStatusCode(200);
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
        $contract = ContractInfo::findOrFail($id);
        $contract->is_deleted = 1;
        $contract->deleted_datetime = Carbon::now();
        $contract->deleted_by = Auth::user()->id;

        //update classification based on the http json body that is sent
        $contract->save();

        return ( new Reference( $contract ) )
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

        if(BillingInfo::where('contract_id', '=', $id)
            ->where('is_deleted', 0)
            ->exists()) {
            $exists = 'true';
        }
        
        return $exists;
    }
}

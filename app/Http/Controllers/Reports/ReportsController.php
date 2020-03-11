<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Resources\Reference;
use App\Models\References\Location;
use App\Models\References\Months;
use App\Models\Transactions\ContractInfo;
use App\Models\Utilities\CompanySettings;
use App\Models\Utilities\SoaNotes;
use App\Models\References\Department;
use App\Models\References\BillingPeriod;
use App\Models\References\Tenants;
use App\Models\Transactions\BillingInfo;
use App\Models\Transactions\BillingSchedule;
use App\Models\Transactions\BillingUtilCharges;
use App\Models\Transactions\BillingMiscCharges;
use App\Models\Transactions\BillingOthrCharges;
use App\Models\Transactions\BillingAdjustments;
use App\Models\Transactions\PaymentInfo;
use App\Models\Transactions\ChargeSlip;
use App\Models\Transactions\StartBilling;
use App\Models\Transactions\StopBilling;
use App\User;
use DB;
use Carbon\Carbon;
use Mpdf\Mpdf;

ini_set('max_execution_time', 300); //300 seconds = 5 minutes
set_time_limit(300);

class ReportsController extends Controller
{
    public function PreviewSoa($billing_id)
    {
        // $data['company_info'] = CompanySettings::findOrFail(1);
        // $data['notes'] = SoaNotes::all();
        $data['billing'] = BillingInfo::select(
                            '*', 
                            'b_billing_info.vat_percent',
                            DB::raw('IF(b_refdepartments.end_day = 0, day(last_day(DATE(CONCAT(b_refbillingperiod.app_year, "-", b_refbillingperiod.month_id,"-", 1)))), end_day) as end_day')
                        )
                        ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                        ->leftJoin('b_refdepartments', 'b_contract_info.department_id', '=', 'b_refdepartments.department_id')
                        ->leftJoin('b_refbillingperiod', 'b_refbillingperiod.period_id', '=', 'b_billing_info.period_id')
                        ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                        ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                        ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                        ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                        ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                        ->findOrFail($billing_id);
        $data['previous_balance'] = DB::select("select GetPreviousBalance(".$data['billing']->month_id.", ".$data['billing']->app_year.", ".$data['billing']->tenant_id.", ".$data['billing']->department_id.") as previous_balance");
        $data['as_of_balance'] = DB::select("select GetAsOfBalance(".$data['billing']->month_id.", ".$data['billing']->app_year.", ".$data['billing']->tenant_id.", ".$data['billing']->department_id.") as as_of_balance");

        DB::statement(DB::raw('set @row=0'));
        $data['schedules'] = BillingSchedule::select(
                        'b_billing_schedule.*',
                        'b_billing_schedule.billing_schedule_notes as contract_schedule_notes',
                        'discounted_line_total as discounted_amount_due',
                        'line_total as amount_due',
                        'b_refmonths.*',
                        DB::raw("@row := @row + 1 as count")
                    )
                    ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_schedule.month_id')
                    ->where('billing_id', $billing_id)->get();
        $data['utilities'] = BillingUtilCharges::select(
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
                    ->where('billing_id', $billing_id)->orderBy('sort_key', 'asc')->get();
        $data['miscellaneous'] = BillingMiscCharges::select(
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
                    ->where('billing_id', $billing_id)->orderBy('sort_key', 'asc')->get();
        $data['other'] = BillingOthrCharges::select(
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
                    ->where('billing_id', $billing_id)->orderBy('sort_key', 'asc')->get();

        $data['adjustments'] = BillingAdjustments::select(
                        'billing_adjustment_id',
                        'billing_id',
                        'billing_adjustment_rate as contract_rate',
                        'billing_adjustment_reading as contract_default_reading',
                        'billing_adjustment_is_vatted as contract_is_vatted',
                        'billing_adjustment_notes as contract_notes',
                        'billing_adjustment_line_total',
                        'billing_adjustment_type',
                        'sort_key',
                        'b_refcharges.charge_id',
                        'b_refcharges.charge_desc'
                    )
                    ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_adjustments.charge_id')
                    ->where('billing_id', $billing_id)->orderBy('sort_key', 'asc')->get();

        $payments = PaymentInfo::select(
            'transaction_no',
            'reference_no',
            'payment_type',
            'amount_paid as payment',
            'used_advances',
            'wtax_amount',
            DB::raw('"Payment" as trans_type'),
            'payment_date',
            'ct.check_type_desc',
            'check_no',
            'check_date'
        )
            ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')
            ->whereRaw('DATE(payment_date) BETWEEN DATE(DATE_ADD("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-'.$data['billing']->start_day.'", INTERVAL -1 MONTH)) AND  DATE("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-'.$data['billing']->end_day.'")')
            ->where('is_canceled', 0)
            ->where('tenant_id', $data['billing']->tenant_id)
            ->whereRaw('amount_paid + carried_advance + used_advances + wtax_amount > 0');

        // $discounts = PaymentInfo::select(
        //                         'transaction_no',
        //                         'reference_no',
        //                         'payment_type',
        //                         'discount as payment',
        //                         'used_advances',
        //                         'wtax_amount',
        //                         DB::raw('"Discount" as trans_type'),
        //                         'payment_date',
        //                         'ct.check_type_desc',
        //                         'check_no',
        //                         'check_date'
        // )
        //                         ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')
        //                         ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-20")')
        //                         ->where('is_canceled', 0)
        //                         ->where('tenant_id', $data['billing']->tenant_id)
        //                         ->where('discount', '>', 0);

        $data['payments'] = $payments->get();
        
        $mpdf = new Mpdf();
        // return $data;
        // return view('reports.soa')->with($data);
        $content = view('reports.soa')->with($data);
        // return $content;
        // print directly
        // $mpdf->SetJS('this.print();');
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();

        // return (new Reference( $data ))
        //     ->response()
        //     ->setStatusCode(200);
    }

    public function PreviewAllSoa($period_id)
    {
        $mpdf = new Mpdf();
        // $data['company_info'] = CompanySettings::findOrFail(1);
        // $data['notes'] = SoaNotes::all();
        $billings = BillingInfo::select(
                                '*', 
                                'b_billing_info.vat_percent',
                                DB::raw('IF(b_refdepartments.end_day = 0, day(last_day(DATE(CONCAT(b_refbillingperiod.app_year, "-", b_refbillingperiod.month_id,"-", 1)))), end_day) as end_day')
                        )
                        ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_billing_info.contract_id')
                        ->leftJoin('b_refdepartments', 'b_contract_info.department_id', '=', 'b_refdepartments.department_id')
                        ->leftJoin('b_refbillingperiod', 'b_refbillingperiod.period_id', '=', 'b_billing_info.period_id')
                        ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                        ->leftJoin('b_refmonths', 'b_refmonths.month_id', '=', 'b_refbillingperiod.month_id')
                        ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                        ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                        ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                        ->where('b_billing_info.is_deleted', 0)
                        ->where('b_billing_info.period_id',$period_id)->get();
        $x = 0;
        foreach($billings as $data['billing'])
        {
            $x++;
            $data['previous_balance'] = DB::select("select GetPreviousBalance(".$data['billing']->month_id.", ".$data['billing']->app_year.", ".$data['billing']->tenant_id.", ".$data['billing']->department_id.") as previous_balance");
            $data['as_of_balance'] = DB::select("select GetAsOfBalance(".$data['billing']->month_id.", ".$data['billing']->app_year.", ".$data['billing']->tenant_id.",".$data['billing']->department_id.") as as_of_balance");

            DB::statement(DB::raw('set @row=0'));
            $data['schedules'] = BillingSchedule::select(
                            'b_billing_schedule.*',
                            'b_billing_schedule.billing_schedule_notes as contract_schedule_notes',
                            'discounted_line_total as discounted_amount_due',
                            'line_total as amount_due',
                            'b_refmonths.*',
                            DB::raw("@row := @row + 1 as count")
                        )
                        ->join('b_refmonths', 'b_refmonths.month_id', '=', 'b_billing_schedule.month_id')
                        ->where('billing_id', $data['billing']->billing_id)->get();
            $data['utilities'] = BillingUtilCharges::select(
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
                        ->where('billing_id', $data['billing']->billing_id)->orderBy('sort_key', 'asc')->get();
            $data['miscellaneous'] = BillingMiscCharges::select(
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
                        ->where('billing_id', $data['billing']->billing_id)->orderBy('sort_key', 'asc')->get();
            $data['other'] = BillingOthrCharges::select(
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
                        ->where('billing_id', $data['billing']->billing_id)->orderBy('sort_key', 'asc')->get();

            $data['adjustments'] = BillingAdjustments::select(
                            'billing_adjustment_id',
                            'billing_id',
                            'billing_adjustment_rate as contract_rate',
                            'billing_adjustment_reading as contract_default_reading',
                            'billing_adjustment_is_vatted as contract_is_vatted',
                            'billing_adjustment_notes as contract_notes',
                            'billing_adjustment_line_total',
                            'billing_adjustment_type',
                            'sort_key',
                            'b_refcharges.charge_id',
                            'b_refcharges.charge_desc'
                        )
                        ->join('b_refcharges', 'b_refcharges.charge_id', '=', 'b_billing_adjustments.charge_id')
                        ->where('billing_id', $data['billing']->billing_id)->orderBy('sort_key', 'asc')->get();

            $payments = PaymentInfo::select(
                'transaction_no',
                'reference_no',
                'payment_type',
                'amount_paid as payment',
                'used_advances',
                'wtax_amount',
                DB::raw('"Payment" as trans_type'),
                'payment_date',
                'ct.check_type_desc',
                'check_no',
                'check_date'
            )
                ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')

                ->whereRaw('DATE(payment_date) BETWEEN DATE(DATE_ADD("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-'.$data['billing']->start_day.'", INTERVAL -1 MONTH)) AND  DATE("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-'.$data['billing']->end_day.'")')

                // ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-20")')
                ->where('is_canceled', 0)
                ->where('tenant_id', $data['billing']->tenant_id)
                ->whereRaw('amount_paid + carried_advance + used_advances + wtax_amount > 0');

            // $discounts = PaymentInfo::select(
            //                         'transaction_no',
            //                         'reference_no',
            //                         'payment_type',
            //                         'discount as payment',
            //                         'used_advances',
            //                         'wtax_amount',
            //                         DB::raw('"Discount" as trans_type'),
            //                         'payment_date',
            //                         'ct.check_type_desc',
            //                         'check_no',
            //                         'check_date'
            // )
            //                         ->leftJoin('b_refchecktype as ct','ct.check_type_id', '=', 'b_payment_info.check_type_id')
            //                         ->whereRaw('payment_date BETWEEN DATE(DATE_ADD("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-21", INTERVAL -1 MONTH)) AND  DATE("'.$data['billing']->app_year.'-'.$data['billing']->month_id.'-20")')
            //                         ->where('is_canceled', 0)
            //                         ->where('tenant_id', $data['billing']->tenant_id)
            //                         ->where('discount', '>', 0);

            $data['payments'] = $payments->get();
            $content = view('reports.soa')->with($data);
            // return $content;
            // print directly
            // $mpdf->SetJS('this.print();');
            // Write some HTML code:
            $mpdf->WriteHTML($content);
            if($x != count($billings))
            {
                $mpdf->AddPage();
            }
        }
        
        
        // return $data;
        // return view('reports.soa')->with($data);
        

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }


    public function PreviewAckReceipt($payment_id, $user_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['payments'] = PaymentInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_refchecktype', 'b_refchecktype.check_type_id', '=', 'b_payment_info.check_type_id')
                            ->findOrFail($payment_id);
        $data['amount_paid_words'] = $this->convertNumberToWord($data['payments']->amount_paid);
        $data['user'] = User::findOrFail($user_id)->username;
        // return $data;
        $mpdf = new \Mpdf\Mpdf();
        // return $data;
        $content = view('reports.ackreceipt')->with($data);
        // return $content;
        // Write some HTML code:
        
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();

    }

    public function tenantsPerSquareMeter($location_id)
    {
        // return DB::select('CALL tenants_per_sqm_rate('.$location_id.')');
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['tenants'] = DB::select('CALL tenants_per_sqm_rate('.$location_id.')');
        $data['location_desc'] = 'ALL';
        if($location_id != 0)
        {
            $data['location_desc'] = Location::findOrFail($location_id)->location_desc;
        }
        $mpdf = new \Mpdf\Mpdf();
        // return $data;
        $content = view('reports.tenantspersqmrate')->with($data);
        
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function tenantsMasterList()
    {
        $data = Tenants::select(
                    'tenant_code',
                    'trade_name',
                    'company_name',
                    'business_concept',
                    'head_office_address',
                    'billing_address',
                    'contact_person',
                    'designation',
                    'contact_number',
                    'email_address',
                    'tin_number'
        )
                    ->where('is_deleted', 0)
                    ->orderBy('tenant_id', 'desc')
                    ->get();

        if(count($data) > 0){
            $data = json_decode( json_encode($data), true);
            $inputFileName = 'uploads/tenantsmasterlist.xlsx';
            
            $spreadsheet =  PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->setCellValue(
                'A1',
                'TENANTS MASTERLIST'
            )
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);
            
            $spreadsheet->getActiveSheet()
            ->fromArray(
                $data, 
                NULL,    
                'A6'        
            ); 

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="data.xlsx"'); /*-- $filename is  xsl filename ---*/
            header('Cache-Control: max-age=0');
    
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    public function contractsMasterList($order_by, $order_type)
    {
        $data = DB::select('CALL contracts_master_list_v2("'.$order_by.'","'.$order_type.'")');
    
        // return array_unique($data->Department);

        if(count($data) > 0){        

            $data = json_decode( json_encode($data), true);
            
            foreach($data[0] as $key => $value)
            {
                if($key != 'department_id' && $key != 'department_desc' && $key != 'location_id' && $key != 'location_desc')
                {
                    $mykey[] = $key;
                }
            }

            $inputFileName = 'uploads/contractsmasterlist.xlsx';
            
            $spreadsheet =  PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->setCellValue(
                'A1',
                'Contracts Masterlist'
            )
            ->getStyle('A1')
            ->getFont()
            ->setBold(true);

            $temp_department_id = "";
            $temp_location_id = "";
            $i = 5;
            foreach($data as $d)
            {
                $x = "A";
                if($temp_department_id != $d['department_id'])
                {
                    $temp_department_id = $d['department_id'];
                    $spreadsheet->getActiveSheet()->setCellValue(
                        $x.$i,
                        $d['department_desc']
                    )
                    ->getStyle($x.$i)
                    ->getFont()
                    ->setBold(true);
                    $i++;
                    $spreadsheet->getActiveSheet()
                    ->fromArray(
                        $mykey,  
                        NULL,
                        'C'.$i      
                    )
                    ->getStyle('C'.$i.':ZZ'.$i)
                    ->getFont()
                    ->setBold(true);
                    $i++;
                }
                $x++;
                if($temp_department_id == $d['department_id'] && $temp_location_id != $d['location_id'])
                {
                    $temp_location_id = $d['location_id'];
                    $spreadsheet->getActiveSheet()->setCellValue(
                        $x.$i,
                        $d['location_desc']
                    )
                    ->getStyle($x.$i)
                    ->getFont()
                    ->setBold(true);
                    $i++;
                }
                $x++;
                foreach($data[0] as $key => $value)
                {
                    if($key != 'department_id' && $key != 'department_desc' && $key != 'location_id' && $key != 'location_desc')
                    {
                        $spreadsheet->getActiveSheet()->setCellValue(
                            $x.$i,
                            $d[$key]
                        );
                        $x++;
                    }
                }
                $i++;
            }
            // $spreadsheet->getActiveSheet()
            // ->fromArray(
            //     $mykey,  
            //     NULL,
            //     'A5'         
            // );
            // $spreadsheet->getActiveSheet()
            // ->fromArray(
            //     $data, 
            //     NULL,    
            //     'A6'        
            // ); 

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="data.xlsx"'); /*-- $filename is  xsl filename ---*/
            header('Cache-Control: max-age=0');
    
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }

    public function BillingForecast($period_id, $department_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $period = BillingPeriod::findOrFail($period_id);
        $contracts = ContractInfo::join('b_tenants as t', 't.tenant_id', '=', 'b_contract_info.tenant_id')
                        ->join('b_refdepartments as d', 'd.department_id', '=', 'b_contract_info.department_id')
                        ->where('b_contract_info.is_deleted', 0)
                        ->where('b_contract_info.is_active', 1)
                        ->whereRaw('IF('.$department_id.'= 0, 0=0, b_contract_info.department_id ='.$department_id.')')
                        // ->where('b_contract_info.status', 1)
                        ->whereRaw("'".$period->app_year."-".$period->month_id."' BETWEEN date_format(commencement_date,'%Y-%m') AND date_format(termination_date,'%Y-%m')");
        
        $data['departments'] = $contracts->select(DB::raw('distinct d.department_id, d.department_desc'))->get();
        $data['contracts'] = $contracts->select(
                                't.tenant_code',
                                't.trade_name',
                                'contract_no',
                                'commencement_date',
                                'termination_date',
                                'department_desc',
                                'd.department_id'
                            )
                            ->get();
        $data['period'] = $period;

        // return $data;

        $mpdf = new \Mpdf\Mpdf();
        // return $data;
        $content = view('reports.billingforecast')->with($data);
        
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $content = $mpdf->Output();
    }

    public function rentalAndCharges($location_id, $app_year, $app_month, $charge_type)
    {
        $data =  DB::select('CALL rental_and_charges("'.$location_id.'","'.$app_year.'","'.$app_month.'","'.$charge_type.'")');

        if(count($data) > 0){        
            $month = Months::findOrFail($app_month);
            $month_name = $month->month_name;

            $location_desc = 'All';
            if($location_id != 0){
                $location = Location::findOrFail($location_id);
                $location_desc = $location->location_desc;
            }

            if($charge_type == 0)
            {
                $text = 'All';
            }

            else if($charge_type == 1)
            {
                $text = 'Utility';
            }

            else if($charge_type == 2)
            {
                $text = 'Miscellaneous';
            }

            else
            {
                $text = 'Other';
            }

            $data = json_decode( json_encode($data), true);

            foreach($data[0] as $key => $value)
            {
                $mykey[] = $key;
            }

            $inputFileName = 'uploads/rentalandcharges.xlsx';
            
            $spreadsheet =  PhpSpreadsheet\IOFactory::load($inputFileName);
            $spreadsheet->getActiveSheet()->mergeCells('A1:J1');
            $spreadsheet->getActiveSheet()->mergeCells('A2:J2');
            $spreadsheet->getActiveSheet()->mergeCells('A3:J3');
            $spreadsheet->getActiveSheet()->setCellValue(
                'A1',
                'Rental Rates and '.$text.' Charges'
            );
            $spreadsheet->getActiveSheet()->setCellValue(
                'A2',
                'Location : '.$location_desc
            );
            $spreadsheet->getActiveSheet()->setCellValue(
                'A3',
                'Applicable Date : '.$month_name.' '.$app_year 
            );
            $spreadsheet->getActiveSheet()
            ->fromArray(
                $mykey,  
                NULL,
                'A5'         
            );
            $spreadsheet->getActiveSheet()
            ->fromArray(
                $data, 
                NULL,    
                'A6'        
            ); 

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="data.xlsx"'); /*-- $filename is  xsl filename ---*/
            header('Cache-Control: max-age=0');
    
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    }
    
    public function expiringContractsList()
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays(45);
        $data['expiring_contracts'] = ContractInfo::leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            // ->whereDate('termination_date', '<=', $from)
                            ->orWhereBetween('termination_date', [$from, $to])
                            ->where('b_contract_info.is_deleted', 0)
                            ->get();

        $data['company_info'] = CompanySettings::findOrFail(1);
        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.expiringcontracts')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function newContractsList()
    {
        $data['new_contracts'] = ContractInfo::leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                            ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                            ->leftJoin('b_refcategory', 'b_refcategory.category_id', '=', 'b_contract_info.category_id')
                            ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                            ->where('b_contract_info.is_deleted', 0)
                            ->where('is_renewal', 0)
                            ->where('renewed', 0)
                            ->get();

        $data['company_info'] = CompanySettings::findOrFail(1);
        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.newcontracts')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function ChargeSlip($charge_slip_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['charge_slip'] = ChargeSlip::select(
                                    'b_charge_slip.*',
                                    'b_tenants.trade_name',
                                    'b_refcharges.charge_desc'
                                )
                                ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_charge_slip.tenant_id')
                                ->leftJoin('b_refcharges', 'b_refcharges.charge_id', '=', 'b_charge_slip.charge_id')
                                ->findOrFail($charge_slip_id);

        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.chargeslip')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function ContractProposal($contract_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['contract'] = ContractInfo::leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_contract_info.tenant_id')
                                ->leftJoin('b_refnatureofbusiness', 'b_refnatureofbusiness.nature_of_business_id', '=', 'b_contract_info.nature_of_business_id')
                                ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                                ->leftJoin('b_reflocations', 'b_reflocations.location_id', '=', 'b_contract_info.location_id')
                                ->findOrFail($contract_id);

        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.contractproposal')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function CollectionReport($date_from, $date_to, $department_id, $type)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['payments'] = PaymentInfo::select(
                            'b_payment_info.*',
                            'b_tenants.trade_name',
                            'b_contract_info.department_id',
                            DB::raw("CASE payment_type
                                        WHEN 0 THEN 'Cash'
                                        WHEN 1 THEN 'Check'
                                        WHEN 2 THEN 'Online'
                                        ELSE 'Withholding'
                                    END as payment_type_desc")
                            )
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_contract_info', 'b_contract_info.tenant_id', '=', 'b_tenants.tenant_id')
                            ->whereRaw('DATE(payment_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
                            ->whereRaw('IF('.$department_id.' = 0, 0=0, b_contract_info.department_id = '.$department_id.')')
                            ->whereRaw('IF('.$type.' = 0, 0=0, IF('.$type.' = 1, b_payment_info.payment_type != 3, b_payment_info.payment_type = 3))')
                            ->where('b_contract_info.is_active', 1)
                            ->groupBy('payment_id')
                            ->get();
        $data['departments'] = PaymentInfo::select(DB::raw('distinct d.department_id, d.department_desc'))
                            ->leftJoin('b_tenants', 'b_tenants.tenant_id', '=', 'b_payment_info.tenant_id')
                            ->leftJoin('b_contract_info', 'b_contract_info.tenant_id', '=', 'b_tenants.tenant_id')
                            ->leftJoin('b_refdepartments as d', 'd.department_id', '=', 'b_contract_info.department_id')
                            ->whereRaw('DATE(payment_date) BETWEEN "'.$date_from.'" AND "'.$date_to.'"')
                            ->whereRaw('IF('.$department_id.' = 0, 0=0, b_contract_info.department_id = '.$department_id.')')
                            ->where('b_contract_info.is_active', 1)
                            ->groupBy('payment_id')
                            ->get();

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        if($department_id == 0)
        {
            $data['department_desc'] = "All Departments";
        }
        else
        {
            $data['departments'] = Department::where('department_id', $department_id)->get();

        }

        $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
        $content = view('reports.collectionreport')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function StartBillingForm($start_billing_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['start_billing'] = StartBilling::select('b_start_billing.*', 'b_refdepartments.department_desc')
                                            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_start_billing.contract_id')
                                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                                            ->findOrFail($start_billing_id);

        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.startbillingform')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
    }

    public function StopBillingForm($stop_billing_id)
    {
        $data['company_info'] = CompanySettings::findOrFail(1);
        $data['stop_billing'] = StopBilling::select('b_stop_billing.*', 'b_refdepartments.department_desc')
                                            ->leftJoin('b_contract_info', 'b_contract_info.contract_id', '=', 'b_stop_billing.contract_id')
                                            ->leftJoin('b_refdepartments', 'b_refdepartments.department_id', '=', 'b_contract_info.department_id')
                                            ->findOrFail($stop_billing_id);

        $mpdf = new \Mpdf\Mpdf();
        $content = view('reports.stopbillingform')->with($data);
        // Write some HTML code:
        $mpdf->WriteHTML($content);

        // Output a PDF file directly to the browser
        $mpdf->Output();
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

    public function sendEmail()
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
        ->setUsername('patawaranjason17@gmail.com')
        ->setPassword("bmlxotivleedtqwv")
        ->setStreamOptions(array('ssl' => array('allow_self_signed' => true, 'verify_peer' => false)));

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        
        $attachment = new \Swift_Attachment($content, 'billingforecast.pdf', 'application/pdf'); 

        // Create a message
        $message = (new \Swift_Message('Wonderful Subject'))
        ->setFrom("patawaranjason17@gmail.com")
        ->setTo(['patawaranjason17@gmail.com' => 'A name'])
        ->setBody('Here is the message itself')
        ->attach($attachment)
        ;

        // Send the message
        $result = $mailer->send($message);
    }
}

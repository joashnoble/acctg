<!DOCTYPE html>
<html>
    <head>
        <title>Collection Report</title>
        
        <style type="text/css">
            body{
                font-family: Calibri;
                font-size: 8pt!important;
            }
            table {
                font-size: 10pt!important;
                border-collapse: collapse;
            }
            tr:last-child td{
                border-bottom: 1px solid gray;
            }
            td {
                border-right: 1px solid gray;
                border-left: 1px solid gray;
                padding-left: 5px;
                padding-right: 5px;
            }
            th {
                border: 1px solid gray;
                padding-left: 5px;
                padding-right: 5px;
            }
            @page {
                margin: 0.4in;
            }
            .cr {
                border-bottom: 1px solid gray;
            }
                
        </style>
    </head>
    <body>
        <table width="100%">
            <tr style="border:none;">
                <td style="border: none" width="80%" class="align-center">
                    <div style="font-size: 20pt;font-weight: 500;">{{$company_info->company_name}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->company_address}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->email_address}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->landline}}/{{$company_info->mobile_number}}</div>
                </td>
                <td width="20%" style="object-fit: cover; border: none"><img src="{{$company_info->logo}}" style="height: 90px; width: 200px; text-align: right;"></td>
            </tr>
        </table>
        <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 16pt; font-weight: 500; text-align: center">COLLECTION REPORT</div>
            <div style="font-size: 9pt; text-align: center">{{date('F d, Y', strtotime($date_from))}} to {{date('F d, Y', strtotime($date_to))}}</div>
            <div style="font-size: 9pt; text-align: center">Department : {{$department_desc}}</div>
        </div>
        <br>
        @foreach ($departments as $department)
            <div style="font-size: 9pt; font-weight: bold">{{$department->department_desc}}</div>
            <table class="cr" style="font-size: 8pt; width: 100%; margin-top: 5px;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Date</th>
                        <th style="text-align:left;">Transaction #</th>
                        <th style="text-align:left;">Reference #</th>
                        <th style="text-align:left;">Tenant</th>
                        <th style="text-align:center;">Payment Type</th>
                        <th style="text-align:center;">Check No</th>
                        <th style="text-align:center;">Check Date</th>
                        <th style="text-align:right;">Amount</th>
                        <th style="text-align:left;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    @if ($payment->department_id == $department->department_id)
                    <tr>
                        <td style="text-align:left; width: 110px;">{{date('F d, Y', strtotime($payment->payment_date))}}</td>
                        <td style="text-align:left; width: 80px;">{{$payment->transaction_no}}</td>
                        <td style="text-align:left; width: 80px;">{{$payment->reference_no}}</td>
                        <td style="text-align:left;">{{$payment->trade_name}}</td>
                        <td style="text-align:center; width: 100px;">{{$payment->payment_type_desc}}</td>
                        <td style="text-align:center; width: 115px;">{{$payment->check_no}}</td>
                        <td style="text-align:center; width: 115px;">{{$payment->payment_type == 1 ? date('F d, Y', strtotime($payment->check_date)) : ''}}</td>
                        <td style="text-align:right; width: 120px;">{{number_format($payment->amount_paid,2)}}</td>
                        <td style="text-align:left;">{{$payment->remarks}}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <table style="font-size: 8pt; width: 100%; margin-top: 5px;">
                <tbody>
                    <tr>
                        <td style="width: 70%; border: none"></td>
                        <td style="width: 15%; text-align: right; border: none">Total Cash</td>
                        <td style="width: 15%; text-align: right; border: none">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p) use($department) {return $p->department_id == $department->department_id && $p->payment_type == 0; }), 'amount_paid')),2)}}</td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: none"></td>
                        <td style="width: 15%; text-align: right; border: none">Total Check</td>
                        <td style="width: 15%; text-align: right; border: none">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p) use($department) {return $p->department_id == $department->department_id && $p->payment_type == 1; }), 'amount_paid')),2)}}</td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: none"></td>
                        <td style="width: 15%; text-align: right; border: none">Total Online</td>
                        <td style="width: 15%; text-align: right; border: none">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p) use($department) {return $p->department_id == $department->department_id && $p->payment_type == 2; }), 'amount_paid')),2)}}</td>
                    </tr>
                    <tr>
                        <td style="width: 70%; border: none"></td>
                        <td style="width: 15%; text-align: right; border: none; border-bottom: 1px solid gray">Total Withholding Tax</td>
                        <td style="width: 15%; text-align: right; border: none; border-bottom: 1px solid gray">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p) use($department) {return $p->department_id == $department->department_id && $p->payment_type == 3; }), 'amount_paid')),2)}}</td>
                    </tr>
                    <tr>
                        <td style="border: none"></td>
                        <td style="width: 15%; text-align: right; border: none"><b>Total Collection</b></td>
                        <td style="width: 15%; text-align: right; border: none"><b>{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p) use($department) {return $p->department_id == $department->department_id;}), 'amount_paid')),2)}}</b></td>
                    </tr>
                </tbody>
            </table>
        @endforeach
        <br><br>
        <table style="font-size: 8pt; width: 100%; margin-top: 5px;">
            <tbody>
                <tr>
                    <td style="border: none" colspan="2"></td>
                    <td style="border: none; font-size: 9pt;"><b>SUMMARY</b></td>
                </tr>
                <tr>
                    <td style="width: 70%; border: none"></td>
                    <td style="width: 15%; text-align: right; border: none"><b>Grand Total Collect Cash</b></td>
                    <td style="width: 15%; text-align: right; border: none"><b>{{number_format(array_sum(array_column(iterator_to_array($payments), 'amount_paid')),2)}}</b></td>
                </tr>
                <tr>
                    <td style="width: 70%; border: none"></td>
                    <td style="width: 15%; text-align: right; border: none">Less : PDC</td>
                    <td style="width: 15%; text-align: right; border: none">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p)  {return $p->deposit_status == 1; }), 'amount_paid')),2)}}</td>
                </tr>
                <tr>
                    <td style="width: 70%; border: none"></td>
                    <td style="width: 15%; text-align: right; border: none; border-bottom: 1px solid gray">Less : Direct Deposit</td>
                    <td style="width: 15%; text-align: right; border: none; border-bottom: 1px solid gray">{{number_format(array_sum(array_column(array_filter(iterator_to_array($payments), function($p)  {return $p->deposit_status == 2; }), 'amount_paid')),2)}}</td>
                </tr>
                <tr>
                    <td style="border: none"></td>
                    <td style="width: 15%; text-align: right; border: none;"><b>Net Collection for Deposit</b></td>
                    <td style="width: 15%; text-align: right; border: none"><b>{{number_format(array_sum(array_column(iterator_to_array($payments), 'amount_paid')) - array_sum(array_column(array_filter(iterator_to_array($payments), function($p)  {return $p->deposit_status == 2 || $p->deposit_status == 1; }), 'amount_paid')),2)}}</b></td>
                </tr>
            </tbody>
        </table>
        <br><br>
    </body>
</html>
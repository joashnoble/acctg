<!DOCTYPE html>
<html>
    <head>
        <title>Start Billing Form</title>
        
        <style type="text/css">
            @page { sheet-size: A4; }
            body{
                font-family: Calibri;
                font-size: 8pt!important;
            }
            table {
                font-size: 11pt!important;
                border-collapse: collapse;
            }
            th, td:last-child {
                border-bottom: 1px solid gray;
            }
            tr td:first-child {
                padding-left: 10px;
            }
            th {
                background: lightgray;
            }
            .table_start td {
                padding-left: 5px;
            }
            .total {
                background: lightgray;
            }
            .border-left {
                border-left: 1px solid gray;
            }
            .border-bottom {
                border-bottom: 1px solid gray;
            }
            @page {
                margin: 0.4in;
            }
                
        </style>
    </head>
    <body>
        <table width="100%">
            <tr>
                <td style="border-bottom: none" width="80%" class="align-center">
                    <div style="font-size: 20pt;font-weight: 500;">{{$company_info->company_name}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->company_address}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->email_address}}</div>
                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->landline}}/{{$company_info->mobile_number}}</div>
                </td>
                <td width="20%" style="object-fit: cover; border-bottom: none"><img src="{{$company_info->logo}}" style="height: 90px; width: 200px; text-align: right;"></td>
            </tr>
        </table>
        <br>
        <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 16pt; font-weight: 500; text-align: center">START BILLING</div>
            <div style="font-size: 9pt; font-weight: 500; text-align: center">NO. {{$start_billing->start_billing_no}}</div>
        </div>
        <br>
        <table class="table_start" style="font-size: 7pt; width: 100%; border: 1px solid gray">
            <tbody>
                <tr style="border: 1px solid gray">
                    <td colspan=4>
                        {{$start_billing->department_desc}}
                    </td>
                </tr>
                <tr>
                    <td style="width: 25%">DATE</td>
                    <td style="width: 5%">:</td>
                    <td style="width: 40%">{{date('F d, Y', strtotime($start_billing->date))}}</td>
                    <td class="border-left" rowspan="7" valign="top">NOTE:{{$start_billing->contract_remarks}}</td>
                </tr>
                <tr>
                    <td>TO</td>
                    <td>:</td>
                    <td>Accounting Department</td>
                </tr>
                <tr>
                    <td class="border-bottom">FROM</td>
                    <td class="border-bottom">:</td>
                    <td class="border-bottom">Leasing and Marketing Department</td>
                </tr>
                <tr>
                    <td>Trade Name</td>
                    <td>:</td>
                    <td>{{$start_billing->trade_name}}</td>
                </tr>
                <tr>
                    <td>Company Name</td>
                    <td>:</td>
                    <td>{{$start_billing->company_name}}</td>
                </tr>
                <tr>
                    <td>Start of Rent</td>
                    <td>:</td>
                    <td>{{date('F d, Y', strtotime($start_billing->start_billing_date))}}</td>
                </tr>
                <tr>
                    <td>Floor Area</td>
                    <td>:</td>
                    <td>{{number_format($start_billing->contract_floor_area, 2)}} sqm.</td>
                </tr>
                <tr>
                    <td>Monthly Rental</td>
                    <td>:</td>
                    <td>Php {{number_format($start_billing->contract_fixed_rent,2)}}</td>
                    <td rowspan="4" class="border-left border-bottom" valign="top">RECEIVED BY : (Name and Signature)</td>
                </tr>
                <tr>
                    <td>CUSA</td>
                    <td>:</td>
                    <td>{{$start_billing->oec_cusa}}</td>
                </tr>
                <tr>
                    <td>Aircondition</td>
                    <td>:</td>
                    <td>{{$start_billing->oec_aircondition}}</td>
                </tr>
                <tr>
                    <td>Contract Processing Fee</td>
                    <td>:</td>
                    <td>{{$start_billing->oec_processing}}</td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid gray" colspan=3>Noted By :</td>
                    <td class="border-left" style="border-top: 1px solid gray; padding-left: 15px">Approved By :</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td class="border-left"></td>
                </tr>
                <tr>
                    <td colspan="2">________________________________</td>
                    <td>________________________________</td>
                    <td class="border-left" style="padding-left: 15px">BYRON JOHN T. SIY</td>
                </tr>
                <tr>
                    <td colspan=2>Leasing Specialist</td>
                    <td>Leasing and Marketing Head</td>
                    <td class="border-left" style="padding-left: 15px">General Manager</td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid gray" colspan="4">
                        <b>&middot;</b> Original (Yellow) - L&M Copy&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>&middot;</b> Duplicate (Green) - Billing Clerk's Copy&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>&middot;</b> Triplicate (White) - Tenant's Copy
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <title>Acknowledgement Receipt</title>
        
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
            .total {
                background: lightgray;
            }
            .border-left {
                border-left: 1px solid gray;
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
        <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 16pt; font-weight: 500; font-style: italic; text-align: center">ACKNOWLEDGEMENT RECEIPT</div>
        </div>
        <br>
        <table style="font-size: 11pt; width: 100%">
            <tbody>
                <tr>
                    <td style="width: 50%; text-align: left;"><div style="font-weight: 500;">Reference No : {{ $payments->reference_no }}</div></td>
                    <td style="width: 50%; text-align: right;"><div style="font-weight: 500;">Txn Date : {{date("F d, Y", strtotime($payments->payment_date))}}</div></td>
                </tr>
            </tbody>
        </table>
        <br>
        {{-- <div style="font-size: 11pt; width: 100%;">
            <div style="font-weight: 500;"><span style="float: left">Reference No : {{ $payments->reference_no }}</span><span style="float: right">Txn Date : {{date("F d, Y", strtotime($payments->payment_date))}}</span></div>
            <br><br>
        </div> --}}
        <table style="width: 100%;" >
            <tbody>
                <tr>
                    <td style="width: 25%; padding-left: 10px;">Received From</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 73%; border-bottom: 1px solid black">{{$payments->trade_name}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">Address</td>
                    <td>:</td>
                    <td style="border-bottom: 1px solid black">{{$payments->head_office_address}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">The Sum of Pesos (Php)</td>
                    <td>:</td>
                    <td style="font-size: {{strlen($amount_paid_words) > 90 ? '9pt':'11pt'}}; border-bottom: 1px solid black">{{ucwords($amount_paid_words)}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px;">Amount</td>
                    <td>:</td>
                    <td style="border-bottom: 1px solid black">{{number_format($payments->amount_paid, 2)}}</td>
                </tr>
            </tbody>
        </table>
        <br><br>
        <table style="font-size: 11pt; width: 85%;" align="center">
            <tbody>
                <tr>
                    <td style="width: 33%; text-align: left;"><input type="checkbox" {{$payments->payment_type == 0 ? 'checked="checked"' : ''}}> Cash</td>
                    <td style="width: 33%; text-align: center;"><input type="checkbox" {{$payments->payment_type == 1 ? 'checked="checked"' : ''}}>Check</td>
                    <td style="width: 33%; text-align: right;"><input type="checkbox" {{$payments->payment_type == 2 ? 'checked="checked"' : ''}}>Online</td>
                </tr>
            </tbody>
        </table>
        {{-- <div style="font-size: 11pt; width: 100%; font-weight: 500;">
            <div style="padding: 0px 50px 5px; text-align: center"><span style="float: left"><input type="checkbox" :checked="payments.payment_type == 0 ? true : false">Cash</span><input type="checkbox" :checked="payments.payment_type == 1 ? true : false">Check<span style="float: right"><input type="checkbox" :checked="payments.payment_type == 2 ? true : false">Online</span></div>
        </div> --}}
        <br>
        <div style="font-size: 11pt; width: 85%; font-weight: 500; margin: auto">
            {{-- <div style="display: inline-block; width: 50px"></div> --}}
            <div style="display: inline-block; border: 1px solid gray; width: 49%; float: left">
                <div style="padding: 5px">For Check Only</div>
                <div style="padding: 5px">Bank : @if($payments->payment_type == 1) {{$payments->check_type_desc}}@endif</div>
                <div style="padding: 5px"><span>Check No : </span>@if($payments->payment_type == 1){{$payments->check_no}}@endif</div>
                <div style="padding: 5px">Check Date : @if($payments->payment_type == 1){{date('F d, Y', strtotime($payments->check_date))}}@endif</div>
            </div>
            <div style="display: inline-block; width: 49%; float: right">
                <div style="padding: 5px">&nbsp;</div>
                <div style="padding: 5px">&nbsp;</div>
                <div style="padding: 5px; text-align: center">_______________________________</div>
                <div style="padding: 5px; text-align: center">Received By</div>
            </div>
            {{-- <div style="display: inline-block; width: 50px"></div> --}}
        </div>
        <br>
        <table style="font-size: 6pt; width: 100%">
            <tbody>
                <tr>
                    <td style="text-align:left; width: 50%"><div style="font-weight: 500;">Date Printed : {{date('F d, Y')}}</div></td>
                    <td style="text-align:right; width: 50%"><div style="font-weight: 500;">Printed By : {{ $user }}</div></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
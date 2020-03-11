<!DOCTYPE html>
<html>
    <head>
        <title>Charge Slip</title>
        
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
        <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 16pt; font-weight: 500; text-align: center">CHARGE SLIP</div>
        </div>
        <br>
        <table style="font-size: 9pt; width: 80%;" align="center">
            <tbody>
                <tr>
                    <td style="width: 33%; text-align: center;"><input type="checkbox" {{$charge_slip->charge_slip_type == 1 ? 'checked="checked"' : ''}}> Non-Compliance to House Rules</td>
                    <td style="width: 33%; text-align: center;"><input type="checkbox" {{$charge_slip->charge_slip_type == 2 ? 'checked="checked"' : ''}}> Overtime (OT) Payment</td>
                    <td style="width: 33%; text-align: center;"><input type="checkbox" {{$charge_slip->charge_slip_type == 3 ? 'checked="checked"' : ''}}> OTHERS</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="border-collapse: collapse; font-size: 9pt; width: 100%;" align="center">
            <tbody>
                <tr>
                    <td style="width: 13%">Charge Slip No : </td>
                    <td style="width: 33%;" class="border-bottom">{{$charge_slip->charge_slip_no}}</td>
                    <td style="width: 8%"></td>
                    <td style="width: 10%">Ref/WP# :</td>
                    <td style="width: 36%; padding-right: 15px" class="border-bottom">{{$charge_slip->charge_ref_wp_no}}</td>
                </tr>
                <tr>
                    <td>Name/Tenant : </td>
                    <td class="border-bottom">{{$charge_slip->trade_name}}</td>
                    <td></td>
                    <td>Location :</td>
                    <td class="border-bottom">location or department?</td>
                </tr>
                <tr>
                    <td>Date : </td>
                    <td class="border-bottom">{{date('M d, Y', strtotime($charge_slip->charge_slip_datetime))}}</td>
                    <td></td>
                    <td>Time :</td>
                    <td class="border-bottom">{{date('h:i a', strtotime($charge_slip->charge_slip_datetime))}}</td>
                </tr>
                <tr>
                    <td>Nature/Details :</td>
                </tr>
                @for ($i = 0; $i < strlen($charge_slip->charge_slip_nature) / 108; $i++)
                    <tr>
                        <td></td>
                        <td colspan="4" class="border-bottom">
                            {{substr($charge_slip->charge_slip_nature, $i * 108, 108)}}
                        </td>
                    </tr>
                @endfor
                <tr>
                    <td>Amount :</td>
                    <td class="border-bottom">{{number_format($charge_slip->charge_slip_amount, 2)}}</td>
                </tr>
                <tr>
                    <td>Category :</td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_category_type == 1 ? 'checked="checked"' : ''}}> Suspension</td>
                    <td></td>
                    <td>Gravity :</td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_gravity_type == 1 ? 'checked="checked"' : ''}}> 1<sup>st</sup> Offense</td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_category_type == 2 ? 'checked="checked"' : ''}}> Termination</td>
                    <td></td>
                    <td></td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_gravity_type == 2 ? 'checked="checked"' : ''}}> 2<sup>nd</sup> Offense</td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_category_type == 3 ? 'checked="checked"' : ''}}> OTHER : <u>{{$charge_slip->charge_slip_category_type == 3 ? $charge_slip->charge_slip_category_others_desc : ''}}</u></td>
                    <td></td>
                    <td></td>
                    <td><input type="checkbox" {{$charge_slip->charge_slip_gravity_type == 3 ? 'checked="checked"' : ''}}><u>{{$charge_slip->charge_slip_gravity_type == 3 ? $charge_slip->charge_slip_gravity_others_desc : ''}}</u> Offense</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="font-size: 9pt; width: 100%;" align="center">
            <tbody>
                <tr>
                    <td>
                        <div style="padding: 5px; text-align: left">Issued By:</div>
                        <br>
                        <div style="padding: 5px; text-align: left">____________________________</div>
                    </td>
                    <td>
                        <div style="padding: 5px; text-align: left">Noted By:</div>
                        <br>
                        <div style="padding: 5px; text-align: left">____________________________</div>
                    </td>
                    <td>
                        <div style="padding: 5px; text-align: left">Acknowledged By:</div>
                        <br>
                        <div style="padding: 5px; text-align: left">____________________________</div>
                    </td>
                </tr>
            </tbody>     
        </table>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <title>Contracts Master List</title>
        
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
            <div style="font-size: 16pt; font-weight: 500; text-align: center">CONTRACTS MASTER LIST</div>
        </div>
        <br>
        <table style="font-size: 9pt; width: 100%; margin-top: 5px;">
            <thead>
                <tr>
                    <th style="text-align:left;">Tenant Code</th>
                    <th style="text-align:left;">Tenant</th>
                    <th style="text-align:right;">Area (sqm)</th>
                    <th style="text-align:center;">Space Code</th>
                    <th style="text-align:center;">Commencement Date</th>
                    <th style="text-align:center;">Termination Date</th>
                    <th style="text-align:left;">Esc. Note</th>
                    <th style="text-align:left;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contracts as $contract)
                <tr>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:left; width: 95px;">{{$contract->tenant_code}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:left;">{{$contract->trade_name}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:right;">{{number_format($contract->contract_floor_area,2)}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:center;">{{$contract->space_code}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:center;">{{date('F d, Y', strtotime($contract->commencement_date))}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:center;">{{date('F d, Y', strtotime($contract->termination_date))}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:left;">{{$contract->escalation_notes}}</td>
                    <td style="{{$loop->last ? 'border-bottom: 1px solid gray' : ''}}; text-align:left; width: 150px;">{{$contract->contract_remarks}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br><br>
    </body>
</html>
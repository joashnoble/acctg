<!DOCTYPE html>
<html>
    <head>
        <title>Expiring Contracts</title>
        
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
            <div style="font-size: 16pt; font-weight: 500; text-align: center">EXPIRING CONTRACTS</div>
            <div style="font-size: 12pt; font-weight: 500; text-align: center">As of {{date('F d, Y')}}</div>
            <div style="text-align: center"><small>Expired contracts and expiring contracts in 45 days.</small></div>
        </div>
        <br>
        <table style="font-size: 9pt; width: 100%; margin-top: 5px;">
            <thead>
                <tr>
                    <th style="text-align:left;">Contract No</th>
                    <th style="text-align:left;">Trade Name</th>
                    <th style="text-align:left;">Department</th>
                    <th style="text-align:left;">Location</th>
                    <th style="text-align:left;">Term Date</th>
                    <!-- <th style="text-align:right;">Basic Rent</th>
                    <th style="text-align:right;">Disc Rent</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($expiring_contracts as $contract)
                <tr>
                    <td style="text-align:left; width: 95px; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$contract->contract_no}}</td>
                    <td style="text-align:left; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$contract->trade_name}}</td>
                    <td style="text-align:left; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$contract->department_desc}}</td>
                    <td style="text-align:left; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$contract->location_desc}}</td>
                    <td style="text-align:left; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{date('F d, Y', strtotime($contract->termination_date))}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br><br>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <title>Tenants Per Sqm Rate</title>
        
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
            }
            th {
                border: 1px solid gray;
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
            <div style="font-size: 16pt; font-weight: 500; text-align: center">TENANTS W/ PER SQM RATE</div>
        </div>
        <br>
        <div style="font-size: 10pt; width: 100%;">
            <div style="font-weight: 500;">Location : {{$location_desc}}</div>
        </div>
        <table style="width: 100%; margin-top: 5px; font-size: 10pt;">
            <thead>
                <tr>
                    <th style="text-align:left;">Tenant Code</th>
                    <th style="text-align:left;">Tenant</th>
                    <th style="text-align:left;">Space Code</th>
                    <th style="text-align:right;">Area (sqm)</th>
                    {{-- <th style="text-align:right;">Rate Per Sqm(Disc)</th> --}}
                    <th style="text-align:right;">Rate Per Sqm(Basic)</th>
                </tr>
            </thead>
            <tbody>
                {{-- @php
                    $last_key = end(array_keys($tenants));
                @endphp --}}
                @foreach($tenants as $tenant)
                <tr>
                    <td style="text-align:left;padding-right:5px; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$tenant->tenant_code}}</td>
                    <td style="text-align:left;padding-left:5px; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$tenant->trade_name}}</td>
                    <td style="text-align:left;padding-left:5px; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{$tenant->space_code}}</td>
                    <td style="text-align:right; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{number_format($tenant->contract_floor_area,2)}}</td>
                    {{-- <td style="text-align:right; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{number_format($tenant->disc_rate_sqm,2)}}</td> --}}
                    <td style="text-align:right; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{number_format($tenant->basic_rate_sqm,2)}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br><br>
    </body>
</html>
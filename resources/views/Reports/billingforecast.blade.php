<!DOCTYPE html>
<html>
    <head>
        <title>Billing Forecast</title>
        
        <style type="text/css">
            body{
                font-family: Calibri;
                font-size: 7pt!important;
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
            .table-data {
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
            <div style="font-size: 16pt; font-weight: 500; text-align: center">BILLING FORECAST</div>
            <div style="font-size: 10pt; font-weight: 500; text-align: center">Applicable Date : {{date('Y-m', strtotime($period->app_year.'-'.$period->month_id.'-1'))}}</div>
        </div>
        <br>
        @foreach ($departments as $department)
        <div style="font-size: 10pt; width: 100%;">
            <div style="font-weight: 500;">{{$department->department_desc}}</div>
        </div>
        <table class="table-data" align="center" style="width: 95%; margin-top: 5px; font-size: 8pt;">
            <thead>
                <tr>
                    <th style="text-align:left;width: 100px;">Tenant Code</th>
                    <th style="text-align:left; width: 30%;">Tenant</th>
                    <th style="text-align:left; width: 100px;">Contract #</th>
                    <th>Commencement</th>
                    {{-- <th style="text-align:right;">Rate Per Sqm(Disc)</th> --}}
                    <th>Termination</th>
                </tr>
            </thead>
            <tbody>
                {{-- @php
                    $last_key = end(array_keys($tenants));
                @endphp --}}
                @foreach($contracts as $contract)
                @if ($contract->department_id == $department->department_id)
                <tr>
                    <td style="text-align:left;padding-right:5px;">{{$contract->tenant_code}}</td>
                    <td style="text-align:left;padding-left:5px;">{{$contract->trade_name}}</td>
                    <td style="text-align:left;padding-left:5px;">{{$contract->contract_no}}</td>
                    <td style="text-align:center;">{{date('Y-m-d', strtotime($contract->commencement_date))}}</td>
                    {{-- <td style="text-align:right; {{$loop->last ? 'border-bottom: 1px solid gray' : ''}}">{{number_format($tenant->disc_rate_sqm,2)}}</td> --}}
                    <td style="text-align:center;">{{date('Y-m-d', strtotime($contract->termination_date))}}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @if (!$loop->last)
        <br><br>
        @endif
        @endforeach
        
    </body>
</html>
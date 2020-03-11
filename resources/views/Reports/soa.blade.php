<!DOCTYPE html>
<html>
    <head>
        <title>Statement of Account</title>
        
        <style type="text/css">
            @page { sheet-size: A4; }
            body{
                font-family: Calibri;
                font-size: 8pt!important;
            }
            table {
                font-size: 8pt!important;
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
            ul{
                margin: none;
            }
                
        </style>
    </head>
    <body>
        <table width="100%">
            <tr>
                <td width="80%">
                    <div style="font-size: 20pt;font-weight: 500;">{{$billing->department_desc}}</div>
                    <div style="font-size: 10pt;font-weight: 500;margin-top: 0px;">{{$billing->department_address}}</div>
                    {{-- <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->email_address}}</div> --}}
                    {{-- <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{$company_info->landline}}/{{$company_info->mobile_number}}</div> --}}
                </td>
                {{-- <td style="object-fit: cover; border: 1px solid black"><img src="{{$company_info->logo}}" style="height: 90px; width: 200px; text-align: right;"></td> --}}
            </tr>
        </table>
        <br>
        {{-- <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 16pt; font-weight: 500; text-align: right">Statement of Account</div>
        </div> --}}
        <table style="width: 100%">
            <thead>
                <tr>
                    <th colspan="4" style="text-align: left; font-size: 10pt;">ACCOUNT DETAILS</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:20%;">TENANT CODE :</td>
                    <td style="width:35%; text-align: left;">{{ $billing->tenant_code }}</td>
                    <td style="width:12%;">BILLING NO :</td>
                    <td style="width:38%; text-align: left;">{{ $billing->billing_no }}</td>
                </tr>
                <tr>
                    <td style="width:20%;">LOCATION :</td>
                    <td style="width:35%; text-align: left;">{{ $billing->location_desc }}</td>
                    <td style="width:12%;">DATE :</td>
                    <td style="width:38%; text-align: left;">{{ date("F d, Y", strtotime($billing->created_datetime)) }}</td>
                </tr>
                <tr>
                    <td>TRADE NAME :</td>
                    <td>{{ $billing->trade_name}}</td>
                    <td>AREA :</td>
                    <td>{{ number_format($billing->contract_floor_area,2) }} sqm</td>
                </tr>
                <tr>
                    <td>NATURE OF BUSINESS :</td>
                    <td>{{ $billing->nature_of_business_desc }}</td>
                    <td>RATE :</td>
                    <td>{{ number_format($billing->contract_fixed_rent,2) }}</td>
                </tr>
                <tr>
                    <td>CONTACT :</td>
                    <td>{{ $billing->contact_person }}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%">
            <thead>
                <tr>
                    <th colspan="4" class="asdf" style="text-align: left; font-size: 10pt;">PREVIOUS BALANCE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="2">BALANCE AS OF : {{ date('F', strtotime('-1 months', strtotime($billing->app_year.'-'.$billing->month_id.'-1')))}}</td>
                    <td style="width: 15%"></td>
                    <td style="text-align: right; width: 15%">{{number_format($as_of_balance[0]->as_of_balance,2)}}</td>
                </tr>
                @if (count($payments) > 0)
                    <tr>
                        <td colspan="2">PAYMENTS :</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <!-- may foreach dito -->
                    @foreach ($payments as $payment)
                        @if ($payment->payment > 0)
                        <tr>
                            <td style="width: 8%"></td>
                            <td>{{ date("m/d/Y", strtotime($payment->payment_date)) }} Trans. No.: {{ $payment->transaction_no }} Ref. No. {{ $payment->reference_no }} - {{$payment->trans_type}} {{$payment->payment_type == 3 ? '- Withholding Tax' : ''}}
                                @if ($payment->payment_type==1)
                                    <span> 
                                        <br>{{$payment->check_type_desc}} Check No - {{$payment->check_no}} Date - {{date("F d, Y", strtotime($payment->check_date)) }}
                                    </span>
                                @endif 
                            </td>
                            <td></td>
                            <td style="text-align:right;">({{number_format($payment->payment, 2)}})
                                @if ($payment->payment_type==1)
                                    <span><br>&nbsp;</span>
                                @endif 
                            </td>
                        </tr>
                        @endif
                        {{-- @if ($payment->used_advances > 0)
                            <tr>
                                <td style="width: 8%"></td>
                                <td>{{ date("F d, Y", strtotime($payment->payment_date)) }} Trans. No.: {{ $payment->transaction_no }} Ref. No. {{ $payment->reference_no }} - Used Advances
                                </td>
                                <td></td>
                                <td style="text-align:right;">({{number_format($payment->used_advances, 2)}})
                                </td>
                            </tr>
                        @endif
                        @if ($payment->wtax_amount > 0)
                            <tr>
                                <td style="width: 8%"></td>
                                <td>{{ date("F d, Y", strtotime($payment->payment_date)) }} Trans. No.: {{ $payment->transaction_no }} Ref. No. {{ $payment->reference_no }} - Withholding Tax
                                </td>
                                <td></td>
                                <td style="text-align:right;">({{number_format($payment->wtax_amount, 2)}})
                                </td>
                            </tr>
                        @endif --}}
                    @endforeach
                @endif
                <tr>
                    <td colspan="3" style="font-size: 9pt;"><b>TOTAL OUTSTANDING BALANCE</b></td>
                    <td style="text-align: right; font-size: 9pt;"><b>{{number_format($previous_balance[0]->previous_balance,2)}}</b></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%; margin-top: 5px;">
            <thead>
                <tr>
                    <th colspan="5" style="text-align: left; font-size: 10pt;">CURRENT BILLING</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 8%"></td>
                    <td colspan="3"></td>
                    <td style="text-align: right; width: 15%">BASIC RENT</td>
                    <!-- <td style="text-align: right; width: 15%">DISCOUNTED RENT</td> -->
                </tr>
                <tr>
                    <td colspan="5">RENTAL :</td>
                </tr>
                @foreach ($schedules as $schedule)
                <tr>
                    <td></td>
                    <td colspan="3">{{$schedule->month_name}} <i>{{ $schedule->billing_schedule_notes }}</i></td>
                    <td style="text-align: right">{{number_format($schedule->line_total, 2)}}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="2"></td>
                    <td style="text-align: right">RATE</td>
                    <td style="text-align: right">READING</td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                @if (count($utilities) > 0)
                    <tr>
                        <td colspan="2">UTILITIES :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td></td> -->
                    </tr>
                <!-- may foreach dito -->
                    @foreach ($utilities as $utility)
                        <tr>
                            <td></td>
                            <td>{{ $utility->charge_desc }} <i>{{ $utility->contract_notes }}</i> </td>
                            <td style="text-align: right">{{ number_format($utility->contract_rate,2) }}</td>
                            <td style="text-align: right">{{ number_format($utility->contract_default_reading,2) }}</td>
                            <td style="text-align: right">{{ number_format($utility->billing_util_line_total,2) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if (count($miscellaneous) > 0)
                    <tr>
                        <td colspan="2">MISCELLANEOUS CHARGES :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td></td> -->
                    </tr>
                    <!-- may foreach dito -->
                    @foreach ($miscellaneous as $misc)
                        <tr>
                            <td></td>
                            <td>{{ $misc->charge_desc }} <i>{{ $misc->contract_notes }}</i></td>
                            <td style="text-align: right">{{ number_format($misc->contract_rate,2) }}</td>
                            <td style="text-align: right">{{ number_format($misc->contract_default_reading,2) }}</td>
                            <td style="text-align: right">{{ number_format($misc->billing_misc_line_total,2) }}</td>
                        </tr>
                    @endforeach
                @endif
                
                @if (count($other) > 0)
                    <tr>
                        <td colspan="2">OTHER CHARGES :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <!-- <td></td> -->
                    </tr>
                    <!-- may foreach dito -->
                    @foreach ($other as $othr)
                        <tr v-if="other.length > 0" v-for="othr in other">
                            <td></td>
                            <td>{{ $othr->charge_desc }} <i>{{ $othr->contract_notes }}</i></td>
                            <td style="text-align: right">{{ number_format($othr->contract_rate,2) }}</td>
                            <td style="text-align: right">{{ number_format($othr->contract_default_reading,2) }}</td>
                            <td style="text-align: right">{{ number_format($othr->billing_othr_line_total,2) }}</td>
                        </tr>
                    @endforeach
                @endif
                @if ($billing->interest_total > 0 || $billing->penalty_total > 0)
                    <tr>
                        <td colspan="2">PENALTIES :</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
                @if ($billing->interest_total > 0)
                    <tr>
                        <td></td>
                        <td>Penalty for unpaid balances ({{number_format($billing->interested_amount,2)}} * {{number_format($billing->interest_percent,2)}}%)</td>
                        <td></td>
                        <td></td>
                        <td style="text-align:right;">{{number_format($billing->interest_total,2)}}</td>
                    </tr>
                @endif
                @if ($billing->penalty_total > 0)
                    <tr>
                        <td></td>
                        <td>Penalty for late payment ({{number_format($billing->penaltied_amount,2)}} * {{number_format($billing->penalty_percent,2)}}%)</td>
                        <td></td>
                        <td></td>
                        <td style="text-align:right;">{{number_format($billing->penalty_total, 2)}}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2">TAXES :</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <!-- <td></td> -->
                </tr>
                {{-- <tr>
                    <td></td>
                    <td>WITHHOLDING TAX</td>
                    <td style="text-align: right">-{{ number_format($billing->wtax_percent,2) }}%</td>
                    <td></td>
                    <td style="text-align: right">({{ number_format($billing->wtax_amount,2) }})</td>
                </tr> --}}
                    <tr>
                    <td></td>
                    <td>VAT</td>
                    <td style="text-align: right">{{ number_format($billing->vat_percent,2) }}%</td>
                    <td></td>
                    <td style="text-align: right">{{ number_format($billing->total_vat,2) }}</td>
                </tr>
                @if (count($adjustments) > 0)
                    <tr>
                        <td colspan="4">ADJUSTMENTS :</td>
                        <td></td>
                        <!-- <td></td> -->
                    </tr>
                    <!-- may foreach dito -->
                    @foreach ($adjustments as $adjustment)
                        <tr>
                            <td></td>
                            <td>{{ $adjustment->charge_desc }} <i>{{ $adjustment->contract_notes }}</i></td>
                            <td style="text-align: right">{{ number_format($adjustment->contract_rate, 2) }}</td>
                            <td style="text-align: right">{{ number_format($adjustment->contract_default_reading, 2) }}</td>
                            <td style="text-align: right">{{ $adjustment->billing_adjustment_type == 0 ? number_format($adjustment->billing_adjustment_line_total, 2) : '('.number_format(abs($adjustment->billing_adjustment_line_total), 2).')' }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="4" style="font-size: 9pt;"><b>TOTAL CURRENT CHARGES</b></td>
                    <td style="text-align: right; border-top: 1px solid gray"><b>{{number_format($billing->total_amount_due,2)}}</b></td>
                </tr>
            </tbody>
        </table>
        <table class="total" style="width: 100%; margin-top: 5px;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 70%; font-size: 12pt;">TOTAL AMOUNT DUE</th>
                    <th style="text-align: right; width: 15%; font-size: 12pt;">{{number_format($previous_balance[0]->previous_balance + $billing->total_amount_due, 2)}}</th>
                </tr>
            </thead>
        </table>
        <div style="width: 100%">
            * NOTES
            {!! $billing->billing_notes !!}
            <ul>
                <li>
                    WITHHOLDING TAX - BIR FORM 2307 (5% of Fixed Rent) - ({{ number_format($billing->wtax_amount,2) }})
                </li>
                <li>
                    WITHHOLDING TAX - BIR FORM 2307 (2% of Electricity) - ({{ number_format($billing->electric_wtax_amount,2) }})
                </li>
                <li>
                    For expanded wthholding tax deductions, please submit the corresponding tax certificate (BIR Form 2307). Otherwise, the total amount withheld shall be deemed unpaid and demandable.
                </li>
            </ul> 
        </div>
        <br>
        <table style="width: 100%">
            <tbody>
                <tr>
                    <td style="text-align: center">Prepared By:</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">Certified Correct:</td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td style="text-align: center">_________________________</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">_________________________</td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
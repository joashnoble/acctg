<!DOCTYPE html>
<html>
    <head>
        <title>Contract of Lease</title>
        
        <style type="text/css">
            body{
                font-family: Calibri;
                font-size: 8pt!important;
            }
            table {
                font-size: 8pt!important;
                border-collapse: collapse;
            }
            
            @page {
                margin: 1.5cm;
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
        <br>
        <div style="font-size: 10.5pt;">
            <b>{{strtoupper($contract->contract_signatory)}}</b>
        </div>
        <div style="font-size: 10.5pt;">
            {{$contract->designation}}
        </div>
        <div style="font-size: 10.5pt;">
            <b>{{$contract->trade_name}}</b>
        </div>
        <div style="font-size: 11pt; width: 100%;">
            <div style="font-size: 13pt; font-weight: bold; text-align: center">CONTRACT OF LEASE</div>
        </div>
        <br>
        <div style="font-size: 10.5pt;">
            <b>Dear {{$contract->contract_signatory}}</b>
        </div>
        <br>
        <div style="font-size: 8pt;">
           <div style="font-size: 11pt"><b>Greetings from TAN SONG BOK Realty and Development Corporation!</b></div>
           <br>
           <br>
           <div style="font-size: 10.5pt">
            We want your prestigious company to be part of our family. Given that, we are pleased to offer you a space in one of our business centers. Kindly see below outlined terms and conditions for your conformity.
           </div>
        </div>
        <br>
        <table style="width: 100%; font-size: 10.5pt; border:none!important">
            <tbody>
                <tr>
                    <td style="width: 45%"><b>TRADENAME</b></td>
                    <td style="width: 10%; text-align: center">:</td>
                    <td style="width: 45%"><b>{{strtoupper($contract->trade_name)}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>NATURE/DESCRIPTION OF ESTABLISHMENT</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->nature_of_business_desc}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>APPROVED MERCHANDISE</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->contract_approved_merch}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>BRANCH/PROPERTY</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->department_desc}}</b></td>
                </tr>
                <tr>
                    <td><b>LOCATION</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->location_desc}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>LEASE TERM</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->contract_terms}} Month(s)</b></td>
                </tr>
                <tr>
                    <td style="padding-left: 10px">Commencement Date</td>
                    <td style="text-align: center">:</td>
                    <td>{{date('M d, Y', strtotime($contract->commencement_date))}}</td>
                </tr>
                <tr>
                    <td style="padding-left: 10px">Termination Date</td>
                    <td style="text-align: center">:</td>
                    <td>{{date('M d, Y', strtotime($contract->termination_date))}}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>APPROXIMATE FLOOR AREA</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{number_format($contract->contract_floor_area,2)}} square meters</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>MONTHLY RENTAL</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>Php {{number_format($contract->contract_fixed_rent,2)}} per Month [Vat Exclusive]</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>OTHER EXPENSES CHARGEABLE</b></td>
                </tr>
                <tr>
                    <td><b>Electricity</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_electricity}}</b></td>
                </tr>
                <tr>
                    <td><b>Water</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_water}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>MARKETING PARTICIPATION FEE</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_marketing}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>CONTRACT PROCESSING FEE (Notarization)</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_processing}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>AIRCONDITION</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_aircondition}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>Common Usage Expenses (CUSA)</b><br>Security, Maintenance, Electricity, and all other Common Area Expenses</td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_cusa}}</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Repair & Maintenance of Leased Premises, Equipment, Furniture</td>
                    <td style="text-align: center">:</td>
                    <td>{{$contract->oec_repair_maintenance}}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Expenses for Individual Utilities</td>
                    <td style="text-align: center">:</td>
                    <td>{{$contract->oec_expense_utilities}}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>SECURITY DEPOSIT</b></td>
                    <td style="text-align: center">:</td>
                    <td><b>{{$contract->oec_security_deposit}}</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div style="font-size: 10.5pt;">
            <b>Note:</b> Security deposits to increase when rent increase. Please coordinate with Accounting Department for the additional security deposits
            <br>
            <br>
            <b>INTEREST AND PENALTIES ON LATE PAYMENT:</b><br>
            All unpaid charges shall be subjected to an interest rate of three percent (3%) per month. In addition, the Lessor, at its option may impose a penalty of five percent (5%) interest per month, on all unpaid charges including interests for accounts over 60 days past due until full payment is made.
            <br><br>
            <b>CANCELLATION/PRE-TERMINATION:</b><br>
            Should Lessee decide to cancel its reservation and/or pre-terminate the Contract of Lease after having paid the reservation fee and all other charges, Lessor shall have the right to forfeit in its favor the reservation, security deposit, power meter deposit, rental payments and any other charges paid by Lessee. Should Lessee fail to commence operation within Three (3) days from the effectivity of the lease, Lessor shall have the right to unilaterally cancel this lease agreement and forfeit all payments thereof.
            <br><br>
            <b>LEASE CONTRACT:</b><br>
            This Lease Proposal together with the attached General Terms and Conditions (Annex "A" hereof), embodies all the terms and conditions agreed upon by the parties and shall be binding upon them. The Lessee or his/her representatives certify that they have read this or caused to be read to him/her, inclusive of its annexes, and that he/she has fully understood the same.
            <br><br>
            It is hereby agreed that the General Terms and Conditions earlier signed and executed shall form part of this lease renewal/extension contract, and that all its provisions are deemed in effect, binding, obligatory, on the part of the conditions as contained herein, are deemed supplemental to this contract and may be referred to for the purpose of determining the rights and obligations of the parties.
            <br><br>
            Please be advised that we are also entertaining other parties to the space and eventual awarding of the space will be on a first confirmed-first reserved basis upon receipt of signed lease proposal and payment of security deposits.
            <br><br>
            We look forward in doing business with you. Should you have any other concerns, please do not hesitate to contact us at <b>(045) 322-8517/(045) 304-2049</b> or drop us an email at <i><u>jabonifacio@tsbrealty.org</u></i> and <i><u>agdeluna@tsbrealty.org</u></i>
            <br><br>
            <table style="width: 100%; font-size: 10.5pt;">
                <tbody>
                    <tr>
                        <td>
                            Very truly yours,
                        </td>
                        <td>
                            Noted by:
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="width: 49%"><b>{{strtoupper($contract->signatory_1)}}</b><br>{{$contract->signatory_1_position}}</div>
                        </td>
                        <td><div style="width: 49%"><b>{{strtoupper($contract->signatory_3)}}</b><br>{{$contract->signatory_3_position}}</div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            Conformed by:
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <div style="width: 49%"><b>{{strtoupper($contract->signatory_2)}}</b><br>{{$contract->signatory_2_position}}</div>
                        </td>
                        <td><div style="width: 49%"><b>{{strtoupper($contract->contract_signatory)}}</b><br>{{$contract->designation}}</div></td>
                    </tr>
                </tbody>
            </table>
         </div>
    </body>
</html>
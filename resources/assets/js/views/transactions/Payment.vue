<template>
    <div>
        <notifications group="notification" />
        <div v-show="showEntry === false" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Payment List
                                <small class="font-italic">List of all payments.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col  sm="2">
                                    <b-button variant="success" v-if="checkRights('15-56')" @click="resetFieldStates('payment'), clearFields('payment'),entryMode = 'Add', showEntry = true, forms.payment.fields.advance">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Payment
                                    </b-button>
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">From:</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker 
                                    @input="filterPayments()"
                                    v-model="date_from" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">To:</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker
                                    @input="filterPayments()"
                                    v-model="date_to" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>
                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.payments.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row>
                        
                        <b-row>
                            <b-col sm="12">
                                <b-table 
                                    v-if="checkAction"
                                    responsive
                                    :filter="filters.payments.criteria"
                                    :fields="tables.payments.fields"
                                    :items.sync="tables.payments.items"
                                    :current-page="paginations.payments.currentPage"
                                    :per-page="paginations.payments.perPage"
                                    @filtered="onFiltered($event,'payments')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(row_data)="row">
                                        <!-- <b-btn :size="'sm'" variant="success" @click.stop="row.toggleDetails"> -->
                                            <i @click.stop="row.toggleDetails()" :class="row.detailsShowing ? 'fa fa-minus fa-lg text-danger' : 'fa fa-plus fa-lg text-success'"></i>
                                        <!-- </b-btn> -->
                                    </template>
                                    <template v-slot:row-details="row">
                                        <b-card class="card-accent-dark">
                                            <h5 slot="header">
                                                Payment Info ({{row.item.transaction_no}})
                                            </h5>
                                            <b-row class="font-weight-bold">
                                                <b-col lg="4">
                                                    <p>Reference No : {{row.item.reference_no}}</p>
                                                    <p>Transaction Date : {{moment(row.item.payment_date, 'MMMM DD, YYYY')}}</p>
                                                    <p>Tenant : {{row.item.trade_name}}</p>
                                                    <p>Tenant Code : {{row.item.tenant_code}}</p>
                                                    <p>Contact Person : {{row.item.contact_person}}</p>
                                                    <!-- <p>Space Code : {{row.item.space_code}}</!--> -->
                                                </b-col>
                                                <b-col lg="4">
                                                    <p>Payment Type : {{row.item.payment_type == 0 ? 'Cash' : row.item.payment_type == 1 ? 'Check' : row.item.payment_type == 2 ? 'Online' : 'Withholding Tax'}}</p>
                                                    <p>Contract Advance : {{formatNumber(row.item.used_advances)}}</p>
                                                    <p>Amount Paid : {{formatNumber(row.item.amount_paid)}}</p>
                                                </b-col>
                                                <b-col lg="4">
                                                    <p v-if="row.item.payment_type == 1">Check Type : {{row.item.check_type_desc}}</p>
                                                    <p v-if="row.item.payment_type == 1">Check No : {{row.item.check_no}}</p>
                                                    <p v-if="row.item.payment_type == 1">Check Date : {{moment(row.item.check_date, 'MMMM DD, YYYY')}}</p>
                                                    <p v-if="row.item.payment_type == 1 || row.item.payment_type == 2">Deposit No : {{row.item.deposit_no}}</p>
                                                    <p v-if="row.item.payment_type == 1 || row.item.payment_type == 2">Deposit Date : {{moment(row.item.deposit_date, 'MMMM DD, YYYY')}}</p>
                                                    <p v-if="row.item.payment_type == 1 || row.item.payment_type == 2">Deposit Status : {{row.item.deposit_status == 1 ? 'PDC' : 'Direct Deposit'}}</p>
                                                    <!-- <p>Payment Advance : {{formatNumber(row.item.advance)}}</p> -->
                                                    <p>Remarks : {{row.item.remarks == null || row.item.remarks == "" ? 'N/A' : row.item.remarks}}</p>
                                                </b-col>
                                            </b-row>
                                        </b-card>
                                    </template>
                                    <template v-slot:cell(action)="data">
                                        <b-button :size="'sm'" v-if="checkRights('15-58')" variant="success" @click="printAckReceipt(data.item.payment_id)">
                                            <i class="fa fa-print"></i>
                                        </b-button>
                                        <b-button :size="'sm'" v-if="checkRights('15-57')" variant="danger" @click="setDelete(data)">
                                            <i class="fa fa-trash"></i>
                                        </b-button>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.payments.totalRows" :per-page="paginations.payments.perPage" v-model="paginations.payments.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <div v-show="showEntry" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Payment Entry - {{entryMode}}
                            </span>
                        </h5>
                        <b-row>
                            <b-col sm="12">
                                <b-card class="card-accent-dark">
                                    <h6 slot="header">
                                        Payment Info
                                    </h6>
                                    <b-form @keydown="resetFieldStates('payment')" autocomplete="off">
                                        <b-row>
                                            <b-col lg="4">
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Transaction No : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Auto" 
                                                                v-model="forms.payment.fields.transaction_no"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Reference No. :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                ref="reference_no"
                                                                type="text"
                                                                placeholder="Reference No" 
                                                                v-model="forms.payment.fields.reference_no"
                                                                debounce="250">
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Trade Name : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <select2
                                                                ref="tenant_id"
                                                                @input="getTenantInfo"
                                                                :allowClear="false"
                                                                :placeholder="'Select Tenants'"
                                                                v-model="forms.payment.fields.tenant_id"
                                                            >
                                                                <option v-for="tenant in options.tenants.items" :key="tenant.tenant_id" :value="tenant.tenant_id">{{tenant.trade_name}}</option>
                                                            </select2>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Tenant Code : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Tenant Code" 
                                                                v-model="forms.payment.fields.tenant_code"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Contact Person : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Contact Person" 
                                                                v-model="forms.payment.fields.contact_person"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Space Code : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Space Code" 
                                                                v-model="forms.payment.fields.space_code"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                            </b-col>
                                            <b-col lg="4">
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Payment Type : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <select2
                                                                ref="payment_type"
                                                                @input="getPaymentType"
                                                                :allowClear="false"
                                                                :placeholder="'Select Payment Type'"
                                                                v-model="forms.payment.fields.payment_type"
                                                            >
                                                                <option value=0>Cash</option>
                                                                <option value=1>Check</option>
                                                                <option value=2>Online</option>
                                                                <option value=3>Withholding Tax</option>
                                                            </select2>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Payment Date : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <date-picker 
                                                                v-model="forms.payment.fields.payment_date" 
                                                                lang="en" 
                                                                input-class="form-control mx-input"
                                                                format="MMMM DD, YYYY"
                                                                :clearable="false"
                                                                :disabled="true">
                                                            </date-picker>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Bank : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <select2
                                                                ref="check_type_id"
                                                                :allowClear="false"
                                                                :placeholder="'Select Bank'"
                                                                v-model="forms.payment.fields.check_type_id"
                                                                :disabled="forms.payment.fields.payment_type == 1 ? false : true"
                                                                :reference="'checktype'"
                                                                @input="isOptionCreating"
                                                            >
                                                                <option value="-1">Create New Bank</option>
                                                                <option v-for="check in options.checktypes.items" :key="check.check_type_id" :value="check.check_type_id">{{check.check_type_desc}}</option>
                                                            </select2>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Check No :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                ref="check_no"
                                                                type="text"
                                                                placeholder="Check No." 
                                                                v-model="forms.payment.fields.check_no"
                                                                debounce="250"
                                                                :disabled="forms.payment.fields.payment_type == 1 ? false : true">
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Check Date : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <date-picker 
                                                                ref="check_date"
                                                                v-model="forms.payment.fields.check_date" 
                                                                lang="en" 
                                                                input-class="form-control mx-input"
                                                                format="MMMM DD, YYYY"
                                                                :clearable="false"
                                                                :disabled="forms.payment.fields.payment_type == 1 ? false : true">
                                                            </date-picker>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                            </b-col>
                                            <b-col lg="4">
                                                <b-form-group>
                                                    <b-form-radio-group 
                                                        ref="deposit_status"
                                                        id="is_auto"
                                                        v-model="forms.payment.fields.deposit_status"
                                                        >
                                                        <b-form-radio value="1">PDC</b-form-radio>
                                                        <b-form-radio value="2">Direct Deposit</b-form-radio>
                                                    </b-form-radio-group>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Deposit No :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                ref="deposit_no"
                                                                type="text"
                                                                placeholder="Deposit No." 
                                                                v-model="forms.payment.fields.deposit_no"
                                                                debounce="250"
                                                                >
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Deposit Date : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <date-picker 
                                                                ref="deposit_date"
                                                                v-model="forms.payment.fields.deposit_date" 
                                                                lang="en" 
                                                                input-class="form-control mx-input"
                                                                format="MMMM DD, YYYY"
                                                                :clearable="false"
                                                                >
                                                            </date-picker>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Cntrct Advance : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <vue-autonumeric
                                                                readonly
                                                                ref="contract_advance"
                                                                v-model="forms.payment.fields.contract_advance"
                                                                :class="'form-control text-right'" 
                                                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                            </vue-autonumeric>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label"> Paid Amount :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <vue-autonumeric 
                                                                ref="amount"
                                                                v-model="forms.payment.fields.amount"
                                                                :class="'form-control text-right'" 
                                                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                            </vue-autonumeric>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                    </b-form>
                                </b-card>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col sm="12">
                                <b-card class="card-accent-dark">
                                    <h6 slot="header">
                                        Payment Details
                                    </h6>
                                    <b-row>
                                        <b-col lg="7">
                                            <b-form-group>
                                                <label>Remarks</label>
                                                <b-form-textarea
                                                    id="remarks"
                                                    v-model="forms.payment.fields.remarks"
                                                    debounce="250"
                                                    type="text"
                                                    :rows="7"
                                                    placeholder="Remarks">
                                                </b-form-textarea>
                                            </b-form-group>
                                        </b-col>
                                        <b-col lg="5">
                                            <table style="width:100%" class="table-sm font-weight-bold">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="3">Total Billing</td>
                                                        <td class="text-right">{{ formatNumber(total_outstanding_balance) }}</td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td style="width:5%!important"></td>
                                                        <td style="width:35%!important">Total Discount</td>
                                                        <td style="width:30%!important"></td>
                                                        <td style="width:30%!important" class="text-right">{{ formatNumber(forms.payment.fields.discount) }}</td>
                                                    </tr>
                                                    <tr style="border-top: 1px solid #c2cfd6">
                                                        <td colspan="3">Sub Total</td>
                                                        <td class="text-right">{{ formatNumber(Number(total_outstanding_balance) - Number(forms.payment.fields.discount)) }}</td>
                                                    </tr> -->
                                                    <tr>
                                                        <td colspan="3">Payments</td>
                                                        <td class="text-right"></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>Advance used</td>
                                                        <td>
                                                            <vue-autonumeric 
                                                                v-model="forms.payment.fields.used_advances"
                                                                :class="'form-control text-right'" 
                                                                :options="{minimumValue: 0, maximumValue: forms.payment.fields.contract_advance, modifyValueOnWheel: false, emptyInputBehavior: 0}" 
                                                            >
                                                            </vue-autonumeric>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td></td>
                                                        <td>Withholding Tax</td>
                                                        <td>
                                                            <vue-autonumeric 
                                                                v-model="forms.payment.fields.wtax_amount"
                                                                :class="'form-control text-right'" 
                                                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}" 
                                                            >
                                                            </vue-autonumeric>
                                                        </td>
                                                        <td></td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                        <td></td>
                                                        <td>Carry Over Balance</td>
                                                        <td class="text-right">{{formatNumber(forms.payment.fields.carried_advance)}}</td>
                                                        <td></td>
                                                    </tr> -->
                                                    <tr>
                                                        <td></td>
                                                        <td>Paid Amount</td>
                                                        <td class="text-right">{{formatNumber(forms.payment.fields.amount)}}</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr style="border-top: 1px solid #c2cfd6">
                                                        <td colspan="2">Total Paid</td>
                                                        <td colspan="2" class="text-right">{{formatNumber( Number(forms.payment.fields.amount) + Number(forms.payment.fields.used_advances))}}</td>
                                                    </tr>
                                                    <tr style="border-top: 1px solid #c2cfd6">
                                                        <td colspan="2">Balance</td>
                                                        <td colspan="2" class="text-right">{{formatNumber(total_outstanding_balance - (Number(forms.payment.fields.amount) + Number(forms.payment.fields.used_advances)))}}</td>
                                                    </tr>
                                                    <!-- <tr style="border-top: 1px solid #c2cfd6">
                                                        <td colspan="2">Advance</td>
                                                        <td colspan="2" class="text-right">{{formatNumber(forms.payment.fields.advance)}}</td>
                                                    </tr> -->
                                                </tbody>
                                            </table>
                                            <!-- <b-form-group>
                                                <b-row>
                                                    <b-col lg="4">
                                                        <label class="col-form-label">Advance : </label>
                                                    </b-col>
                                                    <b-col lg="8">
                                                        <vue-autonumeric 
                                                            v-model="forms.payment.fields.advance"
                                                            :class="'form-control text-right'" 
                                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}" 
                                                            readonly>
                                                        </vue-autonumeric>
                                                    </b-col>
                                                </b-row>
                                            </b-form-group>
                                            <b-form-group>
                                                <b-row>
                                                    <b-col lg="4">
                                                        <label class="col-form-label">Total Paid : </label>
                                                    </b-col>
                                                    <b-col lg="8">
                                                        <vue-autonumeric 
                                                            v-model="forms.payment.fields.amount"
                                                            :class="'form-control text-right'" 
                                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}" 
                                                            readonly>
                                                        </vue-autonumeric>
                                                    </b-col>
                                                </b-row>
                                            </b-form-group> -->
                                        </b-col>
                                    </b-row>
                                </b-card>
                            </b-col>
                        </b-row>
                        <!-- <b-row v-if="is_check" class="pull-right mt-2">
                            <b-col sm="12">
                                Check all data if correct, proceed?
                                <b-button 
                                    :disabled="forms.payment.isSaving" 
                                    variant="success" 
                                    @click="onPaymentEntry(), is_check=false">
                                    Yes
                                </b-button>
                                <b-button variant="danger" @click="is_check=false">No</b-button>
                            </b-col>
                        </b-row> -->
                        <template v-slot:footer>
                            <b-row class="pull-right mt-2">
                                <b-col sm="12">
                                    <input type="checkbox" v-model="is_print"> Print Acknowledgement Receipt?
                                    <b-button 
                                        variant="success" 
                                        @click="showModalConfirmation = true">
                                        <i class="fa fa-check"></i>
                                        Save
                                    </b-button>
                                    <b-button variant="danger" @click="showEntry=false, forms.payment.fields.used_advances = 0">Close</b-button>
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <b-modal 
            v-model="showModalDelete"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Cancel Payment
            </div>
            <b-col lg=12>
                Are you sure you want to cancel this payment?
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.payment.isSaving" variant="success" @click="onPaymentDelete()">
                    <icon v-if="forms.payment.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    OK
                </b-button>
                <b-button variant="danger" @click="showModalDelete=false">Close</b-button>            
            </div>
        </b-modal>
        <b-modal 
            v-model="showModalConfirmation"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Save Payment
            </div>
            <b-col lg=12>
                Are you sure you want to save this payment?
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.payment.isSaving" variant="primary" @click="onPaymentEntry()">
                    <icon v-if="forms.payment.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Yes
                </b-button>
                <b-button variant="secondary" @click="showModalConfirmation=false">Cancel</b-button>            
            </div>
        </b-modal>
    <checktypeentry type="payment" ref="checktypeentry"></checktypeentry>
    </div>
</template>

<script>
import checktypeentry from '../modals/CheckTypeEntry'
export default {
    name: 'payments',
    components: {
        checktypeentry
    },
    data () {
      return {
            entryMode: 'Add',
            showEntry: false, //if true show entry
            showModalDelete: false,
            showModalConfirmation: false,
            options: {
                tenants: {
                    items: []
                },
                checktypes: {
                    items: []
                },
            },
            forms: {
                payment: {
                    isSaving: false,
                    isDeleting: false,
                    fields: {
                        payment_id: null,
                        transaction_no: null,
                        reference_no: null,
                        tenant_id: null,
                        tenant_code: null,
                        contact_person: null,
                        space_code: null,
                        payment_type: null,
                        amount: 0.00,
                        payment_date: new Date(),
                        check_type_id: 0,
                        check_no: null,
                        check_date: null,
                        deposit_date: null,
                        deposit_no: null,
                        deposit_status: null,
                        remarks: null,
                        contract_advance: 0.00,
                        used_advances: 0.00,
                        discount: 0.00,
                    }
                },
            },
            tables: {
                payments: {
                    fields:[
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle',
                            thStyle: {width: '2%'}
                        },
                        {
                            key:'transaction_no',
                            label: 'Trans No',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key:'reference_no',
                            label: 'Ref. No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '80px'},
                            sortable: true
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'payment_date',
                            label: 'Payment Date',
                            formatter: (value) => {
                                return this.moment(value, 'MMMM DD, YYYY')
                            },
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                        },
                        {
                            key:'used_advance',
                            label: 'Contract Advance',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '10%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'amount_paid',
                            label: 'Amount Paid',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '20%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            tdClass: 'text-center align-middle',
                            thClass: 'text-center',
                            thStyle: {width: '75px'}
                        }
                    ],
                    items: []
                },
            },
            filters: {
                payments: {
                    criteria: null
                }
            },
            paginations: {
                payments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            is_contract_advance: 0,
            is_print: 0,
            print_payment_id: 0,
            total_outstanding_balance: 0,
            total_remaining_balance: 0,
            payment_id: null,
            is_check: false,
            date_from: moment().startOf('month').format('YYYY-MM-DD'),
            date_to: moment().endOf('month').format('YYYY-MM-DD'),
            row: []
        }
    },
    methods:{
        validateForm(){
            var totalAmountPaid = 0
            if(Number(this.forms.payment.fields.amount) + Number(this.forms.payment.fields.used_advances) == 0){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error',
                    text: "The total paid is equal to 0."
                })
                this.showModalConfirmation = false
                return false
            }
            return true
        },
        onPaymentEntry(){
            if(this.validateForm()){
                if(this.entryMode == 'Add'){
                    this.forms.payment.isSaving = true
                    this.resetFieldStates('payment')
                    this.$http.post('api/payment', this.forms.payment.fields,{
                        headers: {
                                Authorization: 'Bearer ' + localStorage.getItem('token')
                            }
                    })
                    .then((response) => {  
                        this.forms.payment.isSaving = false
                        this.clearFields('payment')
                        this.$notify({
                            type: 'success',
                            group: 'notification',
                            title: 'Success!',
                            text: 'The record has been successfully created.'
                        })

                        this.tables.payments.items.unshift(response.data.data)
                        this.print_payment_id = response.data.data.payment_id
                        this.paginations.payments.totalRows++
                        this.forms.payment.fields.used_advances = 0
                        this.forms.payment.fields.contract_advance = 0
                        this.showEntry = false
                        this.showModalConfirmation = false
                        if(this.is_print){
                            this.printAckReceipt(this.print_payment_id)
                        }
                    }).catch(error => {
                        if (!error.response) return
                        const errors = error.response.data.errors
                        var a = 0
                        for (var key in errors) {
                            if(a == 0){
                                this.focusElement(key)
                                this.$notify({
                                    type: 'error',
                                    group: 'notification',
                                    title: 'Error!',
                                    text: errors[key][0]
                                })
                            }
                            a++
                        }
                        this.forms.payment.isSaving = false
                        this.showModalConfirmation = false
                    })
                }
            }
        },
        onPaymentDelete(){
            this.deleteEntity('payment', this.payment_id, true, 'payments', 'payment_id')
        },
        async setDelete(data){
            if(data.item.is_sent == 1){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error',
                    text: "This payment was already locked. You can't cancel it anymore."
                })
                return
            }
            if(await this.checkIfUsed('payment', data.item.payment_id) == true){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error!',
                    text: "Unable to delete, this record is being used by other transactions."
                })
                return
            }
            this.payment_id = data.item.payment_id
            this.showModalDelete = true
        },
        setUpdate(data){
            this.row = data.item
            this.resetFieldStates('payment')
            this.fillEntityForm('payment', data.item.payment_id)
            this.showEntry=true
            this.entryMode='Edit'
        },
        getTenantInfo: async function (value, data) {
            this.forms.payment.fields.amount = 0
            if(data.length > 0){
                var tenant = this.options.tenants.items[data[0].element.index]
                this.forms.payment.fields.tenant_code = tenant.tenant_code
                this.forms.payment.fields.contact_person = tenant.contact_person
                this.forms.payment.fields.space_code = tenant.space_code
            }

            await this.$http.get('api/billing/balance/' + tenant.tenant_id,{
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    const res = response.data
                    this.total_outstanding_balance = res
                })
                .catch(error => {
                    if (!error.response) 
                    return console.log(error)
                })
            
            await this.$http.get('api/payment/advance/' + tenant.tenant_id,{
                headers:{
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            }).then((response) => {
                const res = response.data
                this.forms.payment.fields.contract_advance = res.data.contract_advance[0].contract_advance
    
            }).catch(error => {
                if (!error.response) 
                return console.log(error)
            })

            // this.distributePayment()
        },
        getPaymentType: function (value, data) {
            if(value == 0 || value == 2){
                this.forms.payment.fields.check_type = null
                this.forms.payment.fields.check_no = null
                this.forms.payment.fields.check_date = null
            }
        },
        getDiscount(data){
            if(data.item.is_discounted == 1){
                data.item.discount = data.item.bill_discount
                data.item.amount_paid = Number(data.item.outstanding_balance) - Number(data.item.discount)
            }
            else{
                data.item.discount = 0.00
                data.item.amount_paid = 0.00
            }
        },
        distributePayment(){
            var amount = Number(this.forms.payment.fields.amount) + Number(this.forms.payment.fields.used_advances)
            var balance_paid = 0
            var total_outstanding_balance = 0
            var discount = 0
            this.tables.payment_details.items.forEach(billing => {
                discount += Number(billing.discount)
                amount += Number(billing.discount)
                total_outstanding_balance += Number(billing.outstanding_balance)
                if(amount > Number(billing.outstanding_balance)){
                    billing.amount_paid = Math.max(0, Number(billing.outstanding_balance) - Number(billing.discount))
                    balance_paid += Math.max(0, Number(billing.outstanding_balance) - Number(billing.discount))
                    amount -= Number(billing.outstanding_balance)
                }
                else{
                    billing.amount_paid = Math.max(0, Number(amount) - Number(billing.discount))
                    balance_paid += Math.max(0, Number(amount) - Number(billing.discount))
                    amount = 0
                }
            })
            // this.forms.payment.fields.advance = Math.max(0, (Number(this.forms.payment.fields.amount) + Number(this.forms.payment.fields.used_advances)) - Number(balance_paid)).toFixed(2)
            this.forms.payment.fields.balance_paid = balance_paid
            this.total_outstanding_balance = total_outstanding_balance
        },
        computePayment(){
            var balance = 0
            var totalDiscount = 0
            var totalAmount = 0
            var remaining_balance = 0

            // this.tables.payment_details.items.forEach(billing => {
            //     balance += Math.max(0, Number(billing.outstanding_balance) - Number(billing.discount))
            //     totalDiscount += Number(billing.discount)
            //     totalAmount += Number(billing.amount_paid)
            //     remaining_balance += Number(billing.outstanding_balance) - (Number(billing.discount) + Number(billing.amount_paid))
            // })

            // this.forms.payment.fields.advance = Math.max(0, (Number(this.forms.payment.fields.amount) + Number(this.forms.payment.fields.used_advances)) - Number(balance)).toFixed(2)
            this.forms.payment.fields.discount = totalDiscount
            this.forms.payment.fields.balance_paid = totalAmount
            this.total_remaining_balance = remaining_balance
        },
        isOptionCreating: function(value, data, reference){
            if(value == -1){
                if(reference == 'checktype'){
                    this.$refs.checktypeentry.showModalEntry = true
                    this.$refs.checktypeentry.clearFields('checktype')
                    this.forms.checktypeentry.fields.check_type_id = "null"
                }
            }
        },
        filterPayments(){
            var from = this.moment(this.date_from, 'YYYY-MM-DD')
            var to = this.moment(this.date_to, 'YYYY-MM-DD')
            this.filterTableList('payments', from, to)
        },
        printAckReceipt(payment_id){
            // console.log(this.$store.state.user)
            window.open('api/reports/ackreceipt/'+payment_id+'/'+this.$store.state.user.id)
            // let routeData = this.$router.resolve({name: 'Acknowledgement Receipt', query: {payment_id: payment_id}});
            // window.open(routeData.href, '_blank');
            //this.$router.push({ name: 'soa' })
        },
    },
    created () {
        this.filterTableList('payments', this.date_from, this.date_to)
        this.fillOptionsList('tenants')
        this.fillOptionsList('checktypes')
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('15-57') || this.checkRights('15-58')) == false){
                    this.tables.payments.fields.pop()
                }
            }
            return true
        }
    },
    watch: {
        showEntry: function (showEntry) {
            if(showEntry){
                let self = this
                Vue.nextTick(function(){
                    self.focusElement('reference_no')
                })
            }
        },
    }
  }
</script>
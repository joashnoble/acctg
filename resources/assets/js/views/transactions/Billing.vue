<template>
    <div>
        <notifications group="notification" />
        <div v-show="showEntry === false && showList === true" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Billing List
                                <small class="font-italic">List of all billings.</small>
                                <small>( {{ month_name + ' ' + app_year + ' - ' +  department_name}} )</small>
                                <small>Total billing(s) : {{tables.billings.items.length}}</small>
                            </span>
                            <span class="float-right"><b-btn @click="$refs.billingperiodfilter.showModalPeriod = true" variant="primary"><i class="fa fa-refresh"></i></b-btn></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="3">
                                    <b-button variant="success" 
                                        v-if="checkRights('14-51')"
                                        @click="setEntry(false)">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Billing
                                    </b-button>
                            </b-col>

                            <b-col sm="3">
                                    <b-button variant="success" class="float-right"
                                        v-if="checkRights('14-51')"
                                        @click="setEntry(true)">
                                            <i class="fa fa-file-o"></i> &nbsp; Batch Create Billing     
                                    </b-button>
                            </b-col>
                            <b-col sm="3">
                                 <b-button variant="secondary"
                                    v-if="checkRights('14-54')"
                                    @click="printAllSoa()">
                                        <i class="fa fa-print"></i> Batch Print Billing     
                                </b-button>
                            </b-col>

                            <b-col  sm="3">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.billings.criteria"
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
                                    ref="billingtable"
                                    v-if="checkAction"
                                    responsive
                                    selected-variant="success"
                                    :filter="filters.billings.criteria"
                                    :fields="tables.billings.fields"
                                    :items.sync="tables.billings.items"
                                    :current-page="paginations.billings.currentPage"
                                    :per-page="paginations.billings.perPage"
                                    @filtered="onFiltered($event,'billings')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('14-54')" :size="'sm'" variant="success" @click="printSoa(data)">
                                            <i class="fa fa-print"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('14-52')" :size="'sm'" variant="primary" @click="setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('14-53')" :size="'sm'" variant="danger" @click="!checkIfSent(data.item.is_sent) ?  $refs.deleteentry.setDelete(data.item.billing_id) : ''">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>

                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.billings.totalRows" :per-page="paginations.billings.perPage" v-model="paginations.billings.currentPage"
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
                                Billing Entry - {{entryMode}}
                            </span>
                        </h5>
                        <b-form @keydown="resetFieldStates('billing')" autocomplete="off">
                            <b-row>
                                <b-col sm="9">
                                    <b-card class="card-accent-dark">
                                        <h6 slot="header">
                                            Details
                                        </h6>
                                        <b-row>
                                            <b-col lg="6">
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Billing No. :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Auto" 
                                                                v-model="forms.billing.fields.billing_no"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Tenant : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <select2
                                                                ref="tenant_id"
                                                                @input="getTenantInfo"
                                                                :allowClear="false"
                                                                :placeholder="'Select Tenants'"
                                                                v-model="forms.billing.fields.tenant_id"
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
                                                                v-model="forms.billing.fields.tenant_code"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Contract No : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <select2
                                                                ref="contract_id"
                                                                @input="getContractInfo"
                                                                :allowClear="false"
                                                                :placeholder="'Select Contracts'"
                                                                v-model="forms.billing.fields.contract_id"
                                                            >
                                                                <option v-for="contract in options.contracts.items" :key="contract.contract_id" :value="contract.contract_id">{{contract.contract_no}}</option>
                                                            </select2>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                            </b-col>
                                            <b-col lg="6">
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Commencement Date :</label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Commencement Date" 
                                                                v-model="this.commencement_date"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Termination Date : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                type="text"
                                                                placeholder="Termination Date" 
                                                                v-model="this.termination_date"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Basic Rent : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <vue-autonumeric 
                                                                v-model="forms.billing.fields.contract_fixed_rent"
                                                                :class="'form-control text-right'" 
                                                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                                                readonly>
                                                            </vue-autonumeric>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                                <b-form-group>
                                                    <b-row>
                                                        <b-col lg=4>
                                                            <label class="col-form-label">Due Date : </label>
                                                        </b-col>
                                                        <b-col lg="8">
                                                            <b-form-input
                                                                v-if="$refs.billingperiodfilter != undefined"
                                                                type="text"
                                                                placeholder="Due Date"
                                                                v-model="$refs.billingperiodfilter.forms.period.fields.period_due_date"
                                                                readonly>
                                                            </b-form-input>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>
                                            </b-col>
                                        </b-row>
                                    </b-card>
                                <!-- </b-col>
                                <b-col sm="7"> -->
                                    <b-card class="card-accent-dark">
                                        <b-row slot="header">
                                            <b-col sm="4">
                                                <h6>
                                                    Schedule
                                                </h6>
                                            </b-col>
                                            <b-col sm="4">
                                                <span></span>
                                            </b-col>
                                            <b-col sm="4">
                                                <b-button class="float-right" variant="primary" @click="addSchedule()">
                                                    <i class="fa fa-file-o"></i> &nbsp; Add Schedule
                                                </b-button>    
                                            </b-col>
                                        </b-row>
                                        

                                        <b-row>
                                            <b-col lg="12">
                                                <b-table 
                                                    responsive
                                                    small bordered
                                                    :fields="tables.schedules.fields"
                                                    :items.sync="tables.schedules.items"
                                                    show-empty>
                                                    <template v-slot:cell(month_name)="data">
                                                        <select2
                                                            :allowClear="false"
                                                            :placeholder="'Select Month'"
                                                            v-model="data.item.month_id"
                                                        >
                                                            <option v-for="month in options.months.items" :key="month.month_id" :value="month.month_id">{{month.month_name}}</option>
                                                        </select2>
                                                    </template>
                                                    <!-- <template slot="discounted_amount_due" >
                                                        <vue-autonumeric 
                                                            :class="'form-control text-right'"
                                                            v-model="data.item.discounted_amount_due" 
                                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                        </vue-autonumeric>
                                                    </template> -->
                                                    <template v-slot:cell(amount_due)="data">
                                                        <vue-autonumeric 
                                                            :class="'form-control text-right'"
                                                            v-model="data.item.amount_due" 
                                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                        </vue-autonumeric>
                                                    </template>
                                                    <template v-slot:cell(is_vatted)="data">
                                                        <b-form-checkbox
                                                            v-model="data.item.is_vatted"
                                                            value=1
                                                            unchecked-value=0>
                                                        </b-form-checkbox>
                                                    </template>
                                                    <template v-slot:cell(contract_schedule_notes)="data">
                                                        <b-form-input 
                                                            id="contract_schedule_notes"
                                                            placeholder="Notes"
                                                            v-model="data.item.contract_schedule_notes">
                                                        </b-form-input>
                                                    </template>
                                                    <template v-slot:cell(action)="data">
                                                        <b-btn :size="'sm'" variant="danger" @click="removeSchedule()">
                                                            <i class="fa fa-times-circle"></i>
                                                        </b-btn>
                                                    </template>
                                                </b-table>
                                            </b-col>
                                        </b-row>
                                    </b-card>
                                    <b-card class="card-accent-dark">
                                        <b-row slot="header">
                                            <b-col sm="4">
                                                <h6>
                                                    Charges
                                                </h6>
                                            </b-col>
                                        </b-row>
                                        <b-row class="mb-2">
                                            <b-col sm="4">
                                                <h5>Utility</h5>
                                            </b-col>
                                            <b-col  sm="4">
                                                <span></span>
                                            </b-col>
                                            <b-col  sm="4">
                                                <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('utilities'),charge_type='utilities'">
                                                    <i class="fa fa-file-o"></i> &nbsp; Add Charges
                                                </b-button>
                                            </b-col>
                                        </b-row>
                                        <b-table 
                                            responsive
                                            small bordered
                                            :fields="tables.utilities.fields"
                                            :items.sync="tables.utilities.items"
                                            :sort-by="'sort_key'"
                                            :sort-desc="false"
                                            show-empty>
                                            <template v-slot:cell(contract_rate)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_rate" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_default_reading)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_default_reading" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_is_vatted)="data">
                                                <b-form-checkbox
                                                    v-model="data.item.contract_is_vatted"
                                                    value=1
                                                    unchecked-value=0>
                                                </b-form-checkbox>
                                            </template>
                                            <template v-slot:cell(contract_notes)="data">
                                                <b-form-input 
                                                    placeholder="Notes"
                                                    v-model="data.item.contract_notes">
                                                </b-form-input>
                                            </template>
                                            <template v-slot:cell(action)="data">
                                                <b-btn :size="'sm'" variant="danger" @click="removeCharge('utilities', data.index)">
                                                    <i class="fa fa-times-circle"></i>
                                                </b-btn>
                                            </template>
                                        </b-table>
                                        <b-row class="mb-2">
                                            <b-col sm="4">
                                                <h5>Miscellaneous</h5>
                                            </b-col>
                                        <b-col  sm="4">
                                                <span></span>
                                            </b-col>
                                            <b-col  sm="4">
                                                <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('utilities'),charge_type='miscellaneous'">
                                                    <i class="fa fa-file-o"></i> &nbsp; Add Charges
                                                </b-button>
                                            </b-col>
                                        </b-row>
                                        <b-table 
                                            responsive
                                            small bordered
                                            :fields="tables.miscellaneous.fields"
                                            :items.sync="tables.miscellaneous.items"
                                            :sort-by="'sort_key'"
                                            :sort-desc="false"
                                            show-empty>
                                            <template v-slot:cell(contract_rate)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_rate" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_default_reading)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_default_reading" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_is_vatted)="data">
                                                <b-form-checkbox
                                                    v-model="data.item.contract_is_vatted"
                                                    value=1
                                                    unchecked-value=0>
                                                </b-form-checkbox>
                                            </template>
                                            <template v-slot:cell(contract_notes)="data">
                                                <b-form-input 
                                                    placeholder="Notes"
                                                    v-model="data.item.contract_notes">
                                                </b-form-input>
                                            </template>
                                            <template v-slot:cell(action)="data">
                                                <b-btn :size="'sm'" variant="danger" @click="removeCharge('miscellaneous', data.index)">
                                                    <i class="fa fa-times-circle"></i>
                                                </b-btn>
                                            </template>
                                        </b-table>
                                        <b-row class="mb-2">
                                            <b-col sm="4">
                                                <h5>Other</h5>
                                            </b-col>
                                            <b-col sm="4">
                                                <span></span>
                                            </b-col>
                                            <b-col  sm="4">
                                                <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('utilities'), charge_type='other'">
                                                    <i class="fa fa-file-o"></i> &nbsp; Add Charges
                                                </b-button>
                                            </b-col>
                                        </b-row>
                                        <b-table 
                                            responsive
                                            small bordered
                                            :fields="tables.other.fields"
                                            :items.sync="tables.other.items"
                                            :sort-by="'sort_key'"
                                            :sort-desc="false"
                                            show-empty>
                                            <template v-slot:cell(contract_rate)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_rate" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_default_reading)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_default_reading" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_is_vatted)="data">
                                                <b-form-checkbox
                                                    v-model="data.item.contract_is_vatted"
                                                    value=1
                                                    unchecked-value=0>
                                                </b-form-checkbox>
                                            </template>
                                            <template v-slot:cell(contract_notes)="data">
                                                <b-form-input 
                                                    placeholder="Notes"
                                                    v-model="data.item.contract_notes">
                                                </b-form-input>
                                            </template>
                                            <template v-slot:cell(action)="data">
                                                <b-btn :size="'sm'" variant="danger" @click="removeCharge('other', data.index)">
                                                    <i class="fa fa-times-circle"></i>
                                                </b-btn>
                                            </template>
                                        </b-table>
                                        <b-row class="mb-2">
                                            <b-col sm="4">
                                                <h5>Adjustments</h5>
                                            </b-col>
                                            <b-col sm="4">
                                                <span></span>
                                            </b-col>
                                            <b-col  sm="4">
                                                <!-- <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('utilities'), charge_type='adjustment'">
                                                    <i class="fa fa-file-o"></i> &nbsp; Add Charges
                                                </b-button> -->
                                            </b-col>
                                        </b-row>
                                        <b-table 
                                            responsive
                                            small bordered
                                            :fields="tables.adjustment.fields"
                                            :items.sync="tables.adjustment.items"
                                            :sort-by="'sort_key'"
                                            :sort-desc="false"
                                            show-empty>
                                            <!-- <template v-slot:cell(contract_rate)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_rate" 
                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template>
                                            <template v-slot:cell(contract_default_reading)="data">
                                                <vue-autonumeric 
                                                    :class="'form-control text-right'"
                                                    v-model="data.item.contract_default_reading" 
                                                    :options="{modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </template> -->
                                            <!-- <template v-slot:cell(contract_is_vatted)="data">
                                                <b-form-checkbox
                                                    v-model="data.item.contract_is_vatted"
                                                    value=1
                                                    unchecked-value=0>
                                                </b-form-checkbox>
                                            </template> -->
                                            <!-- <template v-slot:cell(contract_notes)="data">
                                                <b-form-input 
                                                    placeholder="Notes"
                                                    v-model="data.item.contract_notes">
                                                </b-form-input>
                                            </template>
                                            <template v-slot:cell(action)="data">
                                                <b-btn :size="'sm'" variant="danger" @click="removeCharge('other', data.index)">
                                                    <i class="fa fa-times-circle"></i>
                                                </b-btn>
                                            </template> -->
                                        </b-table>
                                    </b-card>
                                </b-col>
                                <b-col sm="3">
                                    <b-card class="card-accent-dark">
                                        <h6 slot="header">
                                            Summary
                                        </h6>
                                        <b-row>
                                            <label @click="showModalHistory = true" class="font-weight-bold col-sm-6 prev-bal"><u>Prev. Balance :</u></label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(previous_balance)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12">Current Billing </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Rental :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(this.forms.billing.fields.total_fixed_rent)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Utility :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(this.forms.billing.fields.total_util_charges)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Miscellaneous :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(this.forms.billing.fields.total_misc_charges)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Other :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(this.forms.billing.fields.total_othr_charges)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Vat Total :</label>
                                        </b-row>
                                        <b-row>
                                            <label class="col-sm-7 text-right">Vatable :</label>
                                            <label class="col-sm-5 text-right"> {{formatNumber(getVatables)}} </label>
                                        </b-row>
                                         <b-row>
                                            <label class="font-weight-bold col-sm-7 text-right col-form-label"> X </label>
                                            <b-col sm="5">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.vat_percent" 
                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12 text-right"> {{formatNumber(getVatTotal)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">WH Tax (Rent) :</label>
                                        </b-row>
                                        <b-row>
                                            <label class="col-sm-7 text-right">Basic Rent :</label>
                                            <label class="col-sm-5 text-right"> {{formatNumber(forms.billing.fields.total_fixed_rent)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-7 text-right col-form-label"> X </label>
                                            <b-col sm="5">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.wtax_percent" 
                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12 text-right"> ({{formatNumber(getWithHoldingTax)}}) </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">WH Tax (Electric) :</label>
                                        </b-row>
                                        <b-row>
                                            <label class="col-sm-7 text-right">Electricity :</label>
                                            <label class="col-sm-5 text-right"> {{formatNumber(getElectricCharge)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-7 text-right col-form-label"> X </label>
                                            <b-col sm="5">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.electric_wtax_percent" 
                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12 text-right"> ({{formatNumber(getElectricWithHoldingTax)}}) </label>
                                        </b-row>
                                        <hr>
                                         <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Sub Total :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(this.forms.billing.fields.sub_total)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Penalty for unpaid balances :</label>
                                        </b-row>
                                        <b-row class="mb-1"> 
                                            <label class="col-sm-5 text-right"></label>
                                            <b-col sm="7">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.interested_amount" 
                                                    :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-7 text-right col-form-label"> X </label>
                                            <b-col sm="5">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.interest_percent" 
                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12 text-right"> {{formatNumber(getInterestTotal)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Penalty for late payment :</label>
                                        </b-row>
                                        <b-row class="mb-1">
                                            <label class="col-sm-5 text-right"></label>
                                            <b-col sm="7">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.penaltied_amount" 
                                                    :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-7 text-right col-form-label"> X </label>
                                            <b-col sm="5">
                                                <vue-autonumeric 
                                                    :class="'text-right form-control'" 
                                                    v-model="forms.billing.fields.penalty_percent" 
                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                </vue-autonumeric>
                                            </b-col>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-12 text-right"> {{formatNumber(getPenaltyTotal)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Adj. Total In :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(forms.billing.fields.total_adjusted_in)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6 text-right">Adj. Total Out :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> ({{formatNumber(forms.billing.fields.total_adjusted_out)}}) </label>
                                        </b-row>
                                        <hr>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6">Total Due :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(getTotalDue)}} </label>
                                        </b-row>
                                        <b-row>
                                            <label class="font-weight-bold col-sm-6">End Balance :</label>
                                            <label class="font-weight-bold col-sm-6 text-right"> {{formatNumber(getEndingBalance)}} </label>
                                        </b-row>
                                    </b-card>
                                </b-col>                     
                            </b-row>
                        </b-form>
                        <template v-slot:footer>
                            <b-row class="pull-right mt-2">
                                <b-col sm="12">
                                    <b-button 
                                        :disabled="forms.billing.isSaving" 
                                        variant="success" 
                                        @click="onBillingEntry">
                                        <icon v-if="forms.billing.isSaving" name="sync" spin></icon>
                                        <i class="fa fa-check"></i>
                                        Save
                                    </b-button>
                                    <b-button variant="danger" @click="showEntry=false">Close</b-button>
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <b-modal 
            v-model="showModalCharges"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Charges
            </div>
            <b-col lg=12>
                <b-row class='mb-2'>
                    <b-col  sm="5">
                        <span></span>
                    </b-col>

                    <b-col  sm="7">
                         <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.charges.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                    </b-col>
                </b-row>
                <b-table 
                    responsive
                    small bordered
                    :filter="filters.charges.criteria"
                    :fields="tables.charges.fields"
                    :items.sync="tables.charges.items"
                    :current-page="paginations.charges.currentPage"
                    :per-page="paginations.charges.perPage"
                    @filtered="onFiltered($event, 'charges')"
                    show-empty>
                    <template v-slot:cell(is_selected)="data">
                        <b-btn :size="'sm'" title="Add" variant="primary" @click="addCharges(charge_type, data.item)">
                            <i class='fa fa-plus-square'></i>
                        </b-btn>
                    </template>
                </b-table>
                <b-pagination
                    :align="'right'"
                    :total-rows="paginations.charges.totalRows"
                    :per-page="paginations.charges.perPage"
                    v-model="paginations.charges.currentPage" />
            </b-col>
            <div slot="modal-footer">
                <!-- <b-button variant="primary" @click="addCharges(charge_type)">
                    <i class="fa fa-check"></i>
                    Add
                </b-button> -->
                <b-button variant="danger" @click="showModalCharges=false">Close</b-button>            
            </div>
        </b-modal>
        <b-modal 
            v-model="showModalConfirmation"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Batch Billing
            </div>
            <b-col lg=12>
                Are you sure you want to batch create all the billings?
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.billing.isSaving" variant="success" @click="onBatchBilling">
                    <icon v-if="forms.billing.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    OK
                </b-button>
                <b-button variant="danger" @click="showModalConfirmation=false">Close</b-button>            
            </div>
        </b-modal>
        <b-modal 
            size="lg"
            ref="history"
            id="modal-history"
            v-model="showModalHistory"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="checkScrollBar()"
        >
            <div slot="modal-title">
                Tenant History
            </div>
            <b-col lg=12>
                <b-btn class="mb-3" variant="success" v-if="sbButton" @click="navigateButton('End')">Go to Bottom</b-btn>
                <b-table 
                    responsive
                    small bordered
                    :fields="tables.tenant_history.fields"
                    :items.sync="tables.tenant_history.items"
                    show-empty>
                </b-table>
                <b-btn class="mt-3" variant="success" v-if="sbButton" @click="navigateButton('Home')">Go to Top</b-btn>
            </b-col>
            <div slot="modal-footer">
                <b-button variant="danger" @click="showModalHistory=false">Close</b-button>            
            </div>
        </b-modal>
        <billingperiodfilter type="billing" ref="billingperiodfilter"></billingperiodfilter>
        <deleteentry entity="billing" table="billings" primary_key="billing_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import billingperiodfilter from '../modals/BillingPeriodFilter'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'billing',
    components: {
        billingperiodfilter,
        deleteentry,
    },
    data () {
        return {
            entryMode: 'Add',
            showList: false,
            showEntry: false,
            showModalCharges: false,
            showModalHistory: false,
            showModalConfirmation: false,
            month_name: '',
            app_year: '',
            options: {
                months: {
                    items: []
                },
                tenants: {
                    items: []
                },
                contracts: {
                    items: []
                }
            },
            tables: {
                billings: {
                    fields: [
                        {
                            key: 'billing_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'billing_no',
                            label: 'Billing No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'contract_no',
                            label: 'Contract No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'app_year',
                            label: 'App Year/Month',
                            tdClass: 'align-middle',
                            thStyle: {width: '150px'},
                            formatter: (value, key, item) => {
                                return item.app_year + '/' + item.month_name
                            }
                        },
                        {
                            key: 'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key: 'tenant_code',
                            label: 'Tenant Code',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                        },
                        {
                            key: 'total_amount_due',
                            label: 'Total Amount Due',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '15%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '115px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
                charges: {
                    fields: [
                        {
                            key: 'charge_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key:'charge_code',
                            label: 'Charge Code',
                            thStyle: {width: '25%'},
                            tdClass: 'align-middle'
                        },
                        {
                            key:'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle'
                        },
                        {
                            key: 'is_selected',
                            label: '',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '5px'}
                        },
                    ],
                    items:[]
                },
                schedules: {
                    fields: [
                        {
                            key: 'count',
                            label: '',
                            tdClass: 'align-middle text-center',
                            thStyle: {width: '25px'},
                        },
                        {
                            key: 'month_name',
                            label: 'Month',
                            thStyle: {width: '20%'},
                            tdClass: 'align-middle'
                        },
                        {
                            key: 'app_year',
                            label: 'Year',
                            tdClass: 'align-middle',
                            thStyle: {width: '5%'},
                        },
                        // {
                        //     key: 'discounted_amount_due',
                        //     label: 'Disc Amount Due',
                        //     thClass: 'text-right',
                        //     tdClass: 'text-right align-middle',
                        //     thStyle: {width: '17%'},
                        //     formatter: (value) => {
                        //         return this.formatNumber(value)
                                
                        //     }
                        // },
                        {
                            key: 'amount_due',
                            label: 'Amount Due',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                                
                            }
                        },
                        {
                            key: 'is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '10%'},
                        },
                        {
                            key: 'contract_schedule_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '50px'},
                        }
                    ],
                    items: []
                },
                utilities: {
                    fields: [
                        {
                            key: 'charge_id',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'sort_key',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none',
                            sortable: true
                        },
                        {
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '18%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'}
                        },
                        {
                            key: 'contract_line_total',
                            label: 'Line Total',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value, key, item) => {
                                item.contract_line_total = item.contract_rate * item.contract_default_reading
                                return this.formatNumber(item.contract_line_total)
                            }

                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '10%'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '50px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
                miscellaneous: {
                    fields: [
                        {
                            key: 'charge_id',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'sort_key',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none',
                            sortable: true
                        },
                        {
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '18%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'}
                        },
                        {
                            key: 'contract_line_total',
                            label: 'Line Total',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value, key, item) => {
                                item.contract_line_total = item.contract_rate * item.contract_default_reading
                                return this.formatNumber(item.contract_line_total)
                            }

                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '10%'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '50px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
                other: {
                    fields: [
                        {
                            key: 'charge_id',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'sort_key',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none',
                            sortable: true
                        },
                        {
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '18%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'}
                        },
                        {
                            key: 'contract_line_total',
                            label: 'Line Total',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value, key, item) => {
                                item.contract_line_total = item.contract_rate * item.contract_default_reading
                                return this.formatNumber(item.contract_line_total)
                            }

                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '10%'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '50px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
                adjustment : {
                    fields: [
                        {
                            key: 'charge_id',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'sort_key',
                            label: '',
                            thClass: 'd-none',
                            tdClass: 'd-none',
                            sortable: true
                        },
                        {
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '18%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '15%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'contract_line_total',
                            label: 'Line Total',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '17%'},
                            formatter: (value, key, item) => {
                                item.contract_line_total = item.contract_rate * item.contract_default_reading
                                return this.formatNumber(item.contract_line_total)
                            }

                        },
                        {
                            key: 'adjustment_type',
                            label: 'Adj. Type',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '100px'},
                            formatter: (value) => {
                                if(value == 0){
                                    return 'IN'
                                }
                                else {
                                    return 'OUT'
                                }
                            }
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        // {
                        //     key: 'action',
                        //     label: 'Action',
                        //     thClass: 'text-center',
                        //     thStyle: {width: '50px'},
                        //     tdClass: 'text-center align-middle'
                        // }
                    ],
                    items: []
                },
                tenant_history: {
                    fields: [
                        {
                            key: 'date_created',
                            label: 'Date',
                            formatter: (value) => {
                                return this.moment(value, 'MMMM DD, YYYY')
                            }
                        },
                        {
                            key: 'reference_no',
                            label: 'Reference No'
                        },
                        {
                            key: 'app_date',
                            label: 'App Date/Note'
                        },
                        {
                            key: 'debit',
                            label: 'Debit',
                            thClass: 'text-right',
                            tdClass: 'text-right',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'credit',
                            label: 'Credit',
                            thClass: 'text-right',
                            tdClass: 'text-right',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'running_balance',
                            label: 'Balance',
                            thClass: 'text-right',
                            tdClass: 'text-right',
                            formatter: (value) => {
                                return this.formatNumber(Math.abs(value))
                            }
                        }
                    ],
                    items: []
                }
            },
            forms: {
                billing: {
                    isSaving: false,
                    fields: {
                        billing_id: null,
                        billing_no: null,
                        tenant_id: null,
                        tenant_code: null,
                        period_id: null,
                        app_year: null,
                        month_id: null,
                        contract_id: null,
                        department_id: null,
                        contract_no: null,
                        contract_discounted_rent: null,
                        commencement_date: null,
                        termination_date: null,
                        contract_fixed_rent: null,
                        due_date: null,
                        total_discounted_rent: 0,
                        total_fixed_rent: 0,
                        total_util_charges: 0,
                        total_misc_charges: 0,
                        total_othr_charges: 0,
                        sub_total: 0,
                        vatable_amount: 0,
                        discounted_vatable_amount: 0,
                        interested_amount: 0,
                        interest_percent: 3,
                        interest_total: 0,
                        penaltied_amount: 0,
                        penalty_percent: 3,
                        penalty_total: 0,
                        vat_percent: 12,
                        total_vat: 0,
                        total_amount_due: 0,
                        discounted_total_amount_due: 0,
                        total_adjusted_in: 0,
                        total_adjusted_out: 0,
                        wtax_amount: 0,
                        wtax_percent: 5,
                        electric_wtax_amount: 0,
                        electric_wtax_percent: 2,
                        electric_charge_id: 0,
                        is_auto: 0,
                        is_withhold_electricity: 0,
                        schedules: [],
                        utilities: [],
                        miscellaneous: [],
                        other: [],
                        adjustment: []
                    }
                }
            },
            filters: {
                billings: {
                    criteria: null
                },
                charges: {
                    criteria: null
                }
            },
            paginations: {
                billings: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                },
                charges: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            counter: 0,
            charge_type: null,
            previous_balance: 0,
            previous_sub_total: 0,
            prev_previous_balance: 0,
            late_payment: 0,
            payment_interest: 0,
            billing_id: null,
            is_check_all: 0,
            is_selectable: false,
            sbButton: true,
            department_name: '',
            row: []
        }
    },
    methods:{
        checkScrollBar(){
            var modal = document.querySelector("#modal-history")
            if(window.innerHeight >= modal.scrollHeight){
                this.sbButton = false
            }
            else{
                this.sbButton = true
            }
        },
        navigateButton(position){
            var modal = document.querySelector("#modal-history")
            if(position == 'Home'){
                modal.scrollTop = 0
            }
            else{
                modal.scrollTop = modal.scrollHeight
            }
        },
        async onBillingEntry () {
            this.forms.billing.fields.schedules = this.tables.schedules.items
            this.forms.billing.fields.utilities = this.tables.utilities.items
            this.forms.billing.fields.miscellaneous = this.tables.miscellaneous.items
            this.forms.billing.fields.other = this.tables.other.items
            this.forms.billing.fields.adjustments = this.tables.adjustment.items
            this.forms.billing.fields.period_id = this.$refs.billingperiodfilter.forms.period.fields.period_id
            this.forms.billing.fields.due_date = this.$refs.billingperiodfilter.forms.period.fields.period_due_date
            this.forms.billing.fields.app_year = this.$refs.billingperiodfilter.forms.period.fields.app_year
            this.forms.billing.fields.month_id = this.$refs.billingperiodfilter.forms.period.fields.month_id
            

            if(this.entryMode == 'Add'){
                await this.createEntity('billing', false, 'billings', true)
            }
            else{
                await this.updateEntity('billing', 'billing_id', false, this.row)
            }
        },
        checkIfSent(is_sent){
            if(is_sent == 1){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error',
                    text: "This billing was already locked. You can't edit or delete this billing."
                })
                return true
            }
            return false
        },
        async setUpdate(data){
            if(!this.checkIfSent(data.item.is_sent)){
                this.row = data.item
                this.getTenantHistory(data.item.tenant_id)
                this.getPrevBalance(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, data.item.tenant_id, this.forms.billing.fields.department_id)
                // this.getPrevSubTotal(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, data.item.tenant_id)
                // this.getPrevPrevBalance(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, data.item.tenant_id)
                this.filterOptionsList('contracts', data.item.tenant_id)
                await this.$http.get('/api/billingSC/sc/'+ data.item.billing_id,{
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    const res = response.data
                    this.tables.schedules.items = res.schedules
                    this.tables.utilities.items = res.util_charges
                    this.tables.miscellaneous.items = res.misc_charges
                    this.tables.other.items = res.othr_charges
                    this.tables.adjustment.items = res.adjustments
                    this.fillEntityForm('billing', data.item.billing_id)
                    this.showEntry=true
                    this.entryMode='Edit'
                    this.counter = this.tables.schedules.items.length
                })
                .catch(error => {
                if (!error.response) return
                console.log(error)
                })
            }
        },
        addSchedule(){
            try {
                var amount_due = 0
                var discounted_amount_due = 0
                if(this.tables.schedules.items.length > 0){
                    var default_rent = this.tables.schedules.items[0]
                    amount_due = default_rent.amount_due
                    discounted_amount_due = default_rent.discounted_amount_due
                }

                var month;
                
                this.counter++;

                this.tables.schedules.items.push({
                    count: this.counter, 
                    month_id: this.$refs.billingperiodfilter.forms.period.fields.month_id, 
                    app_year: this.$refs.billingperiodfilter.forms.period.fields.app_year, 
                    // fixed_rent: fixed_rent,
                    discounted_amount_due: discounted_amount_due,
                    amount_due: amount_due, 
                    is_vatted: 1, 
                    contract_schedule_notes: ''
                });
            } 
            catch(e) {
                console.log(e);
            }
        },
        removeSchedule(){
            this.tables.schedules.items.splice(this.tables.schedules.items.length - 1, 1)
            this.counter--
        },
        addCharges(charge_type, row){
            try {
                this.tables[charge_type].items.push({
                    charge_id: row.charge_id,
                    charge_desc: row.charge_desc,
                    contract_rate: 0,
                    contract_default_reading: 0,
                    contract_is_vatted: 0,
                    contract_notes:'',
                    sort_key:row.sort
                })
            }
            catch(e){
                console.log(e)
            }
        },
        removeCharge(charge_type, index){
            this.tables[charge_type].items.splice(index, 1)
        },
        clearCharges(charge_type){
            this.filters.charges.criteria = null
        },
        getTenantHistory(tenant_id){
            this.$http.get('api/tenanthistory/'+tenant_id,{
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                const res = response.data
                this.tables.tenant_history.items = res
            })
            .catch(error => {
                if (!error.response) 
                return console.log(error)
            })
        },
        getPrevBalance(month_id, app_year, tenant_id, department_id){
            this.$http.get('api/billing/'+month_id+'/'+app_year+'/'+tenant_id+'/'+department_id,{
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                const res = response.data
                this.previous_balance = res[0]['prevBalance'];
            })
            .catch(error => {
                if (!error.response) 
                return console.log(error)
            })
        },
        // getPrevSubTotal(month_id, app_year, tenant_id){
        //     this.$http.get('api/billing/sub_total/'+month_id+'/'+app_year+'/'+tenant_id,{
        //             headers: {
        //                 Authorization: 'Bearer ' + localStorage.getItem('token')
        //             }
        //     })
        //     .then((response) => {
        //         const res = response.data
        //         this.previous_sub_total = res[0]['subTotal'];
        //     })
        //     .catch(error => {
        //         if (!error.response) 
        //         return console.log(error)
        //     })
        // },
        // getPrevPrevBalance(month_id, app_year, tenant_id){
        //     if(month_id == 1){
        //         month_id = 13
        //     }
        //     var month = Number(month_id) - 1
        //     this.$http.get('api/billing/'+month+'/'+app_year+'/'+tenant_id,{
        //             headers: {
        //                 Authorization: 'Bearer ' + localStorage.getItem('token')
        //             }
        //     })
        //     .then((response) => {
        //         const res = response.data
        //         this.prev_previous_balance = res[0]['prevBalance'];
        //     })
        //     .catch(error => {
        //         if (!error.response) 
        //         return console.log(error)
        //     })
        // },
        // getPaymentInterest(month_id, app_year, tenant_id){
        //     this.$http.get('api/payment/interest/'+month_id+'/'+app_year+'/'+tenant_id,{
        //             headers: {
        //                 Authorization: 'Bearer ' + localStorage.getItem('token')
        //             }
        //     })
        //     .then((response) => {
        //         const res = response.data
        //         this.payment_interest = res.data[0].payment
        //     })
        //     .catch(error => {
        //         if (!error.response) 
        //         return console.log(error)
        //     })
        // },
        getLatePayment(month_id, app_year, tenant_id, department_id){
            this.$http.get('api/payment/'+month_id+'/'+app_year+'/'+tenant_id+'/'+department_id,{
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                const res = response.data
                this.late_payment = res[0]['latePayment'];
            })
            .catch(error => {
                if (!error.response) 
                return console.log(error)
            })
        },
        
        getApprovedAdjustments(tenant_id,period_id){
            this.$http.get('api/adjustments/'+tenant_id+'/'+period_id,{
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                const res = response.data
                res.data.forEach(adj => {
                    this.tables.adjustment.items.push({
                        charge_id: adj.charge_id, 
                        charge_desc: adj.charge_desc, 
                        contract_rate: adj.adjustment_rate, 
                        contract_default_reading: adj.adjustment_reading,
                        contract_line_total: adj.amount, 
                        contract_is_vatted: 0, 
                        adjustment_type: adj.adjustment_type,
                        contract_notes: adj.notes,
                        sort_key: adj.sort
                    });
                });
            })
            .catch(error => {
                if (!error.response) 
                return console.log(error)
            })
        },
        
        getTenantInfo: function (value, data) {
            if(data.length > 0){
                this.tables.schedules.items = []
                this.tables.utilities.items = []
                this.tables.miscellaneous.items = []
                this.tables.other.items = []
                this.tables.adjustment.items = []
                var tenant = this.options.tenants.items[data[0].element.index]
                this.forms.billing.fields.tenant_code = tenant.tenant_code
                this.forms.billing.fields.is_auto = tenant.is_auto
                this.forms.billing.fields.is_withhold_electricity = tenant.is_withhold_electricity
                if(tenant.is_vatted == 1){
                    this.forms.billing.fields.vat_percent = tenant.vat_percent
                }
                this.getTenantHistory(tenant.tenant_id)
                this.filterOptionsList('contracts', tenant.tenant_id)
                this.getApprovedAdjustments(tenant.tenant_id, this.$refs.billingperiodfilter.forms.period.fields.period_id)
                this.previous_balance = 0
                // this.getPaymentInterest(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, tenant.tenant_id)
            }
        },
        getContractInfo: function (value, data) {
            if(data.length > 0){
                if(data.length > 0){
                    var contract = this.options.contracts.items[data[0].element.index]
                    this.forms.billing.fields.commencement_date = moment(contract.commencement_date).format('MMMM DD, YYYY')
                    this.forms.billing.fields.termination_date = moment(contract.termination_date).format('MMMM DD, YYYY')
                    this.forms.billing.fields.contract_fixed_rent = contract.contract_fixed_rent
                    this.forms.billing.fields.department_id = contract.department_id

                    var nmonth_id = Number(this.$refs.billingperiodfilter.forms.period.fields.month_id)
                    var nyear = this.$refs.billingperiodfilter.forms.period.fields.app_year
                    if(nmonth_id == 13){
                        nmonth_id = 1
                        nyear++
                    }
                    this.$http.get('/api/contracts/' + value + '/' + nyear + '/' + nmonth_id + '/' + this.forms.billing.fields.tenant_id,{
                        headers: {
                                Authorization: 'Bearer ' + localStorage.getItem('token')
                            }
                    })
                    .then((response) => {
                        const res = response.data
                        this.tables.schedules.items = res.schedules
                        this.tables.utilities.items = res.util_charges
                        this.tables.miscellaneous.items = res.misc_charges
                        this.tables.other.items = res.othr_charges

                        var balance = Number(this.previous_balance) + Number(this.late_payment)
                        
                        if(balance > 0){
                            this.forms.billing.fields.penaltied_amount = Number(this.late_payment)
                            if(this.forms.billing.fields.is_auto == 1){
                                this.forms.billing.fields.interested_amount = Math.max(0, (Number(this.previous_balance)))
                            }
                        }
                        this.counter = this.tables.schedules.items.length
                    })
                    .catch(error => {
                        if (!error.response) 
                        return console.log(error)
                    })
                    this.getPrevBalance(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, this.forms.billing.fields.tenant_id, this.forms.billing.fields.department_id)
                    // this.getPrevSubTotal(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, tenant.tenant_id)
                    // this.getPrevPrevBalance(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, this.forms.billing.fields.tenant_id)
                    this.getLatePayment(this.$refs.billingperiodfilter.forms.period.fields.month_id, this.$refs.billingperiodfilter.forms.period.fields.app_year, this.forms.billing.fields.tenant_id, this.forms.billing.fields.department_id)
                }
            }
        },
        printSoa(data){
            window.open('api/reports/soa/'+data.item.billing_id)
            // let routeData = this.$router.resolve({name: 'Soa', query: {billing_id: data.item.billing_id}});
            // window.open(routeData.href, '_blank');
            //this.$router.push({ name: 'soa' })
        },
        printAllSoa(){
            if(this.tables.billings.items.length == 0){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error',
                    text: "No billing to print. Create billing first."
                })
                return
            }
            window.open('api/reports/allsoa/'+this.$refs.billingperiodfilter.forms.period.fields.period_id)
        },
        toggleSelectAll(){
            if(this.is_check_all == 0){
                this.tables.charges.items.forEach(charge => {
                    charge.is_selected = true
                })
                this.is_check_all = 1
            }
            else{
                this.tables.charges.items.forEach(charge => {
                    charge.is_selected = false
                })
                this.is_check_all = 0
            }
        },
        toggleAllBilling(){
            this.$refs.billingtable.selectAllRows()
        },
        onBatchBilling(){
            this.forms.billing.isSaving = true
            this.$http.get('api/batchbilling/' + this.$refs.billingperiodfilter.forms.period.fields.period_id, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            }).then(response => {
                this.forms.billing.isSaving = false
                this.showBillingList()
                this.showModalConfirmation = false
            }).catch(err => {
                this.forms.billing.isSaving = true
                console.log(err)
            })
        },
        setEntry(is_all){ 
            if(is_all){
                this.showModalConfirmation = true
            }
            else{
                this.showEntry = true
                this.entryMode='Add'
                this.tables.schedules.items=[]
                this.tables.utilities.items=[]
                this.tables.miscellaneous.items=[]
                this.tables.adjustment.items=[]
                this.tables.other.items=[]
                this.clearFields('billing')
                this.forms.billing.fields.vat_percent = 0
                this.forms.billing.fields.interest_percent = 3
                this.forms.billing.fields.penalty_percent = 3
                this.forms.billing.fields.wtax_percent = 5
                this.forms.billing.fields.electric_wtax_percent = 2
                this.counter = 0 
            }
        },
        showBillingList(){
            this.month_name = this.$refs.billingperiodfilter.forms.period.fields.month_name
            this.app_year = this.$refs.billingperiodfilter.forms.period.fields.app_year
            if(this.$refs.billingperiodfilter.forms.period.fields.department_id != 0){
                this.department_name = this.options.departments.items.filter(i => i.department_id == this.$refs.billingperiodfilter.forms.period.fields.department_id)[0]
            }
            else{
                this.department_name = "All Departments"
            }
            if(this.$refs.billingperiodfilter.forms.period.fields.period_id != null && this.$refs.billingperiodfilter.forms.period.fields.department_id != null){
                this.filterTableList('billings', this.$refs.billingperiodfilter.forms.period.fields.period_id, this.$refs.billingperiodfilter.forms.period.fields.department_id)
                this.showList = true
                this.$refs.billingperiodfilter.showModalPeriod = false
            }
        }
    },
    computed :{
        commencement_date: function(){
            if(this.forms.billing.fields.commencement_date == null){
                return null
            }
            this.forms.billing.fields.commencement_date = new Date(this.forms.billing.fields.commencement_date)
            this.forms.billing.fields.commencement_date = moment(this.forms.billing.fields.commencement_date).format("MMMM DD, YYYY")
            return this.forms.billing.fields.commencement_date
        },
        termination_date: function(){
            if(this.forms.billing.fields.termination_date == null){
                return null
            }
            this.forms.billing.fields.termination_date = new Date(this.forms.billing.fields.termination_date)
            this.forms.billing.fields.termination_date = moment(this.forms.billing.fields.termination_date).format("MMMM DD, YYYY")
            return this.forms.billing.fields.termination_date
        },
        getTotalDue: function(){
            var schedTotal = 0
            var discSchedTotal = 0
            var utilTotal = 0
            var miscTotal = 0
            var othrTotal = 0
            var adjustmentInTotal = 0
            var adjustmentOutTotal = 0

            this.tables.schedules.items.forEach(schedule => {
                if(schedule != null){
                    schedTotal += Number(schedule.amount_due)
                    discSchedTotal += Number(schedule.discounted_amount_due)
                }        
            })

            this.tables.utilities.items.forEach(util => {
                if(util != null){
                    utilTotal += Number(util.contract_rate  * util.contract_default_reading)
                }
            })

            this.tables.miscellaneous.items.forEach(misc => {
                if(misc != null){
                    miscTotal += Number(misc.contract_rate * misc.contract_default_reading)
                }
            })

            this.tables.other.items.forEach(othr => {
                if(othr != null){
                    othrTotal += Number(othr.contract_rate * othr.contract_default_reading)
                }   
            })

            this.tables.adjustment.items.forEach(adj => {
                if(adj != null){
                    if(adj.adjustment_type == 0){
                        adjustmentInTotal += Number(adj.contract_rate * adj.contract_default_reading)
                    }
                    else{
                        adjustmentOutTotal += Math.abs(Number(adj.contract_rate * adj.contract_default_reading))
                    }
                }   
            })

            this.forms.billing.fields.total_fixed_rent = schedTotal
            this.forms.billing.fields.total_discounted_rent = discSchedTotal
            this.forms.billing.fields.total_util_charges = utilTotal
            this.forms.billing.fields.total_misc_charges = miscTotal
            this.forms.billing.fields.total_othr_charges = othrTotal
            this.forms.billing.fields.total_adjusted_in = adjustmentInTotal
            this.forms.billing.fields.total_adjusted_out = adjustmentOutTotal

            this.forms.billing.fields.sub_total = Number(this.forms.billing.fields.total_fixed_rent) + Number(this.forms.billing.fields.total_util_charges) + Number(this.forms.billing.fields.total_misc_charges) + Number(this.forms.billing.fields.total_othr_charges) + Number(this.forms.billing.fields.total_vat)

            var discounted_sub_total = Number(this.forms.billing.fields.total_discounted_rent) + Number(this.forms.billing.fields.total_util_charges) + Number(this.forms.billing.fields.total_misc_charges) + Number(this.forms.billing.fields.total_othr_charges)


            this.forms.billing.fields.total_amount_due = Number(this.forms.billing.fields.sub_total) + Number(this.getInterestTotal) + Number(this.getPenaltyTotal) + Number(this.forms.billing.fields.total_adjusted_in) - Number(this.forms.billing.fields.total_adjusted_out)

            this.forms.billing.fields.discounted_total_amount_due = Number(discounted_sub_total) + Number(this.getInterestTotal) + Number(this.getPenaltyTotal) + Number(this.getDiscountedVatTotal) - Number(this.getDiscountedWithHoldingTax) + Number(this.forms.billing.fields.total_adjusted_in) - Number(this.forms.billing.fields.total_adjusted_out)

            return this.forms.billing.fields.total_amount_due
        },
        getVatables : function(){
            var schedVat = 0
            var utilVat = 0
            var miscVat = 0
            var othrVat = 0
            var dSchedVat = 0

            this.tables.schedules.items.forEach(schedule => {
                if(schedule != null){
                    if(schedule.is_vatted == 1){
                        schedVat += Number(schedule.amount_due)
                        dSchedVat += Number(schedule.discounted_amount_due)
                    }
                }
            })

            this.tables.utilities.items.forEach(util => {
                if(util != null){
                    if(util.contract_is_vatted == 1){
                        utilVat += util.contract_rate  * util.contract_default_reading
                    }
                }
            })

            this.tables.miscellaneous.items.forEach(misc => {
                if(misc != null){
                    if(misc.contract_is_vatted == 1){
                        miscVat += misc.contract_rate * misc.contract_default_reading
                    }
                }
            })

            this.tables.other.items.forEach(othr => {
                if(othr != null){
                    if(othr.contract_is_vatted == 1){
                        othrVat += othr.contract_rate * othr.contract_default_reading
                    }
                }
            })
            
            this.forms.billing.fields.vatable_amount = Number(schedVat) + Number(utilVat) + Number(miscVat) + Number(othrVat)

            this.forms.billing.fields.discounted_vatable_amount = Number(dSchedVat) + Number(utilVat) + Number(miscVat) + Number(othrVat)

            return this.forms.billing.fields.vatable_amount
        },

        getVatTotal: function(){
            this.forms.billing.fields.total_vat = this.forms.billing.fields.vatable_amount * (this.forms.billing.fields.vat_percent / 100)
            return this.forms.billing.fields.total_vat
        },

        getDiscountedVatTotal: function(){
            var discounted_total_vat = this.forms.billing.fields.discounted_vatable_amount * (this.forms.billing.fields.vat_percent / 100)
            return discounted_total_vat
        },
        getElectricCharge: function(){
            var electric_wtax_amount = 0

            if(this.forms.billing.fields.is_withhold_electricity == 1){
                this.tables.other.items.forEach(othr => {
                    if(othr != null){
                        if(othr.charge_id == this.electric_charge_id){
                            electric_wtax_amount += othr.contract_rate * othr.contract_default_reading
                        }
                    }
                })

                this.tables.miscellaneous.items.forEach(misc => {
                    if(misc != null){
                        if(misc.charge_id == this.electric_charge_id){
                            electric_wtax_amount += misc.contract_rate * misc.contract_default_reading
                        }
                    }
                })

                this.tables.utilities.items.forEach(util => {
                    if(util != null){
                        if(util.charge_id == this.electric_charge_id){
                            electric_wtax_amount += util.contract_rate * util.contract_default_reading
                        }
                    }
                })
            }
            return electric_wtax_amount
        },
        getElectricWithHoldingTax: function(){
            this.forms.billing.fields.electric_wtax_amount = this.getElectricCharge * (this.forms.billing.fields.electric_wtax_percent / 100)
            return this.forms.billing.fields.electric_wtax_amount
        },
        getWithHoldingTax: function(){
            this.forms.billing.fields.wtax_amount = this.forms.billing.fields.total_fixed_rent * (this.forms.billing.fields.wtax_percent / 100)
            return this.forms.billing.fields.wtax_amount
        },
        getDiscountedWithHoldingTax: function(){
            var discounted_withholding_tax = this.forms.billing.fields.total_discounted_rent * (this.forms.billing.fields.wtax_percent / 100)
            return discounted_withholding_tax
        },

        getInterestTotal: function(){
            this.forms.billing.fields.interest_total = Number(this.forms.billing.fields.interested_amount * (this.forms.billing.fields.interest_percent / 100)).toFixed(2)
            return this.forms.billing.fields.interest_total
        },

        getPenaltyTotal: function(){
            this.forms.billing.fields.penalty_total = Number(this.forms.billing.fields.penaltied_amount * (this.forms.billing.fields.penalty_percent / 100)).toFixed(2)
            return this.forms.billing.fields.penalty_total
        },

        getEndingBalance: function(){
            var endingBalance = this.forms.billing.fields.total_amount_due + Number(this.previous_balance)
            return endingBalance
        },

        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('14-52') || this.checkRights('14-53') || this.checkRights('14-54')) == false){
                    this.tables.billings.fields.pop()
                }
            }
            return true
        },
        
    },
    created(){
        this.fillTableList('charges')
        this.fillOptionsList('months')
        this.fillOptionsList('tenants')
        this.$http.get('/api/companysettingnotes', {
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token'),
                }
        }).then(response => {
            this.electric_charge_id = response.data.company.electric_charge_id
        }).catch(err => {
            if (!error.response) return
            console.log(error)
        })
    },
    watch: {
        showEntry: function (showEntry) {
            if(showEntry){
                let self = this
                Vue.nextTick(function(){
                    self.focusElement('tenant_id')
                })
            }
        },
    },
}
</script>
<style scoped lang="css">
  .prev-bal {
    cursor:pointer;
  }
</style>
<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <notifications group="notification" />
        <div v-show="showEntry === false" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Contract List
                                <small class="font-italic">List of all registered contracts.</small>
                                <small>Total contract(s) : {{tables.contracts.items.length}}</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button variant="success" 
                                        v-if="checkRights('13-47')"
                                        @click="showEntry = true, entryMode='Add', tables.schedules.items=[], tables.utilities.items=[], tables.miscellaneous.items=[], tables.other.items=[], tabIndex=0, clearFields('contract'), forms.contract.fields.contract_terms = 1, forms.contract.fields.schedule_type = 1, computeDates(true),counter = 0 ">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Contract
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.contracts.criteria"
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
                                    :filter="filters.contracts.criteria"
                                    :fields="tables.contracts.fields"
                                    :items.sync="tables.contracts.items"
                                    :current-page="paginations.contracts.currentPage"
                                    :per-page="paginations.contracts.perPage"
                                    striped hover small show-empty
                                    @filtered="onFiltered($event,'contracts')"
                                >
                                    <template v-slot:cell(row_data)="row">
                                        <!-- <b-btn :size="'sm'" variant="success" @click.stop="row.toggleDetails()"> -->
                                            <i @click.stop="row.toggleDetails(), billingForms(row)" :class="row.detailsShowing ? 'fa fa-minus fa-lg text-danger' : 'fa fa-plus fa-lg text-success'"></i>
                                        <!-- </b-btn> -->
                                    </template>
                                    <template v-slot:cell(status)="data">
                                        <span v-html="data.value"></span>
                                    </template>
                                    <template v-slot:row-details="row">
                                        <b-row class="font-weight-bold ml-2 mr-2">
                                            <b-col lg="4">
                                                <p>Contract No. : {{row.item.contract_no}}</p>
                                                <p>Tenant Code : {{row.item.tenant_code}}</p>
                                                <p>Tenant : {{row.item.trade_name}}</p>
                                                <p>Signatory : {{row.item.contract_signatory}}</p>
                                                <p>Billing Address : {{row.item.contract_billing_address}}</p>
                                                <p>Department : {{row.item.department_desc}}</p>
                                                <p>Nature of Business : {{row.item.nature_of_business_desc}}</p>
                                                <p>Approved Merchandise : {{row.item.contract_approved_merch}}</p>
                                            </b-col>
                                            <b-col lg="4">
                                                <p>Location : {{row.item.location_desc}}</p>
                                                <p>Contract Types : {{row.item.contract_type_desc}}</p>
                                                <p>Category : {{row.item.category_desc}}</p>
                                                <p>Terms : {{row.item.contract_terms}}</p>
                                                <p>Commencement Date : {{moment(row.item.commencement_date, 'MMMM DD, YYYY')}}</p>
                                                <p>Termination Date : {{moment(row.item.termination_date, 'MMMM DD, YYYY')}}</p>
                                                <p>Start Billing Date : {{moment(row.item.start_billing_date, 'MMMM DD, YYYY')}}</p>
                                                <p>App Floor Area : {{formatNumber(row.item.contract_floor_area)}}</p>
                                                <p>Basic Rental : {{formatNumber(row.item.contract_fixed_rent)}}</p>
                                            </b-col>
                                            <b-col lg="4">
                                                <!-- <p>Discounted Rental : {{formatNumber(row.item.contract_discounted_rent)}}</p> -->
                                                <p>Advance Rental : {{formatNumber(row.item.c_advance_rent)}}</p>
                                                <p>Security Deposit : {{formatNumber(row.item.c_security_deposit)}}</p>
                                                <p>Electric Meter Deposit : {{formatNumber(row.item.c_electric_meter_deposit)}}</p>
                                                <p>Water Meter Deposit : {{formatNumber(row.item.c_water_meter_deposit)}}</p>
                                                <p>Construction Deposit : {{formatNumber(row.item.c_construction_deposit)}}</p>
                                                <p>Escalation % : {{formatNumber(row.item.contract_escalation_percent)}}</p>
                                                <p>Escalation Notes : {{row.item.escalation_notes}}</p>
                                                <p>Remarks : {{row.item.contract_remarks}}</p>
                                                <p>is Renewal? : {{row.item.is_renewal == 1 ? 'Yes' : 'No'}}</p>
                                                <p>is Active? : {{row.item.is_active == 1 ? 'Yes' : 'No'}}</p>
                                            </b-col>
                                        </b-row>
                                        <b-row class="justify-content-md-center">
                                            <b-col lg=8>
                                                <b-tabs>
                                                    <b-tab title="Start Billing Form">
                                                        <b-button class="float-right mb-2" @click="setStartBilling(row)" variant="success"><i class="fa fa-plus-circle"></i> New Start Billing</b-button>
                                                        <b-table-simple>
                                                            <b-thead>
                                                                <b-tr>
                                                                    <b-th>Start Billing No</b-th>
                                                                    <b-th style="width: 30%">Date</b-th>
                                                                    <b-th></b-th>
                                                                </b-tr>
                                                            </b-thead>
                                                            <b-tbody>
                                                                <b-tr v-for="start in row.item.start_billing" :key="start.start_billing_id">
                                                                    <b-td class="align-middle">{{start.start_billing_no}}</b-td>
                                                                    <b-td class="align-middle">{{moment(start.date, 'MMMM DD, YYYY')}}</b-td>
                                                                    <b-td style="width: 75px">
                                                                        <b-btn title="Print" @click="printForm(true, start.start_billing_id)" size="sm" variant="success"><i class="fa fa-print"></i></b-btn>
                                                                        <b-btn @click="setCancelStartBilling(start.start_billing_id)" title="Cancel" size="sm" variant="danger"><i class="fa fa-trash"></i></b-btn></b-td>
                                                                </b-tr>
                                                            </b-tbody>
                                                        </b-table-simple>
                                                    </b-tab>
                                                    <b-tab title="Stop Billing Form">
                                                        <b-button @click="setStopBilling(row)" class="float-right mb-2" variant="success"><i class="fa fa-plus-circle"></i> New Stop Billing</b-button>
                                                        <b-table-simple>
                                                            <b-thead>
                                                                <b-tr>
                                                                    <b-th>Stop Billing No</b-th>
                                                                    <b-th style="width: 30%">Date</b-th>
                                                                    <b-th></b-th>
                                                                </b-tr>
                                                            </b-thead>
                                                            <b-tbody>
                                                                <b-tr v-for="stop in row.item.stop_billing" :key="stop.stop_billing_id">
                                                                    <b-td class="align-middle">{{stop.stop_billing_no}}</b-td>
                                                                    <b-td class="align-middle">{{moment(stop.date, 'MMMM DD, YYYY')}}</b-td>
                                                                    <b-td style="width: 75px">
                                                                        <b-btn title="Print" @click="printForm(false, stop.stop_billing_id)" size="sm" variant="success"><i class="fa fa-print"></i></b-btn>
                                                                        <b-btn @click="setCancelStopBilling(stop.stop_billing_id)" title="Cancel" size="sm" variant="danger"><i class="fa fa-trash"></i></b-btn></b-td>
                                                                </b-tr>
                                                            </b-tbody>
                                                        </b-table-simple>
                                                    </b-tab>
                                                </b-tabs>
                                            </b-col>
                                        </b-row>
                                    </template>
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('13-72')" :size="'sm'" title="Print Lease Proposal" variant="secondary" @click="printProposal(data.item.contract_id), tabIndex=0">
                                            <i class="fa fa-print"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-69')" :size="'sm'" title="Fees" variant="secondary" @click="showFees(data), tabIndex=0">
                                            <i class="fa fa-ticket"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-68')" :size="'sm'" title="Renew" variant="success" @click="tables.schedules.items=[], tabIndex=0, setRenewal(data), tabIndex=0">
                                            <i class="fa fa-refresh"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-48')" :disabled="data.item.status != 0" :size="'sm'" title="Edit" variant="primary" @click="setUpdate(data), tabIndex=0">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-70') && data.item.status != 3" :size="'sm'" :disabled="data.item.status == 0 || data.item.status == 2 || data.item.status == 4" title="Pre-terminate" variant="warning" @click="setTerminate(data, 3)">
                                            <i class="fa fa-times"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-70') && data.item.status == 3" :size="'sm'" :disabled="data.item.status == 0 || data.item.status == 2 || data.item.status == 4" title="Activate" variant="warning" @click="setTerminate(data, 1)">
                                            <i class="fa fa-check"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-71')" :size="'sm'" :disabled="data.item.status == 0 || data.item.status == 2 || data.item.status == 4" title="Terminate" variant="dark" @click="setTerminate(data, 4)">
                                            <i class="fa fa-times-circle"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('13-49')" :size="'sm'" title="Delete" variant="danger" @click="$refs.deleteentry.setDelete(data.item.contract_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.contracts.totalRows" :per-page="paginations.contracts.perPage" v-model="paginations.contracts.currentPage"
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
                                Contract Entry - {{entryMode}}
                            </span>
                        </h5>
                        <b-form @keydown="resetFieldStates('contract')" autocomplete="off">
                            <b-row>
                                <b-col sm="12">
                                    <b-tabs v-model="tabIndex">
                                        <b-tab title="Contract Info" >
                                            <b-row class="mb-2">
                                                <b-col sm="4">
                                                    <h5>Contract Info</h5>
                                                </b-col>
                                            </b-row>
                                            <b-row>
                                                <b-col lg="4">
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label>Contract No.</label>
                                                                <b-form-input
                                                                    v-model="forms.contract.fields.contract_no"
                                                                    type="text"
                                                                    readonly
                                                                    placeholder="Auto">
                                                                </b-form-input>
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <label>Tenant Code</label>
                                                                <b-form-input
                                                                    v-model="forms.contract.fields.tenant_code"
                                                                    type="text"
                                                                    readonly
                                                                    placeholder="Tenant Code">
                                                                </b-form-input>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Lessee/Tenant </label>
                                                        <select2
                                                            ref="tenant_id"
                                                            @input="getTenantInfo"
                                                            :allowClear="false"
                                                            :placeholder="'Select Tenants'"
                                                            v-model="forms.contract.fields.tenant_id"
                                                        >
                                                            <option v-for="tenant in options.tenants.items" :key="tenant.tenant_id" :value="tenant.tenant_id">{{tenant.trade_name}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Signatory </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.contract_signatory"
                                                            type="text"
                                                            readonly
                                                            placeholder="Signatory">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Billing Address </label>
                                                        <b-form-textarea
                                                            v-model="forms.contract.fields.contract_billing_address"
                                                            type="text"
                                                            readonly
                                                            placeholder="Billing Address">
                                                        </b-form-textarea>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Department </label>
                                                        <select2
                                                            ref="department_id"
                                                            :allowClear="false"
                                                            :placeholder="'Select Department'"
                                                            v-model="forms.contract.fields.department_id"
                                                            :reference="'department'"
                                                            @input="isOptionCreating"
                                                        >
                                                            <option value="-1">Create New Department</option>
                                                            <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_desc}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Nature of Business </label>
                                                        <select2
                                                            ref="nature_of_business_id"
                                                            :allowClear="false" 
                                                            :placeholder="'Select Nature of Business'"
                                                            v-model="forms.contract.fields.nature_of_business_id"
                                                            :reference="'natureofbusiness'"
                                                            @input="isOptionCreating"
                                                        >
                                                            <option value="-1">Create New Nature of Business</option>
                                                            <option v-for="nature in options.natureofbusinesses.items" :key="nature.nature_of_business_id" :value="nature.nature_of_business_id">{{nature.nature_of_business_desc}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Approved Merchandise </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.contract_approved_merch"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Approved Merchandise">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Location </label>
                                                        <select2
                                                            ref="location_id"
                                                            :allowClear="false"
                                                            :placeholder="'Select Location'"
                                                            v-model="forms.contract.fields.location_id"
                                                            :reference="'location'"
                                                            @input="isOptionCreating"
                                                        >
                                                            <option value="-1">Create New Location</option>
                                                            <option v-for="location in options.locations.items" :key="location.location_id" :value="location.location_id">{{location.location_desc}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Contract Types </label>
                                                        <select2
                                                            ref="contract_type_id"
                                                            :allowClear="false"
                                                            :placeholder="'Select Contract Type'"
                                                            v-model="forms.contract.fields.contract_type_id"
                                                            :reference="'contracttype'"
                                                            @input="isOptionCreating"
                                                        >
                                                            <option value="-1">Create New Contract Type</option>
                                                            <option v-for="contract_type in options.contracttypes.items" :key="contract_type.contract_type_id" :value="contract_type.contract_type_id">{{contract_type.contract_type_desc}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Category </label>
                                                        <select2
                                                            ref="category_id"
                                                            :allowClear="false"
                                                            :placeholder="'Select Category'"
                                                            v-model="forms.contract.fields.category_id"
                                                            :reference="'category'"
                                                            @input="isOptionCreating"
                                                        >
                                                            <option value="-1">Create New Category</option>
                                                            <option v-for="category in options.categories.items" :key="category.category_id" :value="category.category_id">{{category.category_desc}}</option>
                                                        </select2>
                                                    </b-form-group>
                                                </b-col>
                                                <b-col lg="4">
                                                    <b-row>
                                                        <b-col lg=6>
                                                            <b-form-group>
                                                                <label class="required">Schedule Type </label>
                                                                <select2
                                                                    @input="computeDates(true)"
                                                                    ref="schedule_type"
                                                                    :allowClear="false"
                                                                    :placeholder="'Schedule Type'"
                                                                    v-model="forms.contract.fields.schedule_type">
                                                                        <option value="0">Daily</option>
                                                                        <option value="1">Monthly</option>
                                                                        <option value="2">Yearly</option>
                                                                </select2>
                                                            </b-form-group>
                                                        </b-col>
                                                        <b-col lg=6>
                                                            <b-form-group>
                                                                <label class="required">Terms </label>
                                                                <vue-autonumeric
                                                                    ref="contract_terms"
                                                                    @input="computeDates(true)"
                                                                    v-model="forms.contract.fields.contract_terms"
                                                                    :class="'form-control text-right'" 
                                                                    :options="{minimumValue: 0, modifyValueOnWheel: false, decimalPlaces: 0, emptyInputBehavior: 1}">
                                                                </vue-autonumeric>
                                                            </b-form-group>
                                                        </b-col>
                                                    </b-row>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label class="required">Commencement Date</label>
                                                                <date-picker 
                                                                    ref="commencement_date"
                                                                    @input="computeDates(true)"
                                                                    v-model="forms.contract.fields.commencement_date" 
                                                                    lang="en" 
                                                                    input-class="form-control mx-input"
                                                                    format="MMMM DD, YYYY"
                                                                    :clearable="false">
                                                                </date-picker>
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <label class="required">Termination Date</label>
                                                                <date-picker 
                                                                    ref="termination_date"
                                                                    @input="computeDates(false)"
                                                                    v-model="forms.contract.fields.termination_date" 
                                                                    lang="en" 
                                                                    input-class="form-control mx-input"
                                                                    format="MMMM DD, YYYY"
                                                                    :clearable="false">
                                                                </date-picker>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">Start Billing Date </label>
                                                        <date-picker 
                                                            ref="start_billing_date"
                                                            v-model="forms.contract.fields.start_billing_date" 
                                                            lang="en" 
                                                            input-class="form-control mx-input"
                                                            format="MMMM DD, YYYY"
                                                            :clearable="false">
                                                        </date-picker>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label class="required">App Floor Area </label>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <vue-autonumeric 
                                                                    ref="contract_floor_area"
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.contract_floor_area" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                            <span class="align-self-center">Sq. Meter</span>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Electricity </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_electricity"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Electricity">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Water </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_water"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Water">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Marketing Participation Fee </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_marketing"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Marketing Participation Fee">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Contract Processing Fee (Notarization) </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_processing"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Contract Proposing Fee (Notarization)">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Aircondition </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_aircondition"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Aircondition">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Common Usage Expenses (CUSA) </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_cusa"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Common Usage Expenses (CUSA)">
                                                        </b-form-input>
                                                    </b-form-group>
                                                </b-col>
                                                <b-col lg="4">
                                                    <b-form-group>
                                                        <label>Repair & Maintenance </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_repair_maintenance"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Repair & Maintenance">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Expenses for Individual Utilities </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_expense_utilities"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Expenses for Individual Utilities">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Security Deposit </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.oec_security_deposit"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Security Deposit">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label class="required">Basic Rental </label>
                                                                <vue-autonumeric 
                                                                    ref="contract_fixed_rent"
                                                                    id="fixed_rent"
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.contract_fixed_rent" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <!-- <label>Discounted Rental </label>
                                                                <vue-autonumeric 
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.contract_discounted_rent" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric>  -->
                                                                <label>Advance Rental </label>
                                                                <vue-autonumeric 
                                                                    readonly
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.c_advance_rent" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label>Security Deposit </label>
                                                                <vue-autonumeric 
                                                                    readonly
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.c_security_deposit" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <label>Electric Meter Deposit </label>
                                                                <vue-autonumeric 
                                                                    readonly
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.c_electric_meter_deposit" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label>Water Meter Deposit </label>
                                                                <vue-autonumeric 
                                                                    readonly
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.c_water_meter_deposit" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <label>Construction Deposit </label>
                                                                <vue-autonumeric 
                                                                    readonly
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.c_construction_deposit" 
                                                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric> 
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg="6">
                                                                <label>Escalation Schedule </label>
                                                                <select2
                                                                    ref="escalation_type"
                                                                    :allowClear="false"
                                                                    :placeholder="'Escalation Schedule'"
                                                                    v-model="forms.contract.fields.escalation_type">
                                                                        <option value="0">None</option>
                                                                        <option value="1">Annual</option>
                                                                        <option value="2">Every Other Year</option>
                                                                        <option value="3">Every 3 Years</option>
                                                                        <option value="5">Every 5 Years</option>
                                                                </select2>
                                                            </b-col>
                                                            <b-col lg="6">
                                                                <label>Escalation % </label>
                                                                <vue-autonumeric 
                                                                    :class="'form-control text-right'" 
                                                                    v-model="forms.contract.fields.contract_escalation_percent" 
                                                                    :options="{minimumValue: 0, maximumValue: 100, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                                </vue-autonumeric>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Escalation Notes </label>
                                                        <b-form-input
                                                            v-model="forms.contract.fields.escalation_notes"
                                                            debounce="250"
                                                            type="text"
                                                            placeholder="Escalation Notes">
                                                        </b-form-input>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <label>Remarks </label>
                                                        <b-form-textarea
                                                            v-model="forms.contract.fields.contract_remarks"
                                                            debounce="250"
                                                            type="text"
                                                            :rows="2"
                                                            placeholder="Remarks">
                                                        </b-form-textarea>
                                                    </b-form-group>
                                                    <b-row>
                                                        <b-col lg=6>
                                                            <b-form-group>
                                                                <label>is Renewal?</label>
                                                                <b-form-radio-group 
                                                                    buttons
                                                                    :button-variant="forms.contract.fields.is_renewal == 1 ?'outline-success' : 'outline-danger'"
                                                                    v-model="forms.contract.fields.is_renewal"
                                                                    :options="[
                                                                        { text: 'Yes', value: '1' },
                                                                        { text: 'No', value: '0' }
                                                                    ]"
                                                                />
                                                            </b-form-group>
                                                        </b-col>
                                                        <b-col lg=6>
                                                            <b-form-group>
                                                                <label>is Active?</label>
                                                                <b-form-radio-group 
                                                                    buttons
                                                                    :button-variant="forms.contract.fields.is_active == 1 ?'outline-success' : 'outline-danger'"
                                                                    v-model="forms.contract.fields.is_active"
                                                                    :options="[
                                                                        { text: 'Yes', value: '1' },
                                                                        { text: 'No', value: '0' }
                                                                    ]"
                                                                />
                                                            </b-form-group>
                                                        </b-col>
                                                    </b-row>
                                                </b-col>
                                            </b-row>
                                        </b-tab>
                                        <b-tab title="Schedule" >
                                            <b-row class="mb-2">
                                                <b-col sm="4">
                                                    <h5>Schedule of Payments</h5>
                                                </b-col>
                                                <b-col  sm="4">
                                                    <span></span>
                                                </b-col>
                                                <b-col  sm="4">
                                                    <b-button class="float-right ml-2" variant="primary" v-show="this.tables.schedules.items.length == 0" @click="addSchedule()">
                                                            <i class="fa fa-plus-circle"></i> Add Schedule
                                                    </b-button>
                                                    <b-button class="float-right" variant="primary" v-show="this.tables.schedules.items.length == 0" @click="autoCompleteSchedule()">
                                                            <i class="fa fa-plus-circle"></i> Auto-Complete
                                                    </b-button>
                                                    <b-button class="float-right" variant="danger" v-show="this.tables.schedules.items.length != 0" @click="removeAllSchedule()">
                                                            <i class="fa fa-trash"></i> Remove All
                                                    </b-button>
                                                </b-col>
                                            </b-row>
                                            <b-table 
                                                responsive
                                                tab="1"
                                                ref="schedules"
                                                small bordered
                                                :fields="tables.schedules.fields"
                                                :items.sync="tables.schedules.items"
                                                show-empty>
                                                <!-- <template slot="discounted_rent" >
                                                    <vue-autonumeric 
                                                        :class="'form-control text-right'"
                                                        v-model="data.item.discounted_rent" 
                                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                    </vue-autonumeric>
                                                </template> -->
                                                <template v-slot:cell(fixed_rent)="data">
                                                    <vue-autonumeric 
                                                        :class="'form-control text-right'"
                                                        v-model="data.item.fixed_rent" 
                                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                    </vue-autonumeric>
                                                </template>
                                                <template v-slot:cell(escalation_percent)="data">
                                                    <vue-autonumeric 
                                                        :class="'form-control text-right'"
                                                        v-model="data.item.escalation_percent" 
                                                        :options="{minimumValue: 0, maximumValue: 100,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                                    </vue-autonumeric>
                                                </template>
                                                <template v-slot:cell(escalation_amount)="data">
                                                    <vue-autonumeric 
                                                        :class="'form-control text-right'"
                                                        v-model="data.item.escalation_amount" 
                                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
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
                                                        placeholder="Notes"
                                                        v-model="data.item.contract_schedule_notes">
                                                    </b-form-input>
                                                </template>
                                                <template v-slot:cell(action)="data" >
                                                    <b-btn :size="'sm'" variant="primary" @click="addSchedule()">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </b-btn>

                                                    <b-btn :size="'sm'" variant="danger" @click="removeSchedule()">
                                                        <i class="fa fa-times-circle"></i>
                                                    </b-btn>
                                                </template>
                                            </b-table>
                                        </b-tab>
                                        <b-tab title="Charges">
                                            <b-row class="mb-2">
                                                <b-col>
                                                    <h5>Charges</h5>
                                                </b-col>
                                            </b-row>
                                            <b-row>
                                                <b-col sm="12">
                                                    <b-tabs>
                                                        <b-tab title="Utility" >
                                                            <b-row class="mb-2">
                                                                <b-col sm="4">
                                                                    <h5>Utility</h5>
                                                                </b-col>
                                                                <b-col  sm="4">
                                                                    <span></span>
                                                                </b-col>
                                                                <b-col  sm="4">
                                                                    <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('utilities'), charge_type='utilities'">
                                                                        <i class="fa fa-plus-circle"></i> Add Charges
                                                                    </b-button>
                                                                </b-col>
                                                            </b-row>
                                                            <b-table 
                                                                responsive
                                                                small bordered
                                                                :fields="tables.utilities.fields"
                                                                :items.sync="tables.utilities.items"
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
                                                                <template v-slot:cell(sort_key)="data">
                                                                    <vue-autonumeric 
                                                                        :class="'form-control text-center'"
                                                                        v-model="data.item.sort_key" 
                                                                        :options="{modifyValueOnWheel: false, emptyInputBehavior: 0,
                                                                        decimalPlaces: 0}">
                                                                    </vue-autonumeric>
                                                                </template>
                                                                <template v-slot:cell(action)="data" >
                                                                    <b-btn :size="'sm'" variant="danger" @click="removeCharge('utilities', data.index)">
                                                                        <i class="fa fa-times-circle"></i>
                                                                    </b-btn>
                                                                </template>
                                                            </b-table>
                                                        </b-tab>
                                                        <b-tab title="Miscellaneous" >
                                                            <b-row class="mb-2">
                                                                <b-col sm="4">
                                                                    <h5>Miscellaneous</h5>
                                                                </b-col>
                                                            <b-col  sm="4">
                                                                    <span></span>
                                                                </b-col>
                                                                <b-col  sm="4">
                                                                    <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('miscellaneous'),charge_type='miscellaneous'">
                                                                        <i class="fa fa-plus-circle"></i> Add Charges
                                                                    </b-button>
                                                                </b-col>
                                                            </b-row>
                                                            <b-table 
                                                                responsive
                                                                small bordered
                                                                :fields="tables.miscellaneous.fields"
                                                                :items.sync="tables.miscellaneous.items"
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
                                                                <template v-slot:cell(sort_key)="data">
                                                                    <vue-autonumeric 
                                                                        :class="'form-control text-center'"
                                                                        v-model="data.item.sort_key" 
                                                                        :options="{modifyValueOnWheel: false, emptyInputBehavior: 0,
                                                                        decimalPlaces: 0}">
                                                                    </vue-autonumeric>
                                                                </template>
                                                                <template v-slot:cell(action)="data" >
                                                                    <b-btn :size="'sm'" variant="danger" @click="removeCharge('miscellaneous', data.index)">
                                                                        <i class="fa fa-times-circle"></i>
                                                                    </b-btn>
                                                                </template>
                                                            </b-table>
                                                        </b-tab>
                                                        <b-tab title="Other" >
                                                            <b-row class="mb-2">
                                                                <b-col sm="4">
                                                                    <h5>Other</h5>
                                                                </b-col>
                                                                <b-col sm="4">
                                                                    <span></span>
                                                                </b-col>
                                                                <b-col  sm="4">
                                                                    <b-button class="float-right" variant="primary" @click="showModalCharges = true, clearCharges('other'),charge_type='other'">
                                                                        <i class="fa fa-plus-circle"></i> Add Charges
                                                                    </b-button>
                                                                </b-col>
                                                            </b-row>
                                                            <b-table
                                                                responsive 
                                                                small bordered
                                                                :fields="tables.other.fields"
                                                                :items.sync="tables.other.items"
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
                                                                <template v-slot:cell(sort_key)="data">
                                                                    <vue-autonumeric 
                                                                        :class="'form-control text-center'"
                                                                        v-model="data.item.sort_key" 
                                                                        :options="{modifyValueOnWheel: false, emptyInputBehavior: 0,
                                                                        decimalPlaces: 0}">
                                                                    </vue-autonumeric>
                                                                </template>
                                                                <template v-slot:cell(action)="data" >
                                                                    <b-btn :size="'sm'" variant="danger" @click="removeCharge('other', data.index)">
                                                                        <i class="fa fa-times-circle"></i>
                                                                    </b-btn>
                                                                </template>
                                                            </b-table>
                                                        </b-tab>
                                                    </b-tabs>
                                                </b-col>
                                            </b-row>
                                        </b-tab>
                                    </b-tabs>
                                </b-col>
                            </b-row>
                        </b-form>
                        <template v-slot:footer>
                            <b-row class="pull-right mt-2">
                                <b-col sm="12">
                                    <b-button 
                                        :disabled="forms.contract.isSaving" 
                                        variant="success" 
                                        @click="onContractEntry">
                                        <icon v-if="forms.contract.isSaving" name="sync" spin></icon>
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
        <div>      
            <b-modal
                v-model="showModalFeesList"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                size="lg">

                <div slot="modal-title">
                    Contract Fees
                </div>
                <b-row>
                    <b-col sm="12">
                        <b-row class="mb-2">
                            <b-col sm="12">
                                <h6>Contract No. : {{contract_no}}</h6>
                                <h6>Trade Name : {{trade_name}}</h6>
                                <h6>Company Name : {{company_name}}</h6>
                            </b-col>
                        </b-row>
                        <b-table 
                            responsive
                            small bordered
                            :fields="tables.feetypes.fields"
                            :items.sync="tables.feetypes.items"
                            show-empty>
                            <template v-slot:cell(row_data)="row">
                                <!-- <b-btn :size="'sm'" variant="success" @click.stop="row.toggleDetails(), contractFeesHistory(row)"> -->
                                    
                                    <i @click.stop="row.toggleDetails(), contractFeesHistory(row)" :class="row.detailsShowing ? 'fa fa-minus fa-lg text-danger' : 'fa fa-plus fa-lg text-success'"></i>
                                <!-- </b-btn> -->
                            </template>
                            <template v-slot:row-details="row">
                                <b-row>
                                    <b-col lg="12">
                                        <h6>{{row.item.fee_type_desc}} History</h6>
                                        <b-tabs>
                                            <b-tab title="Approved" >
                                                <table class="w-100 mb-2 responsive">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:25%">Datetime</th>
                                                            <th>Notes</th>
                                                            <th class="text-right">Debit</th>
                                                            <th class="text-right">Credit</th>
                                                            <th class="text-right">Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-if="row.item.history.length == 0" >
                                                            <td colspan="6">No Records found.</td>
                                                        </tr>
                                                        <tr v-else-if="filterHistory(row.item.history, 1) == 0" >
                                                            <td colspan="6">No Records found.</td>
                                                        </tr>
                                                        <tr v-else v-for="history in filterHistory(row.item.history, 1)">
                                                            <td class="align-middle">{{ moment(history.created_datetime, 'MMMM DD, YYYY hh:mm:ss a') }}</td>
                                                            <td class="align-middle">{{ history.fee_notes }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.fee_debit) }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.fee_credit) }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.balance) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </b-tab>
                                            <b-tab title="Pending" >
                                                <table class="w-100 mb-2 responsive">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:25%">Datetime</th>
                                                            <th>Notes</th>
                                                            <th class="text-right">Debit</th>
                                                            <th class="text-right">Credit</th>
                                                            <th class="text-right">Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-if="row.item.history.length == 0" >
                                                            <td colspan="6">No Records found.</td>
                                                        </tr>
                                                        <tr v-else-if="filterHistory(row.item.history, 0) == 0" >
                                                            <td colspan="6">No Records found.</td>
                                                        </tr>
                                                        <tr v-else v-for="history in filterHistory(row.item.history, 0)">
                                                            <td class="align-middle">{{ moment(history.created_datetime, 'MMMM DD, YYYY hh:mm:ss a') }}</td>
                                                            <td class="align-middle">{{ history.fee_notes }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.fee_debit) }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.fee_credit) }}</td>
                                                            <td class="align-middle text-right">{{ formatNumber(history.pending_balance) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </b-tab>
                                        </b-tabs>
                                    </b-col>
                                </b-row>
                            </template>
                            <template v-slot:cell(action)="data" >
                                <b-btn @click="setFees(data.item)" :size="'sm'" variant="primary">
                                    <i class="fa fa-edit"></i>
                                </b-btn>
                            </template>
                        </b-table>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-button variant="danger" @click="showModalFeesList=false">Close</b-button>            
                </div>
            </b-modal>
            <b-modal 
                v-model="showModalFees"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                @shown="focusElement('fee_no')">
                
                <div slot="modal-title"> <!-- modal title -->
                    Fee Entry - {{forms.contract_other_fees.fields.fee_mode}}
                </div> <!-- modal title -->

                <b-col lg=12> <!-- modal body -->
                    <b-form autocomplete="off">
                        <b-form-group>
                            <label class="required">Fee No</label>
                            <b-form-input
                                v-model="forms.contract_other_fees.fields.fee_no"
                                debounce="250"
                                type="text"
                                ref="fee_no"
                                placeholder="Fee No">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required" for="amount">Amount</label>
                            <vue-autonumeric 
                                ref="amount"
                                v-model="forms.contract_other_fees.fields.amount"
                                :class="'form-control text-right'" 
                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                            </vue-autonumeric>
                        </b-form-group>
                        <b-form-group>
                            <label>Notes</label>
                            <b-form-textarea
                                v-model="forms.contract_other_fees.fields.fee_notes"
                                debounce="250"
                                type="text"
                                :rows="2"
                                placeholder="Notes">
                            </b-form-textarea>
                        </b-form-group>
                    </b-form>
                </b-col> <!-- modal body -->

                <div slot="modal-footer"><!-- modal footer buttons -->
                    <b-button :disabled="forms.contract_other_fees.isSaving" variant="success" @click="saveFees()">
                        <icon v-if="forms.contract_other_fees.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showModalFees=false">Close</b-button>
                </div> <!-- modal footer buttons -->

            </b-modal> <!--fees modal -->
            <b-modal 
                v-model="showModalTerminateConfirmation"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true">
                <div slot="modal-title">
                    {{t_head}}
                </div>
                <b-col lg=12>
                    {{t_msg}}
                </b-col>
                <div slot="modal-footer">
                    <b-button :disabled="forms.contract.isSaving" variant="success" @click="showModalTerminate=true, showModalTerminateConfirmation=false">
                        <icon v-if="forms.contract.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="danger" @click="showModalTerminateConfirmation=false">Close</b-button>            
                </div>
            </b-modal>
            <b-modal 
                v-model="showModalTerminate"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true">
                <div slot="modal-title">
                    {{t_head}}
                </div>
                <b-col lg=12>
                    <b-form-group>
                        <label>Remarks :</label>
                        <b-form-textarea debounce="250" v-model="forms.contract.fields[t_model]" rows="4"></b-form-textarea>
                    </b-form-group>
                </b-col>
                <div slot="modal-footer">
                    <b-button :disabled="forms.contract.isSaving" variant="success" @click="terminateContract(contract_status)">
                        <icon v-if="forms.contract.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="danger" @click="showModalTerminate=false">Close</b-button>            
                </div>
            </b-modal>
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
                v-model="showModalStartBilling"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                size="lg"
            >
                <div slot="modal-title">
                    Start Billing Form
                </div>
                <b-row>
                    <b-col lg=6>
                        <b-form-group>
                            <label>Date : </label>
                            <date-picker 
                                v-model="forms.start_billing.fields.date" 
                                lang="en"
                                ref="date"
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Trade Name : </label>
                            <b-form-input
                                v-model="forms.start_billing.fields.trade_name"
                                ref="trade_name"
                                type="text" 
                                placeholder="Trade Name"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Company Name : </label>
                            <b-form-input
                                ref="company_name"
                                v-model="forms.start_billing.fields.company_name"
                                type="text" 
                                placeholder="Company Name"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Start of Rent : </label>
                            <date-picker 
                                ref="start_billing_date"
                                v-model="forms.start_billing.fields.start_billing_date" 
                                lang="en" 
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-row>
                            <b-col lg=6>
                                <b-form-group>
                                    <label class="required">Floor Area : </label>
                                    <vue-autonumeric
                                        ref="contract_floor_area"
                                        v-model="forms.start_billing.fields.contract_floor_area"
                                        :class="'form-control text-right'" 
                                        :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                    </vue-autonumeric>
                                </b-form-group>
                            </b-col>
                            <b-col lg=6>
                                <b-form-group>
                                    <label class="required">Monthly Rental : </label>
                                    <vue-autonumeric
                                        ref="contract_fixed_rent"
                                        v-model="forms.start_billing.fields.contract_fixed_rent"
                                        :class="'form-control text-right'" 
                                        :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                    </vue-autonumeric>
                                </b-form-group>
                            </b-col>
                        </b-row>
                    </b-col>
                    <b-col lg=6>
                        <b-form-group>
                            <label class="required">CUSA : </label>
                            <b-form-input
                                ref="oec_cusa"
                                v-model="forms.start_billing.fields.oec_cusa"
                                type="text" 
                                placeholder="CUSA"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Aircondition : </label>
                            <b-form-input
                                ref="oec_aircondition"
                                v-model="forms.start_billing.fields.oec_aircondition"
                                type="text" 
                                placeholder="Aircondition"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Contract Processing Fee : </label>
                            <b-form-input
                                ref="oec_processing"
                                v-model="forms.start_billing.fields.oec_processing"
                                type="text" 
                                placeholder="Contract Processing Fee"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label>Note : </label>
                            <b-form-textarea
                                ref="contract_remarks"
                                v-model="forms.start_billing.fields.contract_remarks"
                                type="text"
                                row="3"
                                placeholder="Note"
                                debounce="250">
                            </b-form-textarea>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-button variant="primary" @click="saveStartBilling()">
                        <icon v-if="forms.start_billing.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showModalStartBilling=false">Close</b-button>            
                </div>
            </b-modal>
            <b-modal 
                v-model="showModalStopBilling"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                size="lg"
            >
                <div slot="modal-title">
                    Stop Billing Form
                </div>
                <b-row>
                    <b-col lg=6>
                        <b-form-group>
                            <label>Date : </label>
                            <date-picker
                                ref="date"
                                v-model="forms.stop_billing.fields.date" 
                                lang="en" 
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Trade Name : </label>
                            <b-form-input
                                ref="trade_name"
                                v-model="forms.stop_billing.fields.trade_name"
                                type="text" 
                                placeholder="Trade Name"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Company Name : </label>
                            <b-form-input
                                ref="company_name"
                                v-model="forms.stop_billing.fields.company_name"
                                type="text" 
                                placeholder="Company Name"
                                debounce="250">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Stop Billing Date : </label>
                            <date-picker 
                                ref="termination_date"
                                v-model="forms.stop_billing.fields.termination_date" 
                                lang="en" 
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                    </b-col>
                    <b-col lg=6>
                        <b-row>
                            <b-col lg=6>
                                <b-form-group>
                                    <label class="required">Security Deposit : </label>
                                    <vue-autonumeric
                                        ref="security_deposit"
                                        v-model="forms.stop_billing.fields.security_deposit"
                                        :class="'form-control text-right'" 
                                        :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                    </vue-autonumeric>
                                </b-form-group>
                            </b-col>
                            <b-col lg=6>
                                <b-form-group>
                                    <label class="required">Power Meter Deposit : </label>
                                    <vue-autonumeric
                                        ref="power_meter_deposit"
                                        v-model="forms.stop_billing.fields.power_meter_deposit"
                                        :class="'form-control text-right'" 
                                        :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                    </vue-autonumeric>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-form-group>
                            <label >Note : </label>
                            <b-form-textarea
                                ref="note"
                                v-model="forms.stop_billing.fields.note"
                                type="text"
                                row="3"
                                placeholder="Note"
                                debounce="250">
                            </b-form-textarea>
                        </b-form-group>
                        <b-form-group>
                            <label class="required">Accounting Department</label>
                            <b-form-radio-group
                                ref="accounting_department"
                                v-model="forms.stop_billing.fields.accounting_department"
                                >
                                <b-form-radio value="1">To refund SD and PMD</b-form-radio><br>
                                <b-form-radio value="2">To forfeit SD and PMD</b-form-radio><br>
                                <b-form-radio value="3">To apply SD and PMD to all outstanding balance and refund excess (if there is any)</b-form-radio><br>
                                <b-form-radio value="4">To consume all SD and PMD</b-form-radio><br>
                                <b-form-radio value="5">Others <b-form-input v-model="forms.stop_billing.fields.accounting_department_others" placeholder="Others" :readonly="forms.stop_billing.fields.accounting_department!=5"></b-form-input></b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-button variant="primary" @click="saveStopBilling()">
                        <icon v-if="forms.stop_billing.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showModalStopBilling=false">Close</b-button>            
                </div>
            </b-modal>
            <b-modal 
                v-model="showModalCancelStartConfirmation"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true">
                <div slot="modal-title">
                    Cancel Start Billing Form
                </div>
                <b-col lg=12>
                    Are you sure you want to cancel this start billing form?
                </b-col>
                <div slot="modal-footer">
                    <b-button :disabled="forms.start_billing.isSaving" variant="success" @click="cancelStartBilling()">
                        <icon v-if="forms.start_billing.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="danger" @click="showModalCancelStartConfirmation=false">Close</b-button>            
                </div>
            </b-modal>
            <b-modal 
                v-model="showModalCancelStopConfirmation"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true">
                <div slot="modal-title">
                    Cancel Stop Billing Form
                </div>
                <b-col lg=12>
                    Are you sure you want to cancel this stop billing form?
                </b-col>
                <div slot="modal-footer">
                    <b-button :disabled="forms.stop_billing.isSaving" variant="success" @click="cancelStopBilling()">
                        <icon v-if="forms.stop_billing.isSaving" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="danger" @click="showModalCancelStopConfirmation=false">Close</b-button>            
                </div>
            </b-modal>
        </div>
        <departmententry type="contract" ref="departmententry"></departmententry>
        <natureofbusinessentry type="contract" ref="natureofbusinessentry"></natureofbusinessentry>
        <locationentry type="contract" ref="locationentry"></locationentry>
        <contracttypeentry type="contract" ref="contracttypeentry"></contracttypeentry>
        <categoryentry type="contract" ref="categoryentry"></categoryentry>
        <deleteentry entity="contract" table="contracts" primary_key="contract_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import departmententry from '../modals/DepartmentEntry'
import natureofbusinessentry from '../modals/NatureOfBusinessEntry'
import locationentry from '../modals/LocationEntry'
import contracttypeentry from '../modals/ContractTypeEntry'
import categoryentry from '../modals/CategoryEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'contracts',
    components: {
        departmententry,
        natureofbusinessentry,
        locationentry,
        contracttypeentry,
        categoryentry,
        deleteentry
    },
    data () {
        return {
            entryMode: 'Add',
            showEntry: false, //if true show entry
            showModalFees: false,
            showModalFeesList: false,
            showModalTerminateConfirmation: false,
            showModalTerminate: false,
            showModalCharges: false,
            showModalStartBilling: false,
            showModalStopBilling: false,
            start_billing_id: false,
            stop_billing_id: false,
            showModalCancelStartConfirmation: false,
            showModalCancelStopConfirmation: false,
            t_head: null,
            t_msg: null,
            t_model: null,
            contract_status: false,
            contract_no: null,
            trade_name: null,
            company_name: null,
            options: {
                tenants: {
                    items: []
                },
                departments: {
                    items: []
                },
                natureofbusinesses: {
                    items: []
                },
                locations: {
                    items: []
                },
                contracttypes: {
                    items: []
                },
                categories: {
                    items: []
                },
                months: {
                    items: []
                }
            },
            forms: {
                contract: {
                    isSaving: false,
                    fields: {
                        contract_id: null,
                        contract_no: null,
                        tenant_code: null,
                        tenant_id: null,
                        contract_signatory: null,
                        contract_billing_address: null,
                        department_id: null,
                        nature_of_business_id: null,
                        contract_approved_merch: null,
                        location_id: null,
                        contract_type_id: null,
                        category_id: null,
                        schedule_type: 1,
                        contract_terms: null,
                        commencement_date: new Date(),
                        termination_date: new Date(),
                        start_billing_date: new Date(),
                        contract_floor_area: 0,
                        oec_electricity: null,
                        oec_water: null,
                        oec_marketing: null,
                        oec_processing: null,
                        oec_aircondition: null,
                        oec_cusa: null,
                        oec_repair_maintenance: null,
                        oec_expense_utilities: null,
                        oec_security_deposit: null,
                        contract_fixed_rent: 0,
                        contract_discounted_rent: 0,
                        contract_advance_rent: 0,
                        c_advance_rent: 0,
                        escalation_type: 0,
                        contract_escalation_percent: 0,
                        security_deposit: 0,
                        c_security_deposit: 0,
                        power_meter_deposit: 0,
                        c_electric_meter_deposit: 0,
                        water_meter_deposit: 0,
                        c_water_meter_deposit: 0,
                        construction_deposit: 0,
                        c_construction_deposit: 0,
                        escalation_notes: null,
                        contract_remarks: null,
                        pre_terminated_remarks: '',
                        terminated_remarks: '',
                        activated_remarks: null,
                        schedules: [],
                        utilities: [],
                        miscellaneous: [],
                        other: [],
                        is_renewal: 0,
                        is_active: 1,
                        renewed: 0
                    }
                },
                contract_other_fees : {
                    isSaving: false,
                    fields: {
                        fee_no: null,
                        tenant_id: null,
                        fee_mode: null,
                        fee_type_id: null,
                        fee_notes: null,
                        amount: null
                    }
                },
                start_billing: {
                    isSaving: false,
                    fields: {
                        date: new Date(),
                        contract_id: null,
                        trade_name: null,
                        company_name: null,
                        start_billing_date: null,
                        contract_floor_area: null,
                        contract_fixed_rent: null,
                        oec_cusa: null,
                        oec_aircondition: null,
                        oec_processing: null,
                        oec_contract_remarks: null
                    }
                },
                stop_billing: {
                    isSaving: false,
                    fields: {
                        date: new Date(),
                        contract_id: null,
                        trade_name: null,
                        company_name: null,
                        termination_date: null,
                        security_deposit: null,
                        power_meter_deposit: null,
                        note: null,
                        accounting_department: null,
                        accounting_department_other: null
                    }
                }
            },
            tables: {
                contracts: {
                    fields:[
                        {
                            key: 'contract_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle text-center',
                            thStyle: {width: '30px'}
                        },
                        {
                            key:'contract_no',
                            label: 'Contract No',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key:'trade_name',
                            label: 'Trade Name',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'department_desc',
                            label:'Department',
                            thStyle: {width: '9%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'location_desc',
                            label:'Location',
                            thStyle: {width: '9%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'contract_type_desc',
                            label:'Contract Type',
                            thStyle: {width: '9%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'category_desc',
                            label:'Category',
                            thStyle: {width: '9%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'status',
                            label:'Status',
                            thStyle: {width: '90px'},
                            tdClass: 'align-middle',
                            formatter: (value) => {
                                if(value == 0){
                                    value = '<span style="color: orange"><i class="fa fa-history"></i> Pending</span>'
                                }
                                else if(value == 1){
                                    value = '<span class="text-success"><i class="fa fa-thumbs-up""></i> Approved</span>'
                                }
                                else if(value == 2){
                                    value = '<span class="text-info"><i class="fa fa-thumbs-down""></i> Disapproved</span>'
                                }
                                else if(value == 3){
                                    value = '<span class="text-danger"><i class="fa fa-times-circle""></i> Pre-terminated</span>'
                                }
                                else {
                                    value = '<span class="text-secondary"><i class="fa fa-times""></i> Pre-terminated</span>'
                                }
                                return value
                            }
                        },
                        {
                            key:'action',
                            label:'Action',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '235px'},
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
                            tdClass: 'align-middle',
                            thStyle: {width: '25%'}
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
                            thStyle: {width: '25px'}
                        },
                        {
                            key: 'month_name',
                            label: 'App Month',
                            thStyle: {width: '8%'},
                            tdClass: 'align-middle'
                        },
                        {
                            key: 'app_year',
                            label: 'Year',
                            tdClass: 'align-middle',
                            thStyle: {width: '5%'},
                        },
                        // {
                        //     key: 'discounted_rent',
                        //     label: 'Disc Rent',
                        //     thClass: 'text-right',
                        //     tdClass: 'text-right align-middle',
                        //     thStyle: {width: '11%'}
                        // },
                        {
                            key: 'fixed_rent',
                            label: 'Basic Rent',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '11%'}
                        },
                        {
                            key: 'escalation_percent',
                            label: 'Esc %',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '7%'}
                        },
                        {
                            key: 'escalation_amount',
                            label: 'Esc Amount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '10%'},
                            formatter: (value, key, item) => {
                                item.escalation_amount = item.fixed_rent * (item.escalation_percent / 100)
                            }
                        },
                        // {
                        //     key: 'discounted_amount_due',
                        //     label: 'Disc Amount Due',
                        //     thClass: 'text-right',
                        //     tdClass: 'text-right align-middle',
                        //     thStyle: {width: '11%'},
                        //     formatter: (value, key, item) => {
                        //         item.discounted_amount_due = Number(item.discounted_rent) + (Number(item.discounted_rent) * (Number(item.escalation_percent)/100)) + Number(item.escalation_amount)
                        //         return this.formatNumber(item.discounted_amount_due)
                                
                        //     }
                        // },
                        {
                            key: 'amount_due',
                            label: 'Amount Due',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '11%'},
                            formatter: (value, key, item) => {
                                item.amount_due = Number(item.fixed_rent) + Number(item.escalation_amount)
                                return this.formatNumber(item.amount_due)
                                
                            }
                        },
                        {
                            key: 'is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '7%'},
                        },
                        {
                            key: 'contract_schedule_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            tdClass: 'align-middle text-center',
                            thStyle: {width: '75px'},
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
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '25%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '12%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Default Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '100px'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'sort_key',
                            label: 'Sort',
                            thClass: 'text-center',
                            thStyle: {width: '50px'}
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '75px'},
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
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '25%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '12%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Default Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '100px'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'sort_key',
                            label: 'Sort',
                            thClass: 'text-center',
                            thStyle: {width: '50px'}
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '75px'},
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
                            key: 'charge_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            thStyle: {width: '25%'}
                        },
                        {
                            key: 'contract_rate',
                            label: 'Rate',
                            thClass: 'text-right',
                            tdClass: 'align-middle text-right',
                            thStyle: {width: '12%'}
                        },
                        {
                            key: 'contract_default_reading',
                            label: 'Default Reading',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '15%'}
                        },
                        {
                            key: 'contract_is_vatted',
                            label: 'Is Vatted?',
                            thClass: 'text-center',
                            tdClass: 'text-center align-middle',
                            thStyle: {width: '100px'}
                        },
                        {
                            key: 'contract_notes',
                            label: 'Notes'
                        },
                        {
                            key: 'sort_key',
                            label: 'Sort',
                            thClass: 'text-center',
                            thStyle: {width: '50px'}
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '75px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
                feetypes: {
                    fields: [
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle',
                            thStyle: {width: '2%'}
                        },
                        {
                            key: 'fee_type_desc',
                            label: 'Fee Type Desc',
                            tdClass: 'align-middle'
                        },
                        {
                            key: 'amount',
                            label: 'Amount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '75px'},
                            tdClass: 'text-center align-middle'
                        }
                    ],
                    items: []
                },
            },
            filters: {
                contracts: {
                    criteria: null
                },
                charges: {
                    criteria: null
                }
            },
            paginations: {
                contracts: {
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
            app_year: null,
            charge_type: null,
            tabIndex: 0,
            contract_id: null,
            is_check_all: 0,
            row: []
        }
    },
    methods:{
        onContractEntry () {
            this.forms.contract.fields.schedules = this.tables.schedules.items
            this.forms.contract.fields.utilities = this.tables.utilities.items
            this.forms.contract.fields.miscellaneous = this.tables.miscellaneous.items
            this.forms.contract.fields.other = this.tables.other.items

            if(this.entryMode == 'Add'){
                this.createEntity('contract', false, 'contracts', true)
            }
            else if(this.entryMode == 'Renewal'){
                this.$http.post('api/contract_renewal/'+ this.row.contract_id, this.forms.contract.fields,{
                    headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                })
                .then((response) => {  
                    this.forms.contract.isSaving = false
                    this.clearFields('contract')
                    this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: 'The record has been successfully created.'
                    })

                    this.tables.contracts.items.unshift(response.data.data)
                    this.paginations.contracts.totalRows++

                    this.showEntry = false
                }).catch(error => {
                    this.forms.contract.isSaving = false
                    if (!error.response) return
                    const errors = error.response.data.errors
                    var a = 0
                    for (var key in errors) {
                        // this.forms[entity].states[key] = false
                        // this.forms[entity].errors[key] =  errors[key]
                        if(a == 0){
                            this.focusElement(key, true)
                            this.$notify({
                                type: 'error',
                                group: 'notification',
                                title: 'Error!',
                                text: errors[key][0]
                            })
                        }
                        
                        a++
                    }
                })
            }
            else{
                this.updateEntity('contract', 'contract_id', false, this.row, true)
            }
        },
        setRenewal(data){
            this.row = data.item
            this.fillEntityForm('contract', data.item.contract_id)
            this.$http.get('/api/contracts/sc/'+ data.item.contract_id,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                const res = response.data
                this.tables.utilities.items = res.util_charges
                this.tables.miscellaneous.items = res.misc_charges
                this.tables.other.items = res.othr_charges
                this.showEntry=true
                this.entryMode='Renewal'
                this.counter = this.tables.schedules.items.length
                this.forms.contract.fields.is_renewal = 1
                this.forms.contract.fields.renewed = 1
                this.forms.contract.fields.contract_no = null
                this.forms.contract.fields.commencement_date = new Date(this.forms.contract.fields.termination_date)
                this.forms.contract.fields.start_billing_date = new Date(this.forms.contract.fields.termination_date)
                this.computeDates(true)
            }).catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },

        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('contract', data.item.contract_id)
            this.$http.get('/api/contracts/sc/'+ data.item.contract_id,{
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
                this.showEntry=true
                this.entryMode='Edit'
                this.counter = this.tables.schedules.items.length
                var prev_schedule = this.tables.schedules.items.find(s => s.count == this.counter)
                if(prev_schedule.month_id == 12){
                    this.app_year = prev_schedule.app_year + 1
                }
                else{
                    this.app_year = prev_schedule.app_year
                }
            }).catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
        autoCompleteSchedule(){
            try {
                var stype = 1
                if(this.forms.contract.fields.schedule_type == 2){
                    stype = 12
                }
                for(var x = 0; this.forms.contract.fields.contract_terms * stype >= x; x++){
                    var fixed_rent = 0
                    var discounted_rent = 0
                    var start = new Date(this.forms.contract.fields.start_billing_date)
                    if(this.counter == 0){
                        this.app_year = start.getFullYear()
                        fixed_rent = this.forms.contract.fields.contract_fixed_rent
                        discounted_rent = this.forms.contract.fields.contract_discounted_rent
                    }
                    else{
                        var prev_schedule = this.tables.schedules.items.find(s => s.count == this.counter)
                        fixed_rent = prev_schedule.amount_due
                        discounted_rent = prev_schedule.discounted_amount_due
                    }
                    var month;
                    var month_value = start.getMonth() + 1
                    var add_year = false
                    var escalation_percent = 0

                    var month_id = (month_value + this.counter) % 12
                    var discounted_amount_due = discounted_rent
                    var amount_due = fixed_rent

                    if(this.forms.contract.fields.schedule_type == 1 || this.forms.contract.fields.schedule_type == 2)
                    {
                        if(month_id == 0){
                            month = this.options.months.items.find(d => d.month_id === 12)
                            add_year = true
                        }
                        else{
                            month = this.options.months.items.find(d => d.month_id === month_id)
                        }

                        this.counter++;
                        if(this.forms.contract.fields.escalation_type != 0){
                            if(this.counter % (12 * this.forms.contract.fields.escalation_type) == 1){
                                if(this.counter != 1){
                                    escalation_percent = this.forms.contract.fields.contract_escalation_percent
                                }
                            }
                        }
                    }
                    else if(this.forms.contract.fields.schedule_type == 0){
                        month_value = start.getMonth() + 1
                        this.app_year = start.getFullYear()
                        month_id = month_value % 12
                        this.counter++;
                        if(month_id == 0){
                            month = this.options.months.items.find(d => d.month_id === 12)
                        }
                        else{
                            month = this.options.months.items.find(d => d.month_id === month_id)
                        }
                    }

                    this.tables.schedules.items.push({
                        count: this.counter,
                        month_name:month.month_name,
                        month_id:month.month_id,
                        app_year:this.app_year,
                        discounted_rent:discounted_rent,
                        fixed_rent:fixed_rent,
                        escalation_percent:escalation_percent,
                        escalation_amount: fixed_rent * (escalation_percent/100),
                        discounted_amount_due:discounted_amount_due,
                        amount_due:fixed_rent +  fixed_rent * (escalation_percent/100),
                        is_vatted:1,
                        contract_schedule_notes:''
                    })

                    if(add_year){
                        this.app_year++
                    }   
                }
            } catch(e)
            {
                console.log(e)
            }
        },
        addSchedule(){
            try {
                var stype = 1
                if(this.forms.contract.fields.schedule_type == 2){
                    stype = 12
                }
                if(this.forms.contract.fields.contract_terms * stype >= this.counter){
                    var fixed_rent = 0
                    var discounted_rent = 0
                    var start = new Date(this.forms.contract.fields.start_billing_date)

                    if(this.counter == 0){
                        this.app_year = start.getFullYear()
                        fixed_rent = this.forms.contract.fields.contract_fixed_rent
                        discounted_rent = this.forms.contract.fields.contract_discounted_rent
                    }
                    else{
                        var prev_schedule = this.tables.schedules.items.find(s => s.count == this.counter)
                        fixed_rent = prev_schedule.amount_due
                        discounted_rent = prev_schedule.discounted_amount_due
                    }

                    var month;
                    var month_value = start.getMonth() + 1
                    var add_year = false
                    var escalation_percent = 0

                    var month_id = (month_value + this.counter) % 12
                    var discounted_amount_due = discounted_rent
                    var amount_due = fixed_rent

                    if(this.forms.contract.fields.schedule_type == 1 || this.forms.contract.fields.schedule_type == 2)
                    {
                        if(month_id == 0){
                            month = this.options.months.items.find(d => d.month_id === 12)
                            add_year = true
                        }
                        else{
                            month = this.options.months.items.find(d => d.month_id === month_id)
                        }

                        this.counter++;
                        if(this.forms.contract.fields.escalation_type != 0){
                            if(this.counter % (12 * this.forms.contract.fields.escalation_type) == 1){
                                if(this.counter != 1){
                                    escalation_percent = this.forms.contract.fields.contract_escalation_percent
                                }
                            }
                        }
                    }
                    else if(this.forms.contract.fields.schedule_type == 0){
                        month_value = start.getMonth() + 1
                        this.app_year = start.getFullYear()
                        month_id = month_value % 12
                        this.counter++;
                        if(month_id == 0){
                            month = this.options.months.items.find(d => d.month_id === 12)
                        }
                        else{
                            month = this.options.months.items.find(d => d.month_id === month_id)
                        }
                    }

                    this.tables.schedules.items.push({
                        count: this.counter,
                        month_name:month.month_name,
                        month_id:month.month_id,
                        app_year:this.app_year,
                        discounted_rent:discounted_rent,
                        fixed_rent:fixed_rent,
                        escalation_percent:escalation_percent,
                        escalation_amount: fixed_rent * (escalation_percent/100),
                        discounted_amount_due:discounted_amount_due,
                        amount_due:amount_due,
                        is_vatted:1,
                        contract_schedule_notes:''
                    })

                    if(add_year){
                        this.app_year++
                    }   
                }
                else{
                    this.$notify({
                        type: 'error',
                        group: 'notification',
                        title: 'Error!',
                        text: "Can't add more rows beyond termination date."
                    })
                }
            } catch(e)
            {
                console.log(e)
            }
        },
        removeAllSchedule(){
            this.tables.schedules.items = []
            this.counter = 0
            this.app_year = new Date(this.forms.contract.fields.start_billing_date).getFullYear()
        },
        removeSchedule(){
            var start = new Date(this.forms.contract.fields.start_billing_date)
            var month_value = start.getMonth() + 1

            this.tables.schedules.items.splice(this.tables.schedules.items.length - 1, 1)
            this.counter--
            var month_id = (month_value + this.counter) % 12

            if(month_id == 0){
                this.app_year--
            }
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
        getTenantInfo: function (value, data) {
            if(data.length > 0){
                var tenant = this.options.tenants.items[data[0].element.index]
                this.forms.contract.fields.tenant_code = tenant.tenant_code;
                this.forms.contract.fields.contract_signatory = tenant.contact_person;
                this.forms.contract.fields.contract_billing_address = tenant.billing_address;
            }
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
        isOptionCreating: function(value, data, reference){
            if(value == -1){
                if(reference == 'department'){
                    this.$refs.departmententry.showModalEntry = true
                    this.$refs.departmententry.clearFields('department')
                    this.forms.contract.fields.department_id = 'null'
                }
                else if(reference == 'natureofbusiness'){
                    this.$refs.natureofbusinessentry.showModalEntry = true
                    this.$refs.natureofbusinessentry.clearFields('natureofbusiness')
                    this.forms.contract.fields.nature_of_business_id = 'null'
                }
                else if(reference == 'contracttype'){
                    this.$refs.contracttypeentry.showModalEntry = true
                    this.$refs.contracttypeentry.clearFields('contracttype')
                    this.forms.contract.fields.contract_type_id = "null"
                }
                else if(reference == 'category'){
                    this.$refs.categoryentry.showModalEntry = true
                    this.$refs.categoryentry.clearFields('category')
                    this.forms.contract.fields.category_id = "null"
                }
                else if(reference == 'location'){
                    this.$refs.locationentry.showModalEntry = true
                    this.$refs.locationentry.clearFields('location')
                    this.forms.contract.fields.location_id = "null"
                }
            }
        },
        computeDates(is_commencement){
            if(this.forms.contract.fields.schedule_type == 1){
                if(is_commencement){
                    this.forms.contract.fields.termination_date = moment(this.forms.contract.fields.commencement_date).add(this.forms.contract.fields.contract_terms, 'months').subtract(1,'days').format('MMMM DD, YYYY')
                }
                else{
                    this.forms.contract.fields.commencement_date = moment(this.forms.contract.fields.termination_date).subtract(this.forms.contract.fields.contract_terms, 'months').add(1,'days').format('MMMM DD, YYYY')
                }
            }
            else if(this.forms.contract.fields.schedule_type == 2){
                if(is_commencement){
                    this.forms.contract.fields.termination_date = moment(this.forms.contract.fields.commencement_date).add(this.forms.contract.fields.contract_terms, 'years').subtract(1,'days').format('MMMM DD, YYYY')
                }
                else{
                    this.forms.contract.fields.commencement_date = moment(this.forms.contract.fields.termination_date).subtract(this.forms.contract.fields.contract_terms, 'years').add(1,'days').format('MMMM DD, YYYY')
                }
            }
            else if(this.forms.contract.fields.schedule_type == 0){
                if(is_commencement){
                    this.forms.contract.fields.termination_date = moment(this.forms.contract.fields.commencement_date).add(this.forms.contract.fields.contract_terms - 1,'days').format('MMMM DD, YYYY')
                }
                else{
                    this.forms.contract.fields.commencement_date = moment(this.forms.contract.fields.termination_date).subtract(this.forms.contract.fields.contract_terms - 1, 'days').format('MMMM DD, YYYY')
                }
            }
        },

        setFees(item){
            this.forms.contract_other_fees.fields.fee_mode = item.fee_type_desc
            this.forms.contract_other_fees.fields.fee_type_id = item.fee_type_id
            this.forms.contract_other_fees.fields.amount = 0
            this.forms.contract_other_fees.fields.fee_no = null
            this.showModalFees = true
        },

        showFees(data){
            this.tables.feetypes.items = []
            this.showModalFeesList = true
            this.contract_no = data.item.contract_no
            this.trade_name = data.item.trade_name
            this.company_name = data.item.company_name
            this.forms.contract_other_fees.fields.tenant_id = data.item.tenant_id
            this.$http.get('/api/feetypes/'+ data.item.tenant_id,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                const res = response.data.data
                this.tables.feetypes.items = res
            }).catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },

        saveFees(){
            this.$http.post('/api/contract_other_fees', this.forms.contract_other_fees.fields,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                var tblft = this.tables.feetypes.items.filter(ft => 
                    ft.fee_type_id == this.forms.contract_other_fees.fields.fee_type_id
                )

                var tblContract = this.tables.contracts.items.filter(c => 
                    c.tenant_id == response.data.data.tenant_id
                )

                var type = ""

                if(this.forms.contract_other_fees.fields.fee_type_id == 1){
                    type = "c_advance_rent"
                }
                else if(this.forms.contract_other_fees.fields.fee_type_id == 2){
                    type = "c_security_deposit"
                }
                else if(this.forms.contract_other_fees.fields.fee_type_id == 3){
                    type = "c_electric_meter_deposit"
                }
                else if(this.forms.contract_other_fees.fields.fee_type_id == 4){
                    type = "c_water_meter_deposit"
                }
                else {
                    type = "c_construction_deposit"
                }
                
                // tblContract[0][type] = Number(tblContract[0][type]) + Number(response.data.data.fee_credit)
                // tblft[0].amount = Number(tblft[0].amount) + Number(response.data.data.fee_credit)
                this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: "Fee successfully saved."
                })
                this.showModalFees = false
            }).catch(error => {
                this.showModalFees = false
                if (!error.response) return
                console.log(error)
            })
        },
        contractFeesHistory(row){
            if(row.detailsShowing == true){
                return
            }
            this.$http.get('/api/contractfeeshistory/' + row.item.tenant_id + '/' + row.item.fee_type_id, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                  }
            })
            .then((response) => {
                row.item.history = response.data.data
            })
        },
        filterHistory(history, is_journal_posted){
            return history.filter(h => h.is_journal_posted == is_journal_posted)
        },
        setTerminate(data, status){
            if(status == 1){
                this.t_head = "Activating Contract"
                this.t_msg = "Billing can be created after activating. \n Do you wish to continue"
                this.t_model = "activated_remarks"
            }
            else if(status == 4){
                this.t_head = "Terminating Contract"
                this.t_msg = "No more billing will be created with this contract if you proceed. \n Do you wish to continue"
                this.t_model = "terminated_remarks"
            }
            else{
                this.t_head = "Pre-terminating Contract"
                this.t_msg = "No more billing will be created with this contract if you proceed. But you can still activate it. \n Do you wish to continue"
                this.t_model = "pre_terminated_remarks"
            }
            this.contract_status = status
            this.contract_id = data.item.contract_id
            this.showModalTerminateConfirmation = true
        },
        terminateContract(contract_status){
            this.forms.contract.isSaving = true
            if(contract_status == 1){
                this.$http.put('api/contract/activate/' + this.contract_id, this.forms.contract.fields, {
                    headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then((response) => {
                        this.forms.contract.isSaving = false
                        this.$notify({
                            type: 'success',
                            group: 'notification',
                            title: 'Success!',
                            text: 'The contract has been successfully activated.'
                        })
                        var row = this.tables.contracts.items.filter(c => c.contract_id == this.contract_id)[0]
                        for (var key in response.data.data) {
                            row[key] = response.data.data[key]
                        }
                        this.showModalTerminate = false
                    }).catch(error => {
                        this.forms[entity].isSaving = false
                        console.log(error)
                    })
            }
            else if(contract_status == 4){
                this.$http.put('api/contract/terminate/' + this.contract_id, this.forms.contract.fields, {
                    headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then((response) => {
                        this.forms.contract.isSaving = false
                        this.$notify({
                            type: 'success',
                            group: 'notification',
                            title: 'Success!',
                            text: 'The contract has been successfully terminated.'
                        })
                        var row = this.tables.contracts.items.filter(c => c.contract_id == this.contract_id)[0]
                        for (var key in response.data.data) {
                            row[key] = response.data.data[key]
                        }
                        this.showModalTerminate = false
                    }).catch(error => {
                        this.forms[entity].isSaving = false
                        console.log(error)
                    })
            }
            else{
                this.$http.put('api/contract/preterminate/' + this.contract_id, this.forms.contract.fields, {
                    headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                    })
                    .then((response) => {
                        this.forms.contract.isSaving = false
                        this.$notify({
                            type: 'success',
                            group: 'notification',
                            title: 'Success!',
                            text: 'The contract has been successfully pre-terminated.'
                        })
                        var row = this.tables.contracts.items.filter(c => c.contract_id == this.contract_id)[0]
                        for (var key in response.data.data) {
                            row[key] = response.data.data[key]
                        }
                        this.showModalTerminate = false
                    }).catch(error => {
                        this.forms[entity].isSaving = false
                        console.log(error)
                    })
            }
        },
        printForm(is_start, id){
            if(is_start){
                window.open('api/reports/startbillingform/'+id)
            }
            else{
                window.open('api/reports/stopbillingform/'+id)
            }
        },
        printProposal(contract_id){
            window.open('api/reports/contractproposal/'+contract_id)
        },
        setStartBilling(row){
            this.forms.start_billing.fields.date = new Date()
            this.forms.start_billing.fields.contract_id = row.item.contract_id
            this.forms.start_billing.fields.trade_name = row.item.trade_name
            this.forms.start_billing.fields.company_name = row.item.company_name
            this.forms.start_billing.fields.start_billing_date = row.item.start_billing_date
            this.forms.start_billing.fields.contract_floor_area = row.item.contract_floor_area
            this.forms.start_billing.fields.contract_fixed_rent = row.item.contract_fixed_rent
            this.forms.start_billing.fields.oec_cusa = row.item.oec_cusa
            this.forms.start_billing.fields.oec_aircondition = row.item.oec_aircondition
            this.forms.start_billing.fields.oec_processing = row.item.oec_processing
            this.forms.start_billing.fields.contract_remarks = row.item.contract_remarks
            this.showModalStartBilling = true
        },
        saveStartBilling(){
            this.forms.start_billing.isSaving = true
            this.$http.post('api/contract/start_billing', this.forms.start_billing.fields,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then(response => {
                this.forms.start_billing.isSaving = false
                this.tables.contracts.items.filter(c => 
                    c.contract_id == response.data.contract_id
                )[0].start_billing.push(response.data)
                 this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: 'Start billing form has been successfully created.'
                })
                this.showModalStartBilling = false
            }).catch(error => {
                this.forms.start_billing.isSaving = false
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    // this.forms[entity].states[key] = false
                    // this.forms[entity].errors[key] =  errors[key]
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
            })
        },
        setCancelStartBilling(start_billing_id){
            this.showModalCancelStartConfirmation = true
            this.start_billing_id = start_billing_id
        },
        cancelStartBilling(){
            this.forms.start_billing.isSaving = true
            this.$http.get('api/contract/cancel_start_billing/'+ this.start_billing_id,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then(response => {
                this.forms.start_billing.isSaving = false
                var row = this.tables.contracts.items.filter(c => 
                    c.contract_id == response.data.contract_id
                )[0].start_billing
                this.$delete(row, row.findIndex(r => r.start_billing_id == response.data.start_billing_id))
                this.showModalCancelStartConfirmation = false
            }).catch(err => {
                this.forms.start_billing.isSaving = false
                console.log(err)
            })
        },
        setStopBilling(row){
            this.forms.stop_billing.fields.date = new Date()
            this.forms.stop_billing.fields.contract_id = row.item.contract_id
            this.forms.stop_billing.fields.trade_name = row.item.trade_name
            this.forms.stop_billing.fields.company_name = row.item.company_name
            this.forms.stop_billing.fields.termination_date = row.item.termination_date
            this.forms.stop_billing.fields.security_deposit = row.item.security_deposit
            this.forms.stop_billing.fields.power_meter_deposit = row.item.power_meter_deposit
            this.forms.stop_billing.fields.notes = ''
            this.forms.stop_billing.fields.accounting_department = null
            this.forms.stop_billing.fields.accounting_department_others = ''
            this.showModalStopBilling = true
        },
        saveStopBilling(){
            this.forms.stop_billing.isSaving = true
            this.$http.post('api/contract/stop_billing', this.forms.stop_billing.fields,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then(response => {
                this.forms.stop_billing.isSaving = false
                this.tables.contracts.items.filter(c => 
                    c.contract_id == response.data.contract_id
                )[0].stop_billing.push(response.data)
                 this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: 'Stop billing form has been successfully created.'
                })
                this.showModalStopBilling = false
            }).catch(error => {
                this.forms.stop_billing.isSaving = false
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    // this.forms[entity].states[key] = false
                    // this.forms[entity].errors[key] =  errors[key]
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
            })
        },
        setCancelStopBilling(stop_billing_id){
            this.showModalCancelStopConfirmation = true
            this.stop_billing_id = stop_billing_id
        },
        cancelStopBilling(){
            this.forms.stop_billing.isSaving = true
            this.$http.get('api/contract/cancel_stop_billing/'+ this.stop_billing_id,{
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then(response => {
                this.forms.stop_billing.isSaving = false
                var row = this.tables.contracts.items.filter(c => 
                    c.contract_id == response.data.contract_id
                )[0].stop_billing
                this.$delete(row, row.findIndex(r => r.stop_billing_id == response.data.stop_billing_id))
                this.showModalCancelStopConfirmation = false
            }).catch(err => {
                this.forms.stop_billing.isSaving = false
                console.log(err)
            })
        },
        billingForms(row){
            if(row.detailsShowing == true){
                return
            }
            this.$http.get('/api/contract/billingforms/' + row.item.contract_id, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                  }
            })
            .then((response) => {
                row.item.start_billing = response.data.start_billing
                row.item.stop_billing = response.data.stop_billing
            })
        }
    },
    created () {
        this.fillTableList('contracts')
        this.fillTableList('charges')
        this.fillOptionsList('tenants')
        this.fillOptionsList('departments')
        this.fillOptionsList('natureofbusinesses')
        this.fillOptionsList('locations')
        this.fillOptionsList('contracttypes')
        this.fillOptionsList('categories')
        this.fillOptionsList('months')
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
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('13-48') || this.checkRights('13-49') || this.checkRights('13-68') || this.checkRights('13-69') || this.checkRights('13-70') || this.checkRights('13-71')) == false){
                    this.tables.contracts.fields.pop()
                }
            }
            return true
        }
    }
  }
</script>
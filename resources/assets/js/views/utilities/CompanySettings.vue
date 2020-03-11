<style>
    .label-group {
       font-size: 14pt;
       font-weight: bold;

    } 
</style>

<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <notifications group="notification" />
        <div class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Company Settings
                                <!-- <small class="font-italic">Company Info </small> -->
                            </span>
                        </h5>

                        <b-row>
                            <b-col lg=12>
                                <b-tabs v-model="tabIndex" >
                                    <b-tab title="General Settings">
                                        <b-form autocomplete="off">
                                            <b-row>
                                                <b-col sm="3">
                                                </b-col>
                                                <b-col sm="6">
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label required">Company Name :</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-input
                                                                    tab="0"
                                                                    ref="company_name"
                                                                    id="company_name"
                                                                    v-model="forms.companysetting.fields.company_name"
                                                                    debounce="250"
                                                                    type="text"
                                                                    placeholder="Company Name">
                                                                </b-form-input>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label required">Company Address:</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-textarea
                                                                    tab="0"
                                                                    ref="company_address"
                                                                    id="company_address"
                                                                    v-model="forms.companysetting.fields.company_address"
                                                                    debounce="250"
                                                                    type="text"
                                                                    :rows="2"
                                                                    placeholder="Company Address">
                                                                </b-form-textarea>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label required">Email Address :</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-input
                                                                    tab="0"
                                                                    ref="email_address"
                                                                    id="email_address"
                                                                    v-model="forms.companysetting.fields.email_address"
                                                                    debounce="250"
                                                                    type="text"
                                                                    placeholder="Email Address">
                                                                </b-form-input>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label">Mobile Number :</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-input
                                                                    tab="0"
                                                                    ref="mobile_number"
                                                                    id="mobile_number"
                                                                    v-model="forms.companysetting.fields.mobile_number"
                                                                    debounce="250"
                                                                    type="text"
                                                                    placeholder="Mobile Number">
                                                                </b-form-input>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label">Landline :</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-input
                                                                    tab="0"
                                                                    ref="landline"
                                                                    id="landline"
                                                                    v-model="forms.companysetting.fields.landline"
                                                                    debounce="250"
                                                                    type="text"
                                                                    placeholder="Mobile Number">
                                                                </b-form-input>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                    <b-form-group>
                                                        <b-row>
                                                            <b-col lg=3>
                                                                <label class="col-form-label">Logo :</label>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <div class="border" style="height: 150px; width: 200px;">
                                                                    <b-img :src="forms.companysetting.fields.logo" width="200" height="150px"/>
                                                                </div>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row class="mt-2">
                                                            <b-col lg=3>
                                                            </b-col>
                                                            <b-col lg=9>
                                                                <b-form-file @change="fieldChange" ref="file" accept=".jpg, .png, .gif" plain style="display: none;"></b-form-file>
                                                                <b-btn variant="success" @click="$refs.file.$el.click()">
                                                                    <i class="fa fa-file-image-o"></i> Browse
                                                                </b-btn>
                                                                <b-btn variant="danger" @click="$refs.file.reset(), forms.companysetting.fields.logo = null">
                                                                    <i class="fa fa-times"></i> Remove
                                                                </b-btn>
                                                            </b-col>
                                                        </b-row>
                                                    </b-form-group>
                                                </b-col>
                                                <b-col sm="3">
                                                </b-col>
                                            </b-row>
                                        </b-form>
                                    </b-tab>
                                        
                                    <b-tab title="Advance Settings">
                                        <b-row v-if="isProcessed">
                                            <b-col sm=12>
                                                <b-row> 
                                                    <b-col sm=4>
                                                       <b-row>
                                                           <b-col sm=12>
                                                                <label class="label-group">
                                                                    Account Receivables
                                                                </label>
                                                           </b-col>
                                                       </b-row>
                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Account Receivable</label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="ar_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.ar_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                           <b-col sm=12>
                                                                <label class="label-group">
                                                                    Contract Details Accounts
                                                                </label>
                                                           </b-col>
                                                       </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Rental Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="basic_rental_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.basic_rental_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Advance Rental Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="advance_rental_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.advance_rental_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Construction Deposit Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="construction_deposit_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.construction_deposit_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Security Deposit Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="security_deposit_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.security_deposit_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Electric Meter Deposit Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="electric_meter_deposit_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.electric_meter_deposit_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Water Meter Deposit Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="water_meter_deposit_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.water_meter_deposit_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                    </b-col>
                                                    
                                                    <b-col sm=4>
                                                        <b-row>
                                                            <b-col sm=12>
                                                                <label class="label-group">
                                                                    VAT and WTAX Accounts
                                                                </label>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Withholding Tax Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="withholding_tax_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.withholding_tax_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">VAT Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="vat_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.vat_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <label class="label-group">
                                                                    Payment Collection Accounts
                                                                </label>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Cash Payment Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="cash_payment_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.cash_payment_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>


                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Card Payment Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="card_payment_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.card_payment_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>


                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Online Payment Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="vat_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.online_payment_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Discount Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="discount_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.discount_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Advance Payment Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="payment_advances_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.payment_advances_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                    </b-col>

                                                    <b-col sm=4>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <label class="label-group">
                                                                    Other Accounts
                                                                </label>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Adjustment In Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="adjustment_in_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.adjustment_in_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Adjustment Out Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="adjustment_out_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.adjustment_out_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Interest Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="interest_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.interest_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Penalty Account </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="penalty_account_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.penalty_account_id"
                                                                    >
                                                                        <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>

                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Account Department</label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="account_department_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Account Title'"
                                                                        v-model="forms.companysetting.fields.account_department_id"
                                                                    >
                                                                        <option v-for="department in options.csdepartments.items" :key="department.department_id" :value="department.department_id">{{department.department_name}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>
                                                        <b-row>
                                                            <b-col sm=12>
                                                                <b-form-group>
                                                                    <label class="required">Electric Withholding Charge </label>
                                                                    <select2
                                                                        tab="1"
                                                                        ref="charge_id"
                                                                        :allowClear="false"
                                                                        :placeholder="'Select Charge'"
                                                                        v-model="forms.companysetting.fields.electric_charge_id"
                                                                    >
                                                                        <option v-for="charge in options.charges.items" :key="charge.charge_id" :value="charge.charge_id">{{charge.charge_desc}}</option>
                                                                    </select2>
                                                                </b-form-group>
                                                            </b-col>
                                                        </b-row>
                                                    </b-col>
                                                </b-row>
                                            </b-col>
                                        </b-row>
                                    </b-tab>


                                    <b-tab title="SOA Notes">
                                        <b-row>
                                            <b-col  sm="12">
                                                <b-button class="float-right mb-2" variant="primary" v-if="this.tables.notes.items.length == 0" @click="addNotes()">
                                                        <i class="fa fa-plus-circle"></i> Add Notes
                                                </b-button>
                                            </b-col>
                                        </b-row>
                                        <b-table 
                                            responsive
                                            small bordered
                                            :fields="tables.notes.fields"
                                            :items.sync="tables.notes.items"
                                            show-empty>
                                            <template v-slot:cell(notes)="data">
                                                <b-form-input 
                                                    id="notes"
                                                    placeholder="Notes"
                                                    v-model="data.item.notes">
                                                </b-form-input>
                                            </template>
                                            <template v-slot:cell(action)="data" >
                                                <b-btn :size="'sm'" variant="primary" @click="addNotes()">
                                                    <i class="fa fa-plus-circle"></i>
                                                </b-btn>

                                                <b-btn :size="'sm'" variant="danger" @click="removeNotes(data.index)">
                                                    <i class="fa fa-times-circle"></i>
                                                </b-btn>
                                            </template>
                                        </b-table>
                                    </b-tab>
                                </b-tabs>
                            </b-col>
                            
                        </b-row>
                        <template v-slot:footer>
                            <b-row class="pull-right mt-2">
                                <b-col sm="12">
                                    <b-button 
                                        :disabled="forms.companysetting.isSaving" 
                                        variant="success" 
                                        @click="onCompanySettingEntry">
                                        <icon v-if="forms.companysetting.isSaving" name="sync" spin></icon>
                                        <i class="fa fa-check"></i>
                                        Save
                                    </b-button>
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script>
export default {
    name: 'companysettings',
    data () {
        return {
            entryMode: 'Edit',
            showModalEntry: false, //if true show modal
            showModalDelete: false,
            tabIndex: 0,
            options: {
                accounts: {
                    items: []
                },
                charges: {
                    items: []
                },
                csdepartments: {
                    items: []
                }
            },
            forms: {
                companysetting : {
                    isSaving: false,
                    fields: {
                        company_id: null,
                        company_name: null,
                        company_address: null,
                        email_address: null,
                        mobile_number: null,
                        landline: null,
                        logo: null,
                        advance_rental_account_id: 0,
                        security_deposit_account_id: 0,
                        electric_meter_deposit_account_id: 0,
                        water_meter_deposit_account_id: 0,
                        construction_deposit_account_id: 0,
                        withholding_tax_account_id: 0,
                        vat_account_id: 0,
                        adjustment_in_account_id: 0,
                        adjustment_out_account_id: 0,
                        discount_account_id: 0,
                        interest_account_id: 0,
                        penalty_account_id: 0,
                        cash_payment_account_id: 0,
                        card_payment_account_id: 0,
                        online_payment_account_id: 0,
                        ar_account_id: 0,
                        payment_advances_account_id: 0,
                        account_department_id: 0,
                        electric_charge_id: 0,
                    },
                }
            },
            tables: {
                notes: {
                    fields: [
                        {
                            key: 'notes',
                            label: 'Notes'
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '75px'},
                        }
                    ],
                    items: []
                },
            },
            image: new FormData,
            row: [],
            isProcessed: false
        }
    },
    methods:{
        addNotes(){
            this.tables.notes.items.push({
                contract_schedule_notes:''
            })
        },
        removeNotes(index){
            this.tables.notes.items.splice(index, 1)
        },
        onCompanySettingEntry () {
            this.updateEntity('companysetting', 'company_id', true, this.row, true)
            this.insertNotes()
        },
        insertNotes(){
            this.$http.post('/api/companysetting/notes', this.tables.notes, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                  }
            })
            .then((response) => {
                return response
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
        onUserDelete(){
            this.deleteEntity('user', this.id, true, 'users', 'id')
        },
        setDelete(data){
            this.showModalDelete=true
            this.id = data.item.id
        },
        fieldChange(e){
            let attachment = e.target.files[0]
            let path = 'uploads/logo'

            this.image.append('file', attachment)
            this.image.append('path', path)

            this.$http.post('/api/upload', this.image, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                      'Content-Type' : 'multipart/form-data'
                  }
            })
            .then((response) => {
                this.forms.companysetting.fields.logo = response.data.path
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        }
    },
    async created () {
        await this.$http.get('/api/companysettingnotes', {
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token'),
                }
        })
        .then((response) => {
            this.tables.notes.items = response.data.notes
            this.options.accounts.items = response.data.accounts
            this.options.charges.items = response.data.charges
            this.options.csdepartments.items = response.data.departments
            this.forms.companysetting.fields = response.data.company
            this.isProcessed = true
        })
        .catch(error => {
            if (!error.response) return
            console.log(error)
        })
    },
  }
</script>


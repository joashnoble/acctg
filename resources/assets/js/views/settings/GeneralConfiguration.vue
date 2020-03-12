<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >General Configuration
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
            <div>
            <b-tabs content-class="mt-3">
                <b-tab title="Company" Active>
                    <b-form autocomplete="off">
                        <b-row class="mb-2">
                            <b-col sm="12">
                            <span>Company Profile</span><br>
                            <small><i>Note: Information entered will be used as heading for all PDF and Excel reports.</i> </small>
                            </b-col>
                        </b-row>
                        <b-row> 
                            <b-col sm="8">
                                <b-row>
                                    <b-col sm="6" >
                                        <b-form-group>
                                            <label class="required" for="company_name">Company Name</label>
                                            <b-form-input
                                                id="company_name"
                                                v-model="forms.companyprofile.fields.company_name"
                                                debounce="250"
                                                type="text"
                                                placeholder="Company Name">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="6" >
                                        <b-form-group>
                                            <label class="required" for="email_address">Email Address</label>
                                            <b-form-input
                                                id="email_address"
                                                v-model="forms.companyprofile.fields.email_address"
                                                debounce="250"
                                                type="text"
                                                placeholder="Email Address">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row> <!-- END OF ROW -->
                                <b-row>
                                    <b-col sm="12">
                                        <b-form-group>
                                            <label class="required" for="company_address">Company Address</label>
                                            <b-form-input
                                                id="company_address"
                                                v-model="forms.companyprofile.fields.company_address"
                                                debounce="250"
                                                type="text"
                                                placeholder="Company Address">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col sm="6" >
                                        <b-form-group>
                                            <label class="required" for="mobile_no">Mobile Number</label>
                                            <b-form-input
                                                id="mobile_no"
                                                v-model="forms.companyprofile.fields.mobile_no"
                                                debounce="250"
                                                type="text"
                                                placeholder="Mobile Number">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="6" >
                                        <b-form-group>
                                            <label class="required" for="landline">Landline Number</label>
                                            <b-form-input
                                                id="landline"
                                                v-model="forms.companyprofile.fields.landline"
                                                debounce="250"
                                                type="text"
                                                placeholder="Landline">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col sm="6">
                                        <b-form-group>
                                            <label> Business Structure</label>
                                            <select2
                                                ref="brand_id"
                                                :allowClear="false"
                                                :placeholder="''"
                                                v-model="forms.companyprofile.fields.business_type"
                                                :value="forms.companyprofile.fields.business_type"
                                            >
                                            <option value="1">Sole Proprietorship</option>
                                            <option value="2">Partnership</option>
                                            <option value="3">Corporation</option>
                                        </select2>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="6">
                                        <b-form-group>
                                            <label class="required"> Tax Type </label>
                                            <select2
                                                ref="tax_type_id"
                                                :allowClear="false"
                                                :placeholder="'Select Tax Type'"
                                                v-model="forms.companyprofile.fields.tax_type_id"
                                            >
                                                <option v-for="taxtype in options.taxtypes.items" :key="taxtype.tax_type_id" :value="taxtype.tax_type_id">{{taxtype.tax_type}}</option>
                                            </select2>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                            <b-col sm="4">
                                <b-row>                                    
                                    <b-col lg="12">
                                        <label class="col-form-label">Logo :</label>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col lg="12">
                                        <div class="border" style="border:1px solid black;height: 200px;width: 200px;vertical-align: middle;">
                                            <b-img :src="forms.companyprofile.fields.logo_path" style="object-fit: fill !important; height: 100%;width: 100%;"/>
                                        </div>
                                    </b-col>
                                </b-row>
                                <b-row class="mt-2">
                                    <b-col lg="12">
                                        <b-form-file v-on:change="onImageChange" ref="file" accept=".jpg, .png, .gif" plain style="display: none;"></b-form-file>
                                        <b-btn variant="success" @click="$refs.file.$el.click()">
                                            <i class="fa fa-file-image-o"></i> Browse
                                        </b-btn>
                                        <b-btn variant="danger" @click="$refs.file.reset(), forms.companyprofile.fields.logo_path = 'uploads/defaults/default-logo.jpg'">
                                            <i class="fa fa-times"></i> Remove
                                        </b-btn>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.companyprofile.isSaving" 
                                    variant="success" 
                                    @click="onSaveFA">
                                    <b-spinner v-if="forms.companyprofile.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Company Profile
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="BIR">
                    <b-form autocomplete="off">
                        <b-row class="mb-2">
                            <b-col sm="12">
                            <span>Bureau of Internal Revenue Profile</span><br>
                            <small><i>Note: Information entered will be used for all generated BIR Forms.</i> </small>
                            </b-col>
                        </b-row>
                        <b-row> 
                            <b-col sm="12">
                                <b-row>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="registered_to">Taxpayer's Name</label>
                                            <b-form-input
                                                id="registered_to"
                                                v-model="forms.birform.fields.registered_to"
                                                debounce="250"
                                                type="text"
                                                placeholder="Taxpayer's Name">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="8" >
                                        <b-form-group>
                                            <label class="required" for="registered_address">Registered Address </label>
                                            <b-form-input
                                                id="registered_address"
                                                v-model="forms.birform.fields.registered_address"
                                                debounce="250"
                                                type="text"
                                                placeholder="Registered Address">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="zip_code">Zip Code</label>
                                            <b-form-input
                                                id="zip_code"
                                                v-model="forms.birform.fields.zip_code"
                                                debounce="250"
                                                type="text"
                                                placeholder="Zip Code">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="telephone_no">Telephone No</label>
                                            <b-form-input
                                                id="telephone_no"
                                                v-model="forms.birform.fields.telephone_no"
                                                debounce="250"
                                                type="text"
                                                placeholder="Telephone No">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="tin_no">Tax Identification Number (TIN)</label>
                                            <b-form-input
                                                id="tin_no"
                                                v-model="forms.birform.fields.tin_no"
                                                debounce="250"
                                                type="text"
                                                placeholder="Tax Identification Number">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-row>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="rdo_no">Revenue District Office (RDO) Number</label>
                                            <b-form-input
                                                id="rdo_no"
                                                v-model="forms.birform.fields.rdo_no"
                                                debounce="250"
                                                type="text"
                                                placeholder="Revenue District Office Number">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="nature_of_business">Nature of Business</label>
                                            <b-form-input
                                                id="nature_of_business"
                                                v-model="forms.birform.fields.nature_of_business"
                                                debounce="250"
                                                type="text"
                                                placeholder="Nature of Business">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col sm="4" >
                                        <b-form-group>
                                            <label class="required" for="industry_classification">Industry Classification</label>
                                            <b-form-input
                                                id="industry_classification"
                                                v-model="forms.birform.fields.industry_classification"
                                                debounce="250"
                                                type="text"
                                                placeholder="Industry Classification">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.birform.isSaving" 
                                    variant="success" 
                                    @click="onSaveBir">
                                    <b-spinner v-if="forms.birform.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Bureau of Internal Revenue Profile
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>   
                <b-tab title="Supplier" >
                    <b-form autocomplete="off">
                        <b-row>
                        <b-col sm="6">
                            <b-form-group>
                                <label> Input Tax Account </label>
                                <select2
                                    :allowClear="false"
                                    :placeholder="''"
                                    :value="forms.suppliers.fields.input_tax_account_id"
                                    v-model="forms.suppliers.fields.input_tax_account_id"
                                >
                                <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                </select2>
                                <small><i>Input Tax is generally apply to the purchases of goods and services.</i></small>
                            </b-form-group>
                        </b-col>
                        <b-col sm="6">
                            <b-form-group>
                                <label> Payable to Supplier  </label>
                                <select2
                                    :allowClear="false"
                                    :placeholder="''"
                                    v-model="forms.suppliers.fields.payable_account_id"
                                    :value="forms.suppliers.fields.payable_account_id"
                                >
                                <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                </select2>
                                <small><i>Account that is used to represent the amount owes by the company.</i></small>
                            </b-form-group>
                        </b-col>
                        </b-row>
                        <b-row>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Discount from Supplier  </label>
                                    <select2
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.suppliers.fields.payable_discount_account_id"
                                        :value="forms.suppliers.fields.payable_discount_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Account that is used to represent the discount from the supplier.</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Payment to Supplier </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.suppliers.fields.payment_to_supplier_id"
                                        :value="forms.suppliers.fields.payment_to_supplier_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where payment to supplier will be credited.</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Withholding Tax Account </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.suppliers.fields.supplier_wtax_account_id"
                                        :valuee="forms.suppliers.fields.supplier_wtax_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where supplier withholding tax payables will be credited..</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.suppliers.isSaving" 
                                    variant="success" 
                                    @click="onSave('suppliers')">
                                    <b-spinner v-if="forms.suppliers.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Supplier Configurations
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="Customer">
                    <b-form autocomplete="off">
                         <b-row>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Output Tax Account </label>
                                    <select2
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.customers.fields.output_tax_account_id"
                                        :value="forms.customers.fields.output_tax_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Output tax is the amount charge on your own sales if you are registered as Vatted.</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Receivable from Customer  </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.customers.fields.receivable_account_id"
                                        :value="forms.customers.fields.receivable_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Account that represents the amount of goods and services credited by customer.</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                         <b-row>
                            <b-col sm="6">
                                <b-form-group>
                                    <label>  Discount to Customer  </label>
                                    <select2
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.customers.fields.receivable_discount_account_id"
                                        :value="forms.customers.fields.receivable_discount_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Account that is used to represent the discount you give to customers.</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Collection Account </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.customers.fields.payment_from_customer_id"
                                        :value="forms.customers.fields.payment_from_customer_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where payment of customer will be posted.</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.customers.isSaving" 
                                    variant="success" 
                                    @click="onSave('customers')">
                                    <b-spinner v-if="forms.customers.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Customer Configurations
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="Inventory" >
                    <b-form autocomplete="off">
                        <b-row class="mb-2">
                            <b-col sm="12">
                                <span>INVENTORY COMPUTATION</span><br>
                                <i><small>Select if Sales, Cash, and Dispatching Invoices are included in the computation of inventory.</small></i>
                            </b-col>
                            
                        </b-row>
                         <b-row>
                            <b-col sm="4">
                                <b-form-group>
                                    <label> Sales Invoices </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.inventory.fields.sales_invoice_inventory"
                                        :value="forms.inventory.fields.sales_invoice_inventory"
                                    >
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                   </select2>
                                </b-form-group>
                            </b-col>
                            <b-col sm="4">
                                <b-form-group>
                                    <label> Cash Invoices </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.inventory.fields.cash_invoice_inventory"
                                        :value="forms.inventory.fields.cash_invoice_inventory"
                                    >
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                   </select2>
                                </b-form-group>
                            </b-col>
                            <b-col sm="4">
                                <b-form-group>
                                    <label> Dispatching Invoices </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.inventory.fields.dispatching_invoice_inventory"
                                        :value="forms.inventory.fields.dispatching_invoice_inventory"
                                    >
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                   </select2>
                                </b-form-group>
                            </b-col>
                         </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.inventory.isSaving" 
                                    variant="success" 
                                    @click="onSave('inventory')">
                                    <b-spinner v-if="forms.inventory.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Inventory Configurations
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="Other Accounts" >
                    <b-form autocomplete="off">
                         <b-row>
                            <b-col sm="6" hidden>
                                <b-form-group>
                                    <label> Retained Earnings </label>
                                    <select2
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.others.fields.retained_earnings_id"
                                        :value="forms.others.fields.retained_earnings_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where net income will be forwarded.</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Petty Cash  </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.others.fields.petty_cash_account_id"
                                        :value="forms.others.fields.petty_cash_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where petty cash will be forwarded.</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label> Depreciation Expense (Debit) </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.others.fields.depreciation_expense_debit_id"
                                        :value="forms.others.fields.depreciation_expense_debit_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Account that represents the Debit Entry of the Depreciation Expense.</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                         <b-row>
                            <b-col sm="6" hidden>
                                <b-form-group>
                                    <label>  Fixed Asset  </label>
                                    <select2
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.others.fields.fixed_asset_account_id"
                                        :value="forms.others.fields.fixed_asset_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Please select the account where the fixed asset will be forwarded..</i></small>
                                </b-form-group>
                            </b-col>
                            <b-col sm="6" offset="6">
                                <b-form-group>
                                    <label> Depreciation Expense (Credit) </label>
                                    <select2
                                        ref="brand_id"
                                        :allowClear="false"
                                        :placeholder="''"
                                        v-model="forms.others.fields.depreciation_expense_credit_id"
                                        :value="forms.others.fields.depreciation_expense_credit_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitles.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                    <small><i>Account that represents the Credit Entry of the Depreciation Expense.</i></small>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.others.isSaving" 
                                    variant="success" 
                                    @click="onSave('others')">
                                    <b-spinner v-if="forms.others.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Customer Configurations
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="Accounting Period" >
                    <b-row>
                        <b-col sm="4">
                            Close Accounting Period | Feature not ready
                        </b-col>
                        <b-col offset="4" sm="4">
                            <b-form-input 
                                v-model="filters.accountingperiod.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                            </b-form-input>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-table striped borderless show-empty
                        :fields="tables.accountingperiod.fields"
                        :items.sync="tables.accountingperiod.items"
                        :filter="filters.accountingperiod.criteria"
                        :current-page="paginations.accountingperiod.currentPage"
                        :per-page="paginations.accountingperiod.perPage"
                        @filtered="onFiltered($event,'accountingperiod')"
                        >
                        </b-table>
                    </b-row>
                    <b-row mb="0">
                        <b-col sm="12" class="my-1 mb-0" >
                            <b-pagination mb="0"
                            v-model="paginations.accountingperiod.currentPage"
                            :total-rows="paginations.accountingperiod.totalRows"
                            :per-page="paginations.accountingperiod.perPage"
                            prev-text="Previous"
                            next-text="Next"
                            hide-goto-end-buttons
                            size="sm"
                            align="right"
                            ></b-pagination>
                        </b-col>
                    </b-row> 
                </b-tab>
                <b-tab title="Statement of Accounts">
                    <b-form autocomplete="off">
                        <b-row class="mb-2">
                            <b-col sm="12">
                            <span>RECEIVABLES FROM CUSTOMERS</span><br>
                            <small><i>Note: Please check the appropriate accounts to be included in the computation of Total of Receivables, Statement of Accounts, and Aging of Receivables</i> </small>
                            </b-col>
                        </b-row>
                        <b-row> 
                            <b-col sm="4" v-for="soaaccounttitle in options.soaacccounttitles.items" :key="soaaccounttitle.account_id" >
                                <b-form-group >
                                    <b-form-checkbox
                                        size="sm" 
                                        value="1"
                                        v-model="soaaccounttitle.checked"  
                                        unchecked-value="0"
                                        >
                                        {{soaaccounttitle.account_title}}
                                    </b-form-checkbox>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.statementofaccounts.isSaving" 
                                    variant="success" 
                                    @click="onSaveSOA">
                                    <b-spinner v-if="forms.statementofaccounts.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Accounts Receivable Configuration
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
                <b-tab title="Fixed Assets">
                    <b-form autocomplete="off">
                        <b-row class="mb-2">
                            <b-col sm="12">
                            <span>FIXED ASSET AUTO GENERATION SETTINGS</span><br>
                            <small><i>Note: Please check the appropriate Fixed Asset Accounts used in Product Management (Link to Debit Account) to be included in the Fixed Asset Generation in Purchase Journal</i> </small>
                            </b-col>
                        </b-row>
                        <b-row> 
                            <b-col sm="4" v-for="faaccounttitle in options.faacccounttitles.items" :key="faaccounttitle.account_id" >
                                <b-form-group >
                                    <b-form-checkbox
                                        size="sm" 
                                        value="1"
                                        v-model="faaccounttitle.checked"  
                                        unchecked-value="0"
                                        >
                                        {{faaccounttitle.account_title}}
                                    </b-form-checkbox>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <b-row class="">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.fixedassets.isSaving" 
                                    variant="success" 
                                    @click="onSaveFixedA">
                                    <b-spinner v-if="forms.fixedassets.isSaving" small variant="light" label="Spinning"></b-spinner>
                                    <i class="fa fa-check"></i>
                                    Save Fixed Assets Configuration
                                </b-button>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-tab>
            </b-tabs>
            </div>
      </b-card>
</div>
</template>
<style>
.custom-control-label {
    font-weight: normal!important;
}
</style>
<script>
export default {
    name: 'generalconfiguration',
    components :{
    },
    data(){
        return {
            image: new FormData,
            tables: {
                accountingperiod :{
                    fields:[
                        {
                            key:'date_covered',
                            label: 'Period',
                            sortable: true
                        },
                        {
                            key:'date_time_closed',
                            label: 'Date and Time Closed'
                        },
                        {
                            key:'user',
                            label:'Closed By'
                        },
                        {
                            key:'remarks',
                            label:'Remarks'
                        }
                    ],
                     items: [],
                },
            }, // END OF TABLES
            filters: {
                accountingperiod: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                accountingperiod: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
            options:{
                accounttitles : {
                    items: []
                },
                soaacccounttitles :{
                    items: []
                },
                faacccounttitles : {
                    items: []
                },
                taxtypes : {
                    items: []
                }
            },
            forms:{
                suppliers:{
                    isSaving: false,
                    fields:{
                        input_tax_account_id : null,
                        payable_account_id : null,
                        payable_discount_account_id : null,
                        payment_to_supplier_id : null,
                        supplier_wtax_account_id : null,
                    }
                },
                customers:{
                    isSaving: false,
                    fields:{
                        output_tax_account_id : null,
                        receivable_account_id : null,
                        receivable_discount_account_id : null,
                        payment_from_customer_id : null,
                    }
                },
                others:{
                    isSaving: false,
                    fields:{
                        retained_earnings_id : null,
                        petty_cash_account_id : null,
                        depreciation_expense_debit_id : null,
                        fixed_asset_account_id : null,
                        depreciation_expense_credit_id : null,
                    }
                },
                inventory:{
                    isSaving: false,
                    fields:{
                        sales_invoice_inventory : null,
                        cash_invoice_inventory : null,
                        dispatching_invoice_inventory : null,
                    }
                },
                statementofaccounts:{
                    isSaving : false,
                },
                fixedassets:{
                    isSaving : false,
                },
                companyprofile:{
                    isSaving : false,
                    fields : {
                        company_name : null,
                        company_address : null,
                        email_address : null,
                        mobile_no : null,
                        landline : null,
                        business_type : null,
                        logo_path : '',
                        tax_type_id : null,
                    }
                },
                birform:{
                    isSaving : false,
                    fields : {
                        registered_to : null,
                        registered_address : null,
                        zip_code : null,
                        telephone_no : null,
                        tin_no : null,
                        rdo_no : null,
                        nature_of_business : null,
                        industry_classification : null,
                    }
                },
            }
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.refreshSettings()
        this.fillTableList('accountingperiod');
        this.fillOptionsList('taxtypes');
    }, // END OF CREATED
    methods:{
        async refreshSettings(){
         await this.$http.get('/api/companyinfo',{
            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
            .then((response) => {
                const records = response.data
                this.forms.companyprofile.fields = records.companyinfo
                this.forms.birform.fields = records.companyinfo
            })
            .catch(error => { if (!error.response) return console.log(error)
            })
         await this.$http.get('/api/generalconfigurationoptions',{
            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
            .then((response) => {
                const records = response.data
                this.options.accounttitles.items = records.accounttitles
                this.options.soaacccounttitles.items = records.soaacccounttitles
                this.options.faacccounttitles.items = records.faacccounttitles
            })
            .catch(error => { if (!error.response) return console.log(error)
            })

         await this.$http.get('/api/generalconfiguration',{
            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
            .then((response) => {
                const records = response.data
                this.forms.suppliers.fields = records.current
                this.forms.customers.fields = records.current
                this.forms.others.fields = records.current
                this.forms.inventory.fields = records.current
            })
            .catch(error => { if (!error.response) return console.log(error)
            })
        },
        onSave(utility){
            this.forms[utility].isSaving = true;
            this.$http.put('api/genconf'+utility, this.forms[utility].fields ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
          .then((response) => {  
            this.forms[utility].isSaving = false;
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
              text: 'The '+utility+' settings have been successfully modified.'
            })
          }).catch(error => {
                this.forms[utility].isSaving = false;
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key, is_tab)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
          })
        },
        async onSaveSOA (){
            this.forms.statementofaccounts.isSaving = true;
            var tblft = this.options.soaacccounttitles.items.filter(ft =>ft.checked == "1") // FILTER, ONLY CHECKED WILL BE PUSHED
            let objSelected = { selectedItems: tblft } // THEN PUSH AS ARRAY
            await this.$http.put('api/genconfsoa', objSelected ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
            .then((response) => {  
            this.forms.statementofaccounts.isSaving = false;
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
                text: 'The Statement of Accounts settings have been successfully modified.'
            })
            }).catch(error => {
                this.forms.statementofaccounts.isSaving = false;
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key, is_tab)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
            })
        },
        async onSaveFixedA (){
            this.forms.fixedassets.isSaving = true;
            var tblft = this.options.faacccounttitles.items.filter(ft =>ft.checked == "1") // FILTER, ONLY CHECKED WILL BE PUSHED
            let objSelected = { selectedItems: tblft } // THEN PUSH AS ARRAY
            await this.$http.put('api/genconffa', objSelected ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
            .then((response) => {  
            this.forms.fixedassets.isSaving = false;
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
                text: 'The Fixed Asset Accounts settings have been successfully modified.'
            })
            }).catch(error => {
                this.forms.fixedassets.isSaving = false;
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key, is_tab)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
            })
        },
        async  onImageChange(e) {
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)  return;
            this.image.append('file', e.target.files[0])
            this.image.append('path', 'uploads/logo') // Directory Path
            this.image.append('name', e.target.files[0].name)

            await this.$http.post('/api/uploadlogo', this.image, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token'),
                        'Content-Type' : 'multipart/form-data'
                    }
                })
            .then((response) => {
                this.forms.companyprofile.fields.logo_path = response.data.path
            })
            .catch(error => {
                if (!error.response) return
                console.log(error)
            })
        },
        async onSaveFA (){
            this.forms.companyprofile.isSaving = true;
            this.$http.put('api/genconfcompany', this.forms.companyprofile.fields ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
          .then((response) => {  
            this.forms.companyprofile.isSaving = false;
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
                text: 'The Company Profile settings have been successfully modified.'
            })
            }).catch(error => {
                this.forms.fixedassets.isSaving = false;
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key, is_tab)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
            })
        },
        async onSaveBir (){
            this.forms.birform.isSaving = true;
            this.$http.put('api/genconfbir', this.forms.birform.fields ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
          .then((response) => {  
            this.forms.birform.isSaving = false;
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
                text: 'The Bir Form settings have been successfully modified.'
            })
            }).catch(error => {
                this.forms.fixedassets.isSaving = false;
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key, is_tab)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
            })
        },
    } // END OF METHODS
}// END OF EXPORT 
</script>

<template>
    <div v-show="showEntry" class="animated fadeIn">
        <b-row>
            <b-col sm="12">
                <b-card class="card-accent-dark">
                    <h5 slot="header">
                        <span>
                            Tenant Entry - {{entryMode}}
                        </span>
                    </h5>
                    <b-form autocomplete="off">
                        <b-row>
                            <b-col sm="4">
                                <div class="border border-dark text-center">
                                    COMPANY INFORMATION
                                </div>
                                <br>
                                <b-form-group>
                                    <label>Tenant Code</label>
                                    <b-form-input
                                        id="tenant_code"
                                        v-model="forms.tenant.fields.tenant_code"
                                        debounce="250"
                                        type="text"
                                        readonly
                                        placeholder="Auto">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Trade Name</label>
                                    <b-form-input
                                        ref="trade_name"
                                        id="trade_name"
                                        v-model="forms.tenant.fields.trade_name"
                                        debounce="250"
                                        type="text"
                                        placeholder="Trade Name">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Company Name</label>
                                    <b-form-input
                                        ref="company_name"
                                        id="company_name"
                                        v-model="forms.tenant.fields.company_name"
                                        debounce="250"
                                        type="text"
                                        placeholder="Company Name">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Space Code</label>
                                    <b-form-input
                                        ref="space_code"
                                        id="space_code"
                                        v-model="forms.tenant.fields.space_code"
                                        debounce="250"
                                        type="text"
                                        placeholder="Space Code">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Business Concept</label>
                                    <b-form-input
                                        ref="business_concept"
                                        id="business_concept"
                                        v-model="forms.tenant.fields.business_concept"
                                        debounce="250"
                                        type="text"
                                        placeholder="Business Concept">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Head Office Address</label>
                                    <b-form-textarea
                                        ref="head_office_address"
                                        id="head_office_address"
                                        v-model="forms.tenant.fields.head_office_address"
                                        debounce="250"
                                        type="text"
                                        :rows="2"
                                        placeholder="Head Office Address">
                                    </b-form-textarea>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Billing Address</label>
                                    <b-form-textarea
                                        ref="billing_address"
                                        id="billing_address"
                                        v-model="forms.tenant.fields.billing_address"
                                        debounce="250"
                                        type="text"
                                        :rows="2"
                                        placeholder="Billing Address">
                                    </b-form-textarea>
                                </b-form-group>
                            </b-col>
                            <b-col sm="4">
                                <div class="border border-dark text-center">
                                    CONTRACT SIGNATORY/CONTACT PERSON
                                </div>
                                <br>
                                <b-form-group>
                                    <label class="required">Contact Person</label>
                                    <b-form-input
                                        ref="contact_person"
                                        id="contact_person"
                                        v-model="forms.tenant.fields.contact_person"
                                        debounce="250"
                                        type="text"
                                        placeholder="Contact Person">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Designation</label>
                                    <b-form-input
                                        ref="designation"
                                        id="designation"
                                        v-model="forms.tenant.fields.designation"
                                        debounce="250"
                                        type="text"
                                        placeholder="Designation">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Contact Number</label>
                                    <b-form-input
                                        ref="contact_number"
                                        id="contact_number"
                                        v-model="forms.tenant.fields.contact_number"
                                        debounce="250"
                                        type="text"
                                        placeholder="Contact Person">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Email Address</label>
                                    <b-form-input
                                        ref="email_address"
                                        id="email_address"
                                        v-model="forms.tenant.fields.email_address"
                                        debounce="250"
                                        type="text"
                                        placeholder="Email Address">
                                    </b-form-input>
                                </b-form-group>
                                <b-row>
                                    <b-col lg=6>
                                        <b-form-group>
                                            <label class="required">TIN</label>
                                            <b-form-input
                                                ref="tin_number"
                                                id="tin_number"
                                                v-model="forms.tenant.fields.tin_number"
                                                debounce="250"
                                                type="text"
                                                placeholder="TIN">
                                            </b-form-input>
                                        </b-form-group>
                                    </b-col>
                                    <b-col lg=6>
                                        <b-form-group>
                                            <label>VAT</label>
                                            <vue-autonumeric
                                                ref="adjustment_rate"
                                                v-model="forms.tenant.fields.vat_percent"
                                                :class="'form-control text-right'" 
                                                :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                            </vue-autonumeric>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                                <b-form-group>
                                    <label>Is Vatted?</label>
                                    <b-form-radio-group 
                                        id="is_vatted"
                                        v-model="forms.tenant.fields.is_vatted">
                                        <b-form-radio value="1">Vat</b-form-radio>
                                        <b-form-radio value="0">Non-Vat</b-form-radio>
                                    </b-form-radio-group>
                                </b-form-group>
                                <b-form-group>
                                    <label>Auto Penalty Previous Balance</label>
                                    <b-form-radio-group 
                                        id="is_auto"
                                        v-model="forms.tenant.fields.is_auto">
                                        <b-form-radio value="1">On</b-form-radio>
                                        <b-form-radio value="0">Off</b-form-radio>
                                    </b-form-radio-group>
                                </b-form-group>
                                <b-form-group>
                                    <label>Is Withhold Electricity?</label>
                                    <b-form-radio-group 
                                        id="is_withhold_electricity"
                                        v-model="forms.tenant.fields.is_withhold_electricity">
                                        <b-form-radio value="1">Yes</b-form-radio>
                                        <b-form-radio value="0">No</b-form-radio>
                                    </b-form-radio-group>
                                </b-form-group>
                            </b-col>
                            <b-col sm="4">
                                <div class="border border-dark text-center">
                                    DOCUMENTS
                                </div>
                                <br>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="business_permit"
                                        v-model="forms.tenant.fields.business_permit"
                                        value="1"
                                        unchecked-value="0">
                                        Business Permits
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="tenant_information_sheet"
                                        v-model="forms.tenant.fields.tenant_information_sheet"
                                        value="1"
                                        unchecked-value="0">
                                        Tenant Information Sheet
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="valid_id"
                                        v-model="forms.tenant.fields.valid_id"
                                        value="1"
                                        unchecked-value="0">
                                        Valid ID of the Signatory
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="tin_cor"
                                        v-model="forms.tenant.fields.tin_cor"
                                        value="1"
                                        unchecked-value="0">
                                        TIN/COR
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="dti_sec"
                                        v-model="forms.tenant.fields.dti_sec"
                                        value="1"
                                        unchecked-value="0">
                                        DTI Permit/SEC Registration
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="notarized_contract"
                                        v-model="forms.tenant.fields.notarized_contract"
                                        value="1"
                                        unchecked-value="0">
                                        Notarized Contract of Lease
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="proof_of_billing"
                                        v-model="forms.tenant.fields.proof_of_billing"
                                        value="1"
                                        unchecked-value="0">
                                        Proof of Billing
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="clearance_to_operate"
                                        v-model="forms.tenant.fields.clearance_to_operate"
                                        value="1"
                                        unchecked-value="0">
                                        Clearance to Operate
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-checkbox
                                        id="others"
                                        @change="forms.tenant.fields.others_specify=null"
                                        v-model="forms.tenant.fields.others"
                                        value="1"
                                        unchecked-value="0">
                                        Others (Please Specify)
                                    </b-form-checkbox>
                                </b-form-group>
                                <b-form-group>
                                    <b-form-textarea
                                        id="others_specify"
                                        :disabled="forms.tenant.fields.others == 1 ? false : true"
                                        v-model="forms.tenant.fields.others_specify"
                                        type="text"
                                        :rows="2"
                                        placeholder="Specify">
                                    </b-form-textarea>
                                </b-form-group>
                                <b-form-group>
                                    <label>Billing Notes</label>
                                    <ckeditor v-model="forms.tenant.fields.billing_notes" :config="editorConfig"></ckeditor>
                                </b-form-group>
                            </b-col>
                        </b-row>
                    </b-form>
                    <template v-slot:footer>
                        <b-row class="pull-right mt-2">
                            <b-col sm="12">
                                <b-button 
                                    :disabled="forms.tenant.isSaving" 
                                    variant="success" 
                                    @click="onTenantEntry">
                                    <icon v-if="forms.tenant.isSaving" name="sync" spin></icon>
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
</template>
<script>
export default {
    name: 'tenantentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showEntry: false, //if true show entry
            forms: {
                tenant: {
                    isSaving: false,
                    isDeleting: false,
                    fields: {
                        tenant_id: null,
                        tenant_code: null,
                        trade_name: null,
                        company_name: null,
                        space_code: null,
                        business_concept: null,
                        head_office_address: null,
                        billing_address: null,
                        contact_person: null,
                        designation: null,
                        contact_number: null,
                        email_address: null,
                        tin_number: null,
                        vat_percent: 12,
                        is_vatted: 1,
                        is_auto: 1,
                        business_permit: 0,
                        tenant_information_sheet: 0,
                        valid_id: 0,
                        tin_cor: 0,
                        dti_sec: 0,
                        notarized_contract: 0,
                        proof_of_billing: 0,
                        clearance_to_operate: 0,
                        others: 0,
                        others_specify: null,
                        is_withhold_electricity: 0,
                        billing_notes: null
                    },
                }
            },
            row: [],
            editorConfig: {
                toolbar: [ [ 'Bold','Italic','BulletedList','NumberedList' ] ]
            }
        }
    },
    methods: {
        onTenantEntry(){
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('tenant', false, 'tenants', 'tenantentry')
                }
                else{
                    this.$parent.updateEntityRef('tenant', 'tenant_id', false, this.row, 'tenantentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('tenant', 'showModalEntry', 'tenants', this.type, 'tenant_id', 'tenantentry')
            }
        },
        async setUpdate(data){
            this.row = data.item
            this.fillEntityForm('tenant', data.item.tenant_id, 'showEntry')
            this.entryMode='Edit'
        },
        clearDocuments(){
            this.forms.tenant.fields.business_permit = 0
            this.forms.tenant.fields.tenant_information_sheet = 0
            this.forms.tenant.fields.valid_id = 0
            this.forms.tenant.fields.tin_cor = 0
            this.forms.tenant.fields.dti_sec = 0
            this.forms.tenant.fields.notarized_contract = 0
            this.forms.tenant.fields.proof_of_billing = 0
            this.forms.tenant.fields.others = 0
        },
        setEntry(){
            this.clearFields('tenant')
            this.entryMode = 'Add'
            this.clearDocuments()
            this.showEntry = true
            this.forms.tenant.fields.vat_percent = 12
            this.forms.tenant.fields.is_vatted = 
            this.forms.tenant.fields.is_auto = 1
        }
    },
    watch: {
        showEntry: function (showEntry) {
            if(showEntry){
                let self = this
                Vue.nextTick(function(){
                    self.focusElement('trade_name')
                })
            }
        },
    },
}
</script>
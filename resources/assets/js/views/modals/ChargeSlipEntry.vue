<template>
    <div>
        <b-modal
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            size="lg"
            @shown="focusElement('tenant_id')">
            
            <div slot="modal-title"> <!-- modal title -->
                Charge Slip Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form autocomplete="off">
                    <b-row>
                        <b-col lg=6>
                            <b-form-group>
                                <label>Charge Slip No.</label>
                                <b-form-input placeholder="Auto Generated" v-model="forms.chargeslip.fields.charge_slip_no" readonly></b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="required" for="tenant_id">Tenant</label>
                                <select2
                                    ref="tenant_id"
                                    :allowClear="false"
                                    :placeholder="'Select Tenant'"
                                    v-model="forms.chargeslip.fields.tenant_id">
                                    <option v-for="tenant in options.tenants.items" :key="tenant.month_id" :value="tenant.tenant_id">{{tenant.trade_name}}</option>
                                </select2>
                            </b-form-group>
                            <b-form-group>
                                <label class="required">Charge Slip Type</label>
                                <select2
                                    ref="charge_slip_type"
                                    :allowClear="false"
                                    :placeholder="'Charge Slip Type'"
                                    v-model="forms.chargeslip.fields.charge_slip_type">
                                    <option value="1">Non-Compliance to House Rules</option>
                                    <option value="2">Overtime (OT) Payment</option>
                                    <option value="3">OTHERS</option>
                                </select2>
                            </b-form-group>
                            <b-form-group>
                                <label class="required" for="charge_id">Charge</label>
                                <select2
                                    ref="charge_id"
                                    :allowClear="false"
                                    :placeholder="'Select Charge'"
                                    v-model="forms.chargeslip.fields.charge_id">
                                    <option v-for="charge in options.charges.items" :key="charge.charge_id" :value="charge.charge_id">{{charge.charge_desc}}</option>
                                </select2>
                            </b-form-group>
                            <b-row>
                                <b-col lg=6>
                                    <b-form-group>
                                        <label class="required">Rate</label>
                                        <vue-autonumeric
                                            @input="forms.chargeslip.fields.charge_slip_amount = forms.chargeslip.fields.rate * forms.chargeslip.fields.reading"
                                            ref="rate"
                                            :class="'form-control text-right'" 
                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                            v-model="forms.chargeslip.fields.rate">
                                        </vue-autonumeric>
                                    </b-form-group>
                                </b-col>
                                <b-col lg=6>
                                    <b-form-group>
                                        <label class="required">Reading</label>
                                        <vue-autonumeric
                                            @input="forms.chargeslip.fields.charge_slip_amount = forms.chargeslip.fields.rate * forms.chargeslip.fields.reading"
                                            ref="reading"
                                            :class="'form-control text-right'" 
                                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                            v-model="forms.chargeslip.fields.reading">
                                        </vue-autonumeric>
                                    </b-form-group>
                                </b-col>
                            </b-row>
                            <b-form-group>
                                <label class="required">Amount </label>
                                <vue-autonumeric
                                    readonly
                                    ref="charge_slip_amount"
                                    :class="'form-control text-right'" 
                                    :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    v-model="forms.chargeslip.fields.charge_slip_amount">
                                </vue-autonumeric>
                            </b-form-group>
                        </b-col>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Datetime</label>
                                <date-picker 
                                    ref="charge_slip_datetime"
                                    v-model="forms.chargeslip.fields.charge_slip_datetime" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY hh:mm a"
                                    type="datetime"
                                    :clearable="false">
                                </date-picker>
                            </b-form-group>
                            <b-form-group>
                                <label>Ref/WP#</label>
                                <b-form-input 
                                    placeholder="Ref/WP#"
                                    type="text"
                                    v-model="forms.chargeslip.fields.charge_ref_wp_no"
                                    debounce="250">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label>Nature/Details</label>
                                <b-form-textarea 
                                    placeholder="Nature/Details"
                                    type="text"
                                    v-model="forms.chargeslip.fields.charge_slip_nature"
                                    debounce="250"
                                    rows="3"
                                ></b-form-textarea>
                            </b-form-group>
                            <b-row>
                                <b-col lg=6>
                                    <label class="required">Category</label>
                                    <b-form-radio-group
                                        v-model="forms.chargeslip.fields.charge_slip_category_type"
                                        stacked>
                                        <b-form-radio value=1>Suspension</b-form-radio>
                                        <b-form-radio value=2>Termination</b-form-radio>
                                        <b-form-radio value=3>Other<b-form-input :readonly="forms.chargeslip.fields.charge_slip_category_type!=3" ref="charge_slip_category_others_desc" placeholder="Other" type="text" v-model="forms.chargeslip.fields.charge_slip_category_others_desc"></b-form-input></b-form-radio>
                                    </b-form-radio-group>
                                </b-col>
                                <b-col lg=6>
                                    <label class="required">Gravity</label> 
                                    <b-form-radio-group
                                        v-model="forms.chargeslip.fields.charge_slip_gravity_type"
                                        stacked>
                                        <b-form-radio value=1>1<sup>st</sup> Offense</b-form-radio>
                                        <b-form-radio value=2>2<sup>nd</sup> Offense</b-form-radio>
                                        <b-form-radio value=3><sup>nth</sup> Offense<b-form-input :readonly="forms.chargeslip.fields.charge_slip_gravity_type!=3" ref="charge_slip_gravity_others_desc" placeholder="nth Offense" type="text" v-model="forms.chargeslip.fields.charge_slip_gravity_others_desc"></b-form-input></b-form-radio>
                                    </b-form-radio-group>
                                </b-col>
                            </b-row>
                        </b-col>
                    </b-row>
                </b-form>
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.chargeslip.isSaving" variant="success" @click="onChargeSlipEntry">
                    <icon v-if="forms.chargeslip.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>
            </div>
        </b-modal>
        <b-modal 
            v-model="showModalApprovalDisapproval"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Charge Slip - {{is_approval == true ? 'Approval' : 'Disapproval'}}
            </div>
            <b-col lg=12>
                Are you sure you want to {{is_approval == true ? 'approve' : 'disapprove'}} this charge slip?
            </b-col>
            <div slot="modal-footer">
                <b-button variant="success" @click="onApprovalDisapproval()">
                    <i class="fa fa-check"></i>
                    Accept
                </b-button>
                <b-button variant="danger" @click="showModalApprovalDisapproval=false">Close</b-button>            
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: 'chargeslipentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            showModalApprovalDisapproval: false,
            is_approval: false,
            options:{
                tenants: {
                    items: []
                },
                charges: {
                    items: []
                }
            },
            forms:{
                chargeslip: {
                    isSaving: false,
                    fields: {
                        charge_slip_no: null,
                        tenant_id: null,
                        charge_slip_type: null,
                        charge_id: null,
                        rate: 0,
                        reading: 0,
                        charge_slip_amount: 0,
                        app_year: 0,
                        month_id: 0,
                        charge_slip_datetime: new Date(),
                        charge_ref_wp_no: null,
                        charge_slip_nature: null,
                        charge_slip_category_type: 0,
                        charge_slip_category_others_desc: null,
                        charge_slip_gravity_type: 0,
                        charge_slip_gravity_others_desc: null
                    }
                }
            }
        }
    },
    created(){
        this.fillOptionsList('charges')
        this.fillOptionsList('tenants')
    },
    methods: {
        onChargeSlipEntry () {
            if(this.type == 'reference'){
                this.forms.chargeslip.fields.app_year = this.$parent.$refs.billingperiodfilter.forms.period.fields.app_year
                this.forms.chargeslip.fields.month_id = this.$parent.$refs.billingperiodfilter.forms.period.fields.month_id
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('chargeslip', true, 'chargeslips', 'chargeslipentry')
                }
                else{
                    this.$parent.updateEntityRef('chargeslip', 'charge_slip_id', true, this.row, 'chargeslipentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('chargeslip', 'showModalEntry', 'chargeslips', this.type, 'charge_slip_id', 'chargeslipentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('chargeslip', data.item.charge_slip_id)
            this.showModalEntry=true
            this.entryMode='Edit'
        },
        print(charge_slip_id){
            window.open('api/reports/chargeslip/'+charge_slip_id)
        },
        approvalDisapproval(data, is_approval){
            this.is_approval = is_approval
            this.showModalApprovalDisapproval = true
            this.row = data.item
        },
        onApprovalDisapproval(){
            var type = null
            var msg = ''
            if(this.is_approval){
                type = "approval"
                msg = "approved"
            }
            else{
                type = "disapproval"
                msg = "disapproved"
            }
            this.$http.get('api/chargeslip/'+ type +'/' + this.row.charge_slip_id, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    this.$notify({
                        type: 'success',
                        group: 'notification',
                        title: 'Success!',
                        text: 'The charge slip has been successfully '+ msg +'.'
                    })
                
                    for (var key in response.data.data) {
                        this.row[key] = response.data.data[key]
                    }
                    this.showModalApprovalDisapproval = false
                }).catch(error => {
                    console.log(error)
                })
        }
    }
}
</script>
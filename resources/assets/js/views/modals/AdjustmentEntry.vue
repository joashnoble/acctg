<template>
    <div>
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true">
            <!-- @shown="focusElement('department_code')" -->
            <div slot="modal-title">
                Adjustment Entry - {{entryMode}}
            </div>

            <b-col lg=12>
                <b-form @keydown="resetFieldStates('adjustment')" autocomplete="off">
                    <b-form-group>
                        <label>Adjustment No.</label>
                        <b-form-input 
                            v-model="forms.adjustment.fields.adjustment_no"
                            type="text"
                            placeholder="Auto"
                            readonly>
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required" for="tenant_id">Tenant</label>
                         <select2
                            ref="tenant_id"
                            :allowClear="false"
                            :placeholder="'Select Tenant'"
                            v-model="forms.adjustment.fields.tenant_id"
                        >
                            <option v-for="tenant in options.tenants.items" :key="tenant.tenant_id" :value="tenant.tenant_id">{{ tenant.trade_name }}</option>
                        </select2>
                    </b-form-group>
                    <b-form-group>
                        <label class="required" for="charge_id">Charges</label>
                         <select2
                            id="charge_id"
                            ref="charge_id"
                            :allowClear="false"
                            :placeholder="'Select Charge'"
                            v-model="forms.adjustment.fields.charge_id"
                        >
                            <option v-for="charge in options.charges.items" :key="charge.charge_id" :value="charge.charge_id">{{ charge.charge_desc }}</option>
                        </select2>
                    </b-form-group>
                    <b-row>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Rate</label>
                                <vue-autonumeric
                                    @input="forms.adjustment.fields.amount = forms.adjustment.fields.adjustment_rate * forms.adjustment.fields.adjustment_reading"
                                    ref="adjustment_rate"
                                    v-model="forms.adjustment.fields.adjustment_rate"
                                    :class="'form-control text-right'" 
                                    :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                </vue-autonumeric>
                            </b-form-group>
                        </b-col>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Reading</label>
                                <vue-autonumeric
                                    @input="forms.adjustment.fields.amount = forms.adjustment.fields.adjustment_rate * forms.adjustment.fields.adjustment_reading"
                                    ref="adjustment_reading"
                                    v-model="forms.adjustment.fields.adjustment_reading"
                                    :class="'form-control text-right'" 
                                    :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                </vue-autonumeric>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-form-group>
                        <label class="required">Amount </label>
                        <vue-autonumeric
                            readonly
                            ref="amount"
                            v-model="forms.adjustment.fields.amount"
                            :class="'form-control text-right'" 
                            :options="{minimumValue: 0, modifyValueOnWheel: false, emptyInputBehavior: 0}">
                        </vue-autonumeric>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Adjustment Type</label>
                        <b-form-radio-group 
                            ref="adjustment_type"
                            id="is_auto"
                            v-model="forms.adjustment.fields.adjustment_type">
                            <b-form-radio value="0">In</b-form-radio>
                            <b-form-radio value="1">Out</b-form-radio>
                        </b-form-radio-group>
                    </b-form-group>
                    <b-form-group>
                        <label>Notes</label>
                        <b-form-textarea
                            v-model="forms.adjustment.fields.notes"
                            debounce="250"
                            type="text"
                            :rows="3"
                            placeholder="Notes">
                        </b-form-textarea>
                    </b-form-group>
                </b-form>
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.adjustment.isSaving" variant="success" @click="onAdjustmentEntry">
                    <icon v-if="forms.adjustment.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: 'adjustmententry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false,
            options: {
                tenants: {
                    items: []
                },
                charges: {
                    items: []
                }
            },
            forms: {
                adjustment: {
                    isSaving: false,
                    fields: {
                        adjustment_id: null,
                        adjustment_no: null,
                        charge_id: null,
                        tenant_id: null,
                        period_id: null,
                        app_year: null,
                        month_id: null,
                        adjustment_type: null,
                        adjustment_rate: 0,
                        adjustment_reading: 0,
                        amount: 0,
                        notes: null
                    }
                },
            },
            row: []
        }
    },
    methods: {
        onAdjustmentEntry () {
            this.forms.adjustment.fields.period_id = this.$parent.$refs.billingperiodfilter.forms.period.fields.period_id
            this.forms.adjustment.fields.app_year = this.$parent.$refs.billingperiodfilter.forms.period.fields.app_year
            this.forms.adjustment.fields.month_id = this.$parent.$refs.billingperiodfilter.forms.period.fields.month_id

            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('adjustment', true, 'adjustments', 'adjustmententry')
                }
                else{
                    this.$parent.updateEntityRef('adjustment', 'adjustment_id', true, this.row, 'adjustmententry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('adjustment', 'showModalEntry', 'adjustments', this.type, 'adjustment_id', 'adjustmententry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('adjustment', data.item.adjustment_id)
            this.showModalEntry=true
            this.entryMode='Edit'
        },
    },
    created() {
        this.fillOptionsList('tenants')
        this.fillOptionsList('charges')
    }
}
</script>
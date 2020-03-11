<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('charge_code')"
        >
        
            <div slot="modal-title"> <!-- modal title -->
                Charge Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('charge')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="charge_code">Charge Code</label>
                        <b-form-input
                            ref="charge_code"
                            id="charge_code"
                            v-model="forms.charge.fields.charge_code"
                            debounce="250"
                            type="text"
                            placeholder="Charge Code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Charge Desc</label>
                        <b-form-input
                            ref="charge_desc"
                            id="charge_desc"
                            v-model="forms.charge.fields.charge_desc"
                            debounce="250"
                            type="text"
                            placeholder="Charge Description">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Account Title</label>
                        <select2
                            ref="account_id"
                            id="account_id"
                            :allowClear="false"
                            :placeholder="'Select Account Title'"
                            v-model="forms.charge.fields.account_id"
                        >
                            <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                        </select2>
                    </b-form-group>
                    <b-row>
                        <b-col lg=3>
                            <b-form-group>
                                <label>Sort</label>
                                <vue-autonumeric
                                    ref="sort"
                                    v-model="forms.charge.fields.sort"
                                    :class="'form-control text-right'" 
                                    :options="{minimumValue: 0, decimalPlaces: 0, emptyInputBehavior: 0}">
                                </vue-autonumeric>
                            </b-form-group>
                            <p>{{forms.charge.fields.sort}}</p>
                        </b-col>
                    </b-row>
                </b-form>
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.charge.isSaving" variant="success" @click="onChargeEntry">
                    <icon v-if="forms.charge.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>
            </div> <!-- modal footer buttons -->

        </b-modal>
    </div> <!-- modal div -->
</template>
<script>
export default {
    name: 'chargeentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options: {
                accounts: {
                    items: []
                },
            },
            forms:{
                charge : {
                    isSaving: false,
                    fields: {
                        charge_id: null,
                        charge_code: null,
                        charge_desc: null,
                        account_id: 0,
                        sort: 0
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onChargeEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('charge', true, 'charges', 'chargeentry')
                }
                else{
                    this.$parent.updateEntityRef('charge', 'charge_id', true, this.row, 'chargeentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('charge', 'showModalEntry', 'charges', this.type, 'charge_id', 'chargeentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.resetFieldStates('charge')
            this.fillEntityForm('charge', data.item.charge_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
    created(){
        this.fillOptionsList('accounts')
    }
}
</script>
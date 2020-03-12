<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('bank_code')"
    >
        <div slot="modal-title">
            Bank  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="bank_name">Bank Code</label>
                    <b-form-input
                        ref="bank_code"
                        id="bank_code"
                        v-model="forms.bank.fields.bank_code"
                        debounce="250"
                        type="text"
                        placeholder="Code">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="bank_name">Bank Name</label>
                    <b-form-input
                        ref="bank_name"
                        id="bank_name"
                        v-model="forms.bank.fields.bank_name"
                        debounce="250"
                        type="text"
                        placeholder="Name">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="bank_name">Account Number</label>
                    <b-form-input
                        ref="account_number"
                        id="account_number"
                        v-model="forms.bank.fields.account_number"
                        debounce="250"
                        type="text"
                        placeholder="Account Number">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="account_type">Brand Name</label>
                        <select2
                            ref="account_type"
                            :allowClear="false"
                            :placeholder="'Select Account Type'"
                            v-model="forms.bank.fields.account_type">
                            <option value="1">Current Account</option>
                            <option value="2">Savings Account</option>
                        </select2>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.bank.isSaving" variant="success" @click="onBankEntry">
                  <b-spinner v-if="forms.bank.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'bankentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                bank : {
                    isSaving: false,
                    fields: {
                        bank_id: null,
                        bank_code: null,
                        bank_name: null,
                        account_number : null,
                        account_type: null,
                        account_type_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onBankEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('bank', true, 'banks', 'bankentry')
                }
                else{
                    this.$parent.updateEntityRef('bank', 'bank_id', true, this.row, 'bankentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('bank', data.item.bank_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
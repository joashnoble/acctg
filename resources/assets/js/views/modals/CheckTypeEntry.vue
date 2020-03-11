<template>
    <div>
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('check_type_code')">
            <div slot="modal-title">
                Check Type Entry - {{entryMode}}
            </div>
            <b-col lg=12>
                <b-form @keydown="resetFieldStates('checktype')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="check_type_code">Check Type Code</label>
                        <b-form-input
                            ref="check_type_code"
                            id="check_type_code"
                            v-model="forms.checktype.fields.check_type_code"
                            debounce="250"
                            type="text"
                            placeholder="Check Type Code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Check Type Desc</label>
                        <b-form-input
                            ref="check_type_desc"
                            id="check_type_desc"
                            v-model="forms.checktype.fields.check_type_desc"
                            debounce="250"
                            type="text"
                            placeholder="Check Type Description">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Account Title</label>
                        <select2
                            ref="account_id"
                            id="account_id"
                            :allowClear="false"
                            :placeholder="'Select Account Title'"
                            v-model="forms.checktype.fields.account_id"
                        >
                            <option v-for="account in options.accounts.items" :key="account.account_id" :value="account.account_id">{{account.account_title}}</option>
                        </select2>
                    </b-form-group>
                </b-form>
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.checktype.isSaving" variant="success" @click="onCheckTypeEntry">
                    <icon v-if="forms.checktype.isSaving" name="sync" spin></icon>
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
    name: 'checktypeentry',
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
            forms: {
                checktype : {
                    isSaving: false,
                    fields: {
                        check_type_id: null,
                        check_type_code: null,
                        check_type_desc: null,
                        account_id: 0,
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onCheckTypeEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('checktype', true, 'checktypes', 'checktypeentry')
                }
                else{
                    this.$parent.updateEntityRef('checktype', 'check_type_id', true, this.row, 'checktypeentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('checktype', 'showModalEntry', 'checktypes', this.type, 'check_type_id', 'checktypeentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('checktype', data.item.check_type_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
    created() {
        this.fillOptionsList('accounts')
    }
}
</script>
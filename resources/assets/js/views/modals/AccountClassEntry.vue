<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('account_class')"
    >
        <div slot="modal-title">
            Account Classification  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="account_class">Account Classification Name</label>
                    <b-form-input
                        ref="account_class"
                        id="account_class"
                        v-model="forms.accountclass.fields.account_class"
                        debounce="250"
                        type="text"
                        placeholder="Name">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label> Description</label>
                    <b-form-input
                        ref="description"
                        id="description"
                        v-model="forms.accountclass.fields.description"
                        debounce="250"
                        type="text"
                        placeholder="Description">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="account_type_id">Account Type</label>
                        <select2
                            ref="account_type_id"
                            :allowClear="false"
                            :placeholder="'Select Account Type'"
                            v-model="forms.accountclass.fields.account_type_id">
                        <option v-for="accounttype in options.accounttypes.items" :key="accounttype.account_type_id" :value="accounttype.account_type_id">{{accounttype.account_type}}</option>
                        </select2>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.accountclass.isSaving" variant="success" @click="onAccountClassEntry">
                  <b-spinner v-if="forms.accountclass.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'accountclassentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options:{
                accounttypes:{
                    items:[]
                }
            },
            forms: {
                accountclass : {
                    isSaving: false,
                    fields: {
                        account_class_id: null,
                        account_class: null,
                        descriptioni: null,
                        account_type_id: null
                    }
                }
            },
            row: []
        }
    },
    created(){
        this.fillOptionsList('accounttypes')
    },
    methods:{
        onAccountClassEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('accountclass', true, 'accountclasses', 'accountclassentry')
                }
                else{
                    this.$parent.updateEntityRef('accountclass', 'account_class_id', true, this.row, 'accountclassentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('accountclass', data.item.account_class_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
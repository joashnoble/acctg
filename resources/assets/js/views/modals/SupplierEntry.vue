<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('supplier_name')"
    >
        <div slot="modal-title">
            Supplier  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="supplier_name">Name</label>
                    <b-form-input
                        ref="supplier_name"
                        id="supplier_name"
                        v-model="forms.supplier.fields.supplier_name"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="contact_name">Contact Person</label>
                    <b-form-input
                        ref="contact_name"
                        id="contact_name"
                        v-model="forms.supplier.fields.contact_name"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="address">Address</label>
                    <b-form-input
                        ref="address"
                        id="address"
                        v-model="forms.supplier.fields.address"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label for="email_address">Email Address</label>
                    <b-form-input
                        ref="email_address"
                        id="email_address"
                        v-model="forms.supplier.fields.email_address"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label  for="contact_no">Contact Number</label>
                    <b-form-input
                        ref="contact_no"
                        id="contact_no"
                        v-model="forms.supplier.fields.contact_no"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label for="tin_no">Tax Identification Number</label>
                    <b-form-input
                        ref="tin_no"
                        id="tin_no"
                        v-model="forms.supplier.fields.tin_no"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label for="tax_output">Tax Output Percentage</label>
                    <b-form-input
                        ref="tax_output"
                        id="tax_output"
                        v-model="forms.supplier.fields.tax_output"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required">Tax Type</label>
                    <select2
                        ref="tax_type_id"
                        :allowClear="false"
                        :placeholder="'Select Tax Type'"
                        v-model="forms.supplier.fields.tax_type_id"
                    >
                    <option v-for="taxtype in options.taxtypes.items" :key="taxtype.tax_type_id" :value="taxtype.tax_type_id">{{taxtype.tax_type}}</option>
                    </select2>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.supplier.isSaving" variant="success" @click="onSupplierEntry">
                  <b-spinner v-if="forms.supplier.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'supplierentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options:{
                taxtypes:{
                    items:[]
                }
            },
            forms: {
                supplier : {
                    isSaving: false,
                    fields: {
                        supplier_id:null,
                        supplier_name:null,
                        contact_name:null,
                        address:null,
                        email_address:null,
                        contact_no:null,
                        tin_no:null,
                        photo_path:null,
                        tax_type_id:null,
                        tax_output:null,
                    }
                }
            },
            row: []
        }
    },
    created(){
        this.fillOptionsList('taxtypes');
    },
    methods:{
        onSupplierEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('supplier', true, 'suppliers', 'supplierentry')
                }
                else{
                    this.$parent.updateEntityRef('supplier', 'supplier_id', true, this.row, 'supplierentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('supplier', 'showModalEntry', 'suppliers', this.type, 'supplier_id', 'supplierentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('supplier', data.item.supplier_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
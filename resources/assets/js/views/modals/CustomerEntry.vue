<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('customer_name')"
    >
        <div slot="modal-title">
            Customers  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="customer_name">Name</label>
                    <b-form-input
                        ref="customer_name"
                        id="customer_name"
                        v-model="forms.customer.fields.customer_name"
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
                        v-model="forms.customer.fields.contact_name"
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
                        v-model="forms.customer.fields.address"
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
                        v-model="forms.customer.fields.email_address"
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
                        v-model="forms.customer.fields.contact_no"
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
                        v-model="forms.customer.fields.tin_no"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label>Customer Type</label>
                    <select2
                        ref="customer_type_id"
                        :allowClear="false"
                        :placeholder="'Select Customer Type'"
                        v-model="forms.customer.fields.customer_type_id"
                    >
                    <option value="0">None</option>
                    <option v-for="customertype in options.customertypes.items" :key="customertype.customer_type_id" :value="customertype.customer_type_id">{{customertype.customer_type_name}}</option>
                    </select2>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.customer.isSaving" variant="success" @click="onCustomerEntry">
                  <b-spinner v-if="forms.customer.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'customerentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options:{
                customertypes:{
                    items:[]
                }
            },
            forms: {
                customer : {
                    isSaving: false,
                    fields: {
                        customer_id:null,
                        customer_name:null,
                        contact_name:null,
                        address:null,
                        email_address:null,
                        contact_no:null,
                        tin_no:null,
                        photo_path:null,
                        customer_type_id:null,
                    }
                }
            },
            row: []
        }
    },
    created(){
        this.fillOptionsList('customertypes');
    },
    methods:{
        onCustomerEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('customer', true, 'customers', 'customerentry')
                }
                else{
                    this.$parent.updateEntityRef('customer', 'customer_id', true, this.row, 'customerentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('customer', 'showModalEntry', 'customers', this.type, 'customer_id', 'customerentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('customer', data.item.customer_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
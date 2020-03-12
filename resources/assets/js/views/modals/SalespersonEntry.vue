<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('salesperson_code')"
    >
        <div slot="modal-title">
            Salesperson  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="salesperson_code">Code</label>
                    <b-form-input
                        ref="salesperson_code"
                        id="salesperson_code"
                        v-model="forms.salesperson.fields.salesperson_code"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="firstname">First Name</label>
                    <b-form-input
                        ref="firstname"
                        id="firstname"
                        v-model="forms.salesperson.fields.firstname"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label  for="middlename">Middle Name</label>
                    <b-form-input
                        ref="middlename"
                        id="middlename"
                        v-model="forms.salesperson.fields.middlename"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required" for="lastname">Last Name</label>
                    <b-form-input
                        ref="lastname"
                        id="lastname"
                        v-model="forms.salesperson.fields.lastname"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label for="contact_no">Contact Number</label>
                    <b-form-input
                        ref="contact_no"
                        id="contact_no"
                        v-model="forms.salesperson.fields.contact_no"
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
                        v-model="forms.salesperson.fields.tin_no"
                        debounce="250"
                        type="text"
                        placeholder="">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required">Department</label>
                    <select2
                        ref="department_id"
                        :allowClear="false"
                        :placeholder="'Select Department'"
                        v-model="forms.salesperson.fields.department_id"
                    >
                    <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_name}}</option>
                    </select2>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.salesperson.isSaving" variant="success" @click="onSalespersonEntry">
                  <b-spinner v-if="forms.salesperson.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'salespersonentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options:{
                departments:{
                    items:[]
                }
            },
            forms: {
                salesperson : {
                    isSaving: false,
                    fields: {
                        salesperson_id: null,
                        salesperson_code: null,
                        firstname: null,
                        middlename: null,
                        lastname: null,
                        contact_no: null,
                        department_id: null,
                        tin_no: null,
                    }
                }
            },
            row: []
        }
    },
    created(){
        this.fillOptionsList('departments');
    },
    methods:{
        onSalespersonEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('salesperson', true, 'salespersons', 'salespersonentry')
                }
                else{
                    this.$parent.updateEntityRef('salesperson', 'salesperson_id', true, this.row, 'salespersonentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('salesperson', 'showModalEntry', 'salespersons', this.type, 'salesperson_id', 'salespersonentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('salesperson', data.item.salesperson_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
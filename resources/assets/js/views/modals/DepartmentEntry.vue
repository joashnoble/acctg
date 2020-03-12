<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('department_name')"
    >
        <div slot="modal-title">
            Department  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="department_name">Department Name</label>
                    <b-form-input
                        ref="department_name"
                        id="department_name"
                        v-model="forms.department.fields.department_name"
                        debounce="250"
                        type="text"
                        placeholder="Department Name">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label>Department Description</label>
                    <b-form-input
                        ref="department_desc"
                        id="department_desc"
                        v-model="forms.department.fields.department_desc"
                        debounce="250"
                        type="text"
                        placeholder="Description">
                    </b-form-input>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.department.isSaving" variant="success" @click="onDepartmentEntry">
                  <b-spinner v-if="forms.department.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'departmententry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                department : {
                    isSaving: false,
                    fields: {
                        department_id: null,
                        department_name: null,
                        department_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onDepartmentEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('department', true, 'departments', 'departmententry')
                }
                else{
                    this.$parent.updateEntityRef('department', 'department_id', true, this.row, 'departmententry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('department', data.item.department_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
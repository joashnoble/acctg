<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('unit_name')"
    >
        <div slot="modal-title">
            Unit of Measurement  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="unit_name">Unit Name</label>
                    <b-form-input
                        ref="unit_name"
                        id="unit_name"
                        v-model="forms.unit.fields.unit_name"
                        debounce="250"
                        type="text"
                        placeholder="Name">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label >Description</label>
                    <b-form-input
                        ref="unit_desc"
                        id="unit_desc"
                        v-model="forms.unit.fields.unit_desc"
                        debounce="250"
                        type="text"
                        placeholder="Description">
                    </b-form-input>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.unit.isSaving" variant="success" @click="onUnitEntry">
                  <b-spinner v-if="forms.unit.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'unitentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                unit : {
                    isSaving: false,
                    fields: {
                        unit_id: null,
                        unit_name: null,
                        unit_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onUnitEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('unit', true, 'units', 'unitentry')
                }
                else{
                    this.$parent.updateEntityRef('unit', 'unit_id', true, this.row, 'unitentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('unit', data.item.unit_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
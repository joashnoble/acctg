<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('location_name')"
    >
        <div slot="modal-title">
            Location  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="location_name">Location Name</label>
                    <b-form-input
                        ref="location_name"
                        id="location_name"
                        v-model="forms.location.fields.location_name"
                        debounce="250"
                        type="text"
                        placeholder="Location Name">
                    </b-form-input>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.location.isSaving" variant="success" @click="onLocationEntry">
                  <b-spinner v-if="forms.location.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'locationentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                location : {
                    isSaving: false,
                    fields: {
                        location_id: null,
                        location_name: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onLocationEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('location', true, 'locations', 'locationentry')
                }
                else{
                    this.$parent.updateEntityRef('location', 'location_id', true, this.row, 'locationentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('location', data.item.location_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
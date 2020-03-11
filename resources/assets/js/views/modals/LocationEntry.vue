<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('location_code')"
        >
        
            <div slot="modal-title"> <!-- modal title -->
                Location Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('location')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="location_code">Location Code</label>
                        <b-form-input
                            ref="location_code"
                            id="location_code"
                            v-model="forms.location.fields.location_code"
                            debounce="250"
                            type="text"
                            placeholder="Location Code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Location Desc</label>
                        <b-form-input
                            ref="location_desc"
                            id="location_desc"
                            v-model="forms.location.fields.location_desc"
                            debounce="250"
                            type="text"
                            placeholder="Location Description">
                        </b-form-input>
                    </b-form-group>
                </b-form>
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.location.isSaving" variant="success" @click="onLocationEntry">
                    <icon v-if="forms.location.isSaving" name="sync" spin></icon>
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
    name: 'locationentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms:{
                location : {
                    isSaving: false,
                    fields: {
                        location_id: null,
                        location_code: null,
                        location_desc: null
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
                this.$parent.createOptionsEntityRef('location', 'showModalEntry', 'locations', this.type, 'location_id', 'locationentry')
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
<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('nature_of_business_code')">
            <div slot="modal-title"> <!-- modal title -->
                Nature Of Business Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('natureofbusiness')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="nature_of_business_code">Nature Of Business Code</label>
                        <b-form-input
                            ref="nature_of_business_code"
                            id="nature_of_business_code"
                            v-model="forms.natureofbusiness.fields.nature_of_business_code"
                            debounce="250"
                            type="text"
                            placeholder="Nature Of Business Code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Nature Of Business Desc</label>
                        <b-form-input
                            ref="nature_of_business_desc"
                            id="nature_of_business_desc"
                            v-model="forms.natureofbusiness.fields.nature_of_business_desc"
                            debounce="250"
                            type="text"
                            placeholder="Nature Of Business Description">
                        </b-form-input>
                    </b-form-group>
                </b-form>
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.natureofbusiness.isSaving" variant="success" @click="onNatureOfBusinessEntry">
                    <icon v-if="forms.natureofbusiness.isSaving" name="sync" spin></icon>
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
    name: 'natureofbusinessentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms:{
                natureofbusiness : {
                    isSaving: false,
                    fields: {
                        nature_of_business_id: null,
                        nature_of_business_code: null,
                        nature_of_business_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onNatureOfBusinessEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('natureofbusiness', true, 'natureofbusinesses', 'natureofbusinessentry')
                }
                else{
                    this.$parent.updateEntityRef('natureofbusiness', 'nature_of_business_id', true, this.row, 'natureofbusinessentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('natureofbusiness', 'showModalEntry', 'natureofbusinesses', this.type, 'nature_of_business_id', 'natureofbusinessentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('natureofbusiness', data.item.nature_of_business_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
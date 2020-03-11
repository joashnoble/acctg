<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('contract_type_code')">

            <div slot="modal-title"> <!-- modal title -->
                Contract Type Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('contracttype')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="contract_type_code">Contract Type Code</label>
                        <b-form-input
                            ref="contract_type_code"
                            id="contract_type_code"
                            v-model="forms.contracttype.fields.contract_type_code"
                            debounce="250"
                            type="text"
                            placeholder="Contract Type Code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Contract Type Desc</label>
                        <b-form-input
                            ref="contract_type_desc"
                            id="contract_type_desc"
                            v-model="forms.contracttype.fields.contract_type_desc"
                            debounce="250"
                            type="text"
                            placeholder="Contract Type Description">
                        </b-form-input>
                    </b-form-group>
                </b-form>
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.contracttype.isSaving" variant="success" @click="onContractTypeEntry">
                    <icon v-if="forms.contracttype.isSaving" name="sync" spin></icon>
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
    name: 'contracttypeentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms:{
                contracttype : {
                    isSaving: false,
                    fields: {
                        contract_type_id: null,
                        contract_type_code: null,
                        contract_type_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onContractTypeEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('contracttype', true, 'contracttypes', 'contracttypeentry')
                }
                else{
                    this.$parent.updateEntityRef('contracttype', 'contract_type_id', true, this.row, 'contracttypeentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('contracttype', 'showModalEntry', 'contracttypes', this.type, 'contract_type_id', 'contracttypeentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('contracttype', data.item.contract_type_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
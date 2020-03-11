<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('category_code')"
    >
        <div slot="modal-title">
            Category Entry - {{entryMode}}
        </div>
        <b-col lg=12>
            <b-form @keydown="resetFieldStates('category')" autocomplete="off">
                <b-form-group>
                    <label class="required" for="category_code">Category Code</label>
                    <b-form-input
                        ref="category_code"
                        id="category_code"
                        v-model="forms.category.fields.category_code"
                        debounce="250"
                        type="text"
                        placeholder="Category Code">
                    </b-form-input>
                </b-form-group>
                <b-form-group>
                    <label class="required">Category Desc</label>
                    <b-form-input
                        ref="category_desc"
                        id="category_desc"
                        v-model="forms.category.fields.category_desc"
                        debounce="250"
                        type="text"
                        placeholder="Category Description">
                    </b-form-input>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.category.isSaving" variant="success" @click="onCategoryEntry">
                <icon v-if="forms.category.isSaving" name="sync" spin></icon>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'categoryentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                category : {
                    isSaving: false,
                    fields: {
                        category_id: null,
                        category_code: null,
                        category_desc: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onCategoryEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('category', true, 'categories', 'categoryentry')
                }
                else{
                    this.$parent.updateEntityRef('category', 'category_id', true, this.row, 'categoryentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('category', data.item.category_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
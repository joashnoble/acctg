<template>
    <b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('brand_name')"
    >
        <div slot="modal-title">
            Brand  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-form-group>
                    <label class="required" for="brand_name">Brand Name</label>
                    <b-form-input
                        ref="brand_name"
                        id="brand_name"
                        v-model="forms.brand.fields.brand_name"
                        debounce="250"
                        type="text"
                        placeholder="Name">
                    </b-form-input>
                </b-form-group>
            </b-form>
        </b-col>
        <div slot="modal-footer">
            <b-button :disabled="forms.brand.isSaving" variant="success" @click="onBrandEntry">
                  <b-spinner v-if="forms.brand.isSaving" small variant="light" label="Spinning"></b-spinner>
                <i class="fa fa-check"></i>
                Save
            </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>
</template>
<script>
export default {
    name: 'brandentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            forms: {
                brand : {
                    isSaving: false,
                    fields: {
                        brand_id: null,
                        brand_name: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onBrandEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('brand', true, 'brands', 'brandentry')
                }
                else{
                    this.$parent.updateEntityRef('brand', 'brand_id', true, this.row, 'brandentry')
                }
            }
            else{
                alert('NOT IN USE');
                // this.$parent.createOptionsEntityRef('category', 'showModalEntry', 'categories', this.type, 'category_id', 'categoryentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('brand', data.item.brand_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
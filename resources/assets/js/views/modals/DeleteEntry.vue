<template>
    <div>
        <b-modal 
            v-model="showModalDelete"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
                Delete {{capitalize(entity)}}
            </div>
            <b-col lg=12>
                Are you sure you want to delete this {{entity.toLowerCase()}}?
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="isSaving" variant="success" @click="onDeleteEntry">
                    <icon v-if="isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Accept
                </b-button>
                <b-button variant="danger" @click="showModalDelete=false">Close</b-button>            
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: 'deleteentry',
    props: ['entity','primary_key','table'],
    data() {
        return {
            showModalDelete: false,
            isSaving: false,
            ref_id: null
        }
    },
    methods: {
        onDeleteEntry(){
            this.$parent.deleteEntityRef(this.entity.split(" ").join("").toLowerCase(), this.ref_id, true, this.table, this.primary_key, 'deleteentry')
        },
        async setDelete(id){
            if(await this.checkIfUsed(this.entity.split(" ").join("").toLowerCase(), id) == true){
                this.$notify({
                    type: 'error',
                    group: 'notification',
                    title: 'Error!',
                    text: "Unable to delete, this record is being used by other transactions."
                })
                return
            }
            this.ref_id = id
            this.showModalDelete = true
        },
    }
}
</script>
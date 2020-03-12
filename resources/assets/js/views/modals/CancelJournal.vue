<template>
    <div>
        <b-modal 
            v-model="showModalCancel"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
        >
            <div slot="modal-title">
               Journal Entry {{type}}lation
            </div>
            <b-col lg=12>
                Are you sure you want to {{type}} Journal {{transaction_no}}?
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="isSaving" variant="success" @click="onDeleteEntry">
                    <b-spinner v-if="isSaving" small variant="light" label="Spinning"></b-spinner>
                    <i class="fa fa-check"></i>
                    Accept
                </b-button>
                <b-button variant="danger" @click="showModalCancel=false">Close</b-button>            
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: 'canceljournal',
    props: ['entity','primary_key','table'],
    data() {
        return {
            showModalCancel: false,
            isSaving: false,
            ref_id: null,
            transaction_no: null,
            type: '',
            row:[]
        }
    },
    methods: {
        onDeleteEntry(){
            this.$parent.CancelJournalEntry('generaljournal', 'journal_id', true, this.row, 'canceljournal')
        },
        async setCancel(row){
            if(row.is_active == 1){ this.type = 'Cancel' }else{ this.type = 'Uncancel'}
            if(row.accounting_period_id > 0){
                    this.$parent.$notify({ type: 'error', group: 'notification', title: 'Error!', text: 'Sorry, you cannot '+this.type+' journal that is already closed!.' }) 
            }else{
                this.row = row
                this.ref_id = row.id
                this.transaction_no = row.txn_no
                this.showModalCancel = true
            }
        },
    }
}
</script>
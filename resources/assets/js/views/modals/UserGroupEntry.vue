<template>
    <div>
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true">
            <div slot="modal-title">
                User Group Entry - {{entryMode}}
            </div>
            <b-col lg=12>
                <b-form @keydown="resetFieldStates('usergroup')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="user_group">User Group</label>
                        <b-form-input
                            ref="user_group"
                            id="user_group"
                            v-model="forms.usergroup.fields.user_group"
                            debounce="250"
                            type="text"
                            placeholder="User Group"
                            error="asdf"
                            required>
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label>Description</label>
                        <b-form-textarea
                            ref="user_group_desc"
                            id="user_group_desc"
                            v-model="forms.usergroup.fields.user_group_desc"
                            debounce="250"
                            type="text"
                            placeholder="Description">
                        </b-form-textarea>
                    </b-form-group>
                </b-form>
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.usergroup.isSaving" variant="success" @click="onUserGroupEntry">
                    <icon v-if="forms.usergroup.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: "usergroupentry",
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            showModalDelete: false,
            forms: {
                usergroup : {
                    isSaving: false,
                    fields: {
                        user_group_id: null,
                        user_group: null,
                        user_group_desc: null,
                    },
                }
            },
            row: []
        }
    },
    methods:{
        onUserGroupEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('usergroup', true, 'usergroups', 'usergroupentry')
                }
                else{
                    this.$parent.updateEntityRef('usergroup', 'user_group_id', true, this.row, 'usergroupentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('usergroup', 'showModalEntry', 'usergroups', this.type, 'id', 'usergroupentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('usergroup', data.item.user_group_id,'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
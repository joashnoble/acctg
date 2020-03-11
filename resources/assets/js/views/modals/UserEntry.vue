<template>
    <div>
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('username')">
            <div slot="modal-title">
                User Entry - {{entryMode}}
            </div>
            <b-col lg=12>
                <b-form @keydown="resetFieldStates('user')" autocomplete="off">
                    <b-row>
                        <b-col sm="6">
                            <b-form-group>
                                <label class="required" for="user_code">Username</label>
                                <b-form-input
                                    ref="username"
                                    id="username"
                                    v-model="forms.user.fields.username"
                                    debounce="250"
                                    type="text"
                                    placeholder="Username">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="required">Password</label>
                                <b-form-input
                                    ref="password"
                                    id="password"
                                    v-model="forms.user.fields.password"
                                    debounce="250"
                                    type="password"
                                    placeholder="Password">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="required">Confirm Password</label>
                                <b-form-input
                                    ref="confirm_password"
                                    id="confirm_password"
                                    v-model="forms.user.fields.password_confirmation"
                                    debounce="250"
                                    type="password"
                                    placeholder="Confirm Password">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="required">User Group</label>
                                <select2
                                    ref="user_group_id"
                                    :allowClear="false"
                                    :placeholder="'Select User Group'"
                                    v-model="forms.user.fields.user_group_id">
                                    <option v-for="ug in options.usergroups.items" :key="ug.user_group_id" :value="ug.user_group_id">{{ug.user_group}}</option>
                                </select2>
                            </b-form-group>
                        </b-col>
                        <b-col sm="6">
                            <b-form-group>
                                <label class="required">Email</label>
                                <b-form-input
                                    ref="email"
                                    id="email"
                                    v-model="forms.user.fields.email"
                                    debounce="250"
                                    type="text"
                                    placeholder="Email">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label>Firstname</label>
                                <b-form-input
                                    id="firstname"
                                    v-model="forms.user.fields.firstname"
                                    debounce="250"
                                    type="text"
                                    placeholder="Firstname">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label>Middlename</label>
                                <b-form-input
                                    id="middlename"
                                    v-model="forms.user.fields.middlename"
                                    debounce="250"
                                    type="text"
                                    placeholder="Middlename">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label>Lastname</label>
                                <b-form-input
                                    id="lastname"
                                    v-model="forms.user.fields.lastname"
                                    debounce="250"
                                    type="text"
                                    placeholder="Lastname">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                </b-form>
            </b-col>
            <div slot="modal-footer">
                <b-button :disabled="forms.user.isSaving" variant="success" @click="onUserEntry">
                    <icon v-if="forms.user.isSaving" name="sync" spin></icon>
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
    name: 'userentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options: {
                usergroups: {
                    items: []
                }
            },
            forms: {
                user : {
                    isSaving: false,
                    fields: {
                        user_id: null,
                        username: null,
                        firstname: null,
                        middlename: null,
                        lastname: null,
                        user_group_id: null,
                        email: null,
                        password: null,
                        password_confirmation: null,
                    }
                },
                row: []
            },
        }
    },
    methods:{
        onUserEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('user', true, 'users', 'userentry')
                }
                else{
                    this.$parent.updateEntityRef('user', 'id', true, this.row, 'userentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('user', 'showModalEntry', 'users', this.type, 'id', 'userentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('user', data.item.id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
    created() {
        this.fillOptionsList('usergroups')
    }
}
</script>
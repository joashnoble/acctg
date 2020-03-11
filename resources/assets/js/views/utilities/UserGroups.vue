<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <notifications group="notification" />
        <div class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                User Group List
                                <small class="font-italic">List of all registered usergroup groups.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button v-if="checkRights('11-42')" variant="success" @click="$refs.usergroupentry.showModalEntry = true, $refs.usergroupentry.entryMode='Add', $refs.usergroupentry.clearFields('usergroup')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New User Group
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.usergroups.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col sm="12">
                                <b-table 
                                    v-if="checkAction"
                                    responsive
                                    :filter="filters.usergroups.criteria"
                                    :fields="tables.usergroups.fields"
                                    :items.sync="tables.usergroups.items"
                                    :current-page="paginations.usergroups.currentPage"
                                    :per-page="paginations.usergroups.perPage"
                                    @filtered="onFiltered($event,'usergroups')"
                                    striped small show-empty
                                >
                                    <template v-slot:cell(row_data)="row">
                                        <!-- <b-btn :size="'sm'" variant="success" @click="row.toggleDetails(), getRights(row)"> -->
                                            <i @click.stop="row.toggleDetails(), getRights(row)" :class="row.detailsShowing ? 'fa fa-minus fa-lg text-danger' : 'fa fa-plus fa-lg text-success'"></i>
                                        <!-- </b-btn> -->
                                    </template>
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('11-43')" :size="'sm'" variant="primary" @click="$refs.usergroupentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('11-44')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.user_group_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    <template v-slot:row-details="row">
                                        <b-form @submit.prevent="processRights(row.item.user_group_id)">
                                            <b-list-group>
                                                <b-list-group-item v-for="module_group in modules['module_groups']" :key="module_group.module_group">
                                                    <h4>{{module_group.module_group}}</h4>
                                                    <b-list-group class="mt-2 pl-5 pr-5">
                                                        <b-list-group-item v-for="module in filterModules(module_group.module_group)" :key="module.module_id">
                                                            <h5 v-b-toggle="module.module_id + '-' + row.item.user_group_id">{{module.module_name}}</h5>
                                                            <b-collapse :id="module.module_id + '-' + row.item.user_group_id" accordion="my-accordion" role="tabpanel">
                                                                <b-list-group class="mt-2">
                                                                    <b-list-group-item v-for="right in filterGroupRights(module.module_id)" :key="right.module_right_id">
                                                                        <h6>{{right.right_desc}}
                                                                            <span class="float-right">
                                                                                <!-- <c-switch 
                                                                                    type="text" 
                                                                                    variant="primary" 
                                                                                    on="On" 
                                                                                    off="Off"
                                                                                    :pill="true"
                                                                                    :ref="row.item.user_group_id + '-' + module.module_id + '-' + right.module_right_id" 
                                                                                    :checked="right.rights == 0 ? false : true"
                                                                                /> -->
                                                                                <b-form-radio-group 
                                                                                    buttons
                                                                                    :button-variant="right.rights == 1 ?'outline-primary' : 'outline-danger'"
                                                                                    v-model="right.rights"
                                                                                    :options="[
                                                                                        { text: 'On', value: '1' },
                                                                                        { text: 'Off', value: '0' }
                                                                                    ]"
                                                                                />
                                                                                <!-- <b-form-checkbox
                                                                                    v-model="right.rights"
                                                                                    value=1
                                                                                    unchecked-value=0>
                                                                                </b-form-checkbox> -->
                                                                            </span>
                                                                        </h6>
                                                                    </b-list-group-item>
                                                                </b-list-group>
                                                            </b-collapse>
                                                        </b-list-group-item>
                                                    </b-list-group>
                                                </b-list-group-item>
                                            </b-list-group>
                                            <b-button class="mt-2 mr-2 float-right" variant="primary" type="submit">Save</b-button>
                                        </b-form>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.usergroups.totalRows" :per-page="paginations.usergroups.perPage" v-model="paginations.usergroups.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <usergroupentry type="reference" ref="usergroupentry"></usergroupentry>
        <deleteentry entity="User Group" table="usergroups" primary_key="user_group_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import usergroupentry from '../modals/UserGroupEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'usergroups',
    components: {
        usergroupentry,
        deleteentry
    },
    data () {
        return {
            forms: {
                userrights: {
                    fields: {
                        rights: []
                    }
                }
            },
            tables: {
                usergroups: {
                    fields:[
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle',
                            thStyle: {width: '2%'}
                        },
                        {
                            key:'user_group',
                            label: 'User Group',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'user_group_desc',
                            label: 'Desc',
                            tdClass: 'align-middle',
                        },
                        {
                            key:'action',
                            label:'',
                            thStyle: {width: '75px'},
                            tdClass: 'align-middle text-center'
                        }
                    ],
                    items: []
                }
            },
            filters: {
                usergroups: {
                    criteria: null
                }
            },
            paginations: {
                usergroups: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            group_rights: [],
            modules: [],
        }
    },
    methods:{
        filterGroupRights(module_id){
            return this.group_rights.filter(gr => gr.module_id == module_id);
        },
        filterModules(module_group){
            return this.modules['modules'].filter(m => m.module_group == module_group);
        },
        processRights(user_group_id){
            this.forms.userrights.fields.rights = []
            this.group_rights.forEach(gr => {if(gr.rights == 1){
                this.forms.userrights.fields.rights.push({right_code: gr.right_code})
            }})


            this.$http.post('api/usergroup/rights/'+user_group_id, this.forms.userrights.fields, {
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then((response) => {  
                var data = response.data
                this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: "User group rights successfully updated. This will take effect on next login."
                })
            }).catch(error => {
                console.log(error)
            })
        },
        getRights(row){
            if(row.detailsShowing == true){
                return
            }
            this.$http.get('/api/group_rights/' + row.item.user_group_id , {
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                const res = response.data.data
                this.group_rights = res
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        }
    },
    async created () {
        await this.fillTableList('usergroups');
        await this.$http.get('/api/modules', {
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                const res = response.data
                this.modules = res.data
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('11-43') || this.checkRights('11-44')) == false){
                    this.tables.usergroups.fields.pop()
                }
            }
            return true
        }
    }
  }
</script>


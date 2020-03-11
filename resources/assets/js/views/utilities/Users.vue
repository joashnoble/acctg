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
                                User List
                                <small class="font-italic">List of all registered user.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button v-if="checkRights('10-38')" variant="success" @click="$refs.userentry.showModalEntry = true, $refs.userentry.entryMode='Add', $refs.userentry.clearFields('user')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New User
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.users.criteria"
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
                                    :filter="filters.users.criteria"
                                    :fields="tables.users.fields"
                                    :items.sync="tables.users.items"
                                    :current-page="paginations.users.currentPage"
                                    :per-page="paginations.users.perPage"
                                    @filtered="onFiltered($event,'users')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('10-39')" :size="'sm'" variant="primary" @click="$refs.userentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('10-40')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.users.totalRows" :per-page="paginations.users.perPage" v-model="paginations.users.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <userentry type="reference" ref="userentry"></userentry>
        <deleteentry entity="user" table="users" primary_key="id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import userentry from '../modals/UserEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'users',
    components: {
        userentry,
        deleteentry
    },
    data () {
        return {
            tables: {
                users: {
                    fields:[
                        {
                            key:'username',
                            label: 'Username',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'firstname',
                            label: 'Firstname',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'middlename',
                            label: 'Middlename',
                            tdClass: 'align-middle',
                        },
                        {
                            key:'lastname',
                            label: 'Lastname',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'email',
                            label: 'Email',
                            tdClass: 'align-middle',
                        },
                        {
                            key:'user_group_desc',
                            label: 'User Group',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
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
                users: {
                    criteria: null
                }
            },
            paginations: {
                users: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
        }
    },
    created () {
        this.fillTableList('users');
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('10-39') || this.checkRights('10-40')) == false){
                    this.tables.users.fields.pop()
                }
            }
            return true
        }
    }
  }
</script>


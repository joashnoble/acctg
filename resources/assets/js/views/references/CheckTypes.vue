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
                                Check Type List
                                <small class="font-italic">List of all registered check types.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button v-if="checkRights('7-26')" variant="success" @click="$refs.checktypeentry.showModalEntry = true, $refs.checktypeentry.entryMode='Add', $refs.checktypeentry.clearFields('checktype')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Check Type
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.checktypes.criteria"
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
                                    :filter="filters.checktypes.criteria"
                                    :fields="tables.checktypes.fields"
                                    :items.sync="tables.checktypes.items"
                                    :current-page="paginations.checktypes.currentPage"
                                    :per-page="paginations.checktypes.perPage"
                                    @filtered="onFiltered($event,'checktypes')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(action)="data" >
                                        <b-btn :size="'sm'" variant="primary" @click="$refs.checktypeentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.check_type_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.checktypes.totalRows" :per-page="paginations.checktypes.perPage" v-model="paginations.checktypes.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <checktypeentry type="reference" ref="checktypeentry"></checktypeentry>
        <deleteentry entity="Check Type" table="checktypes" primary_key="check_type_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import checktypeentry from '../modals/CheckTypeEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'checktypes',
    components: {
        checktypeentry,
        deleteentry
    },
    data () {
        return {
            tables: {
                checktypes: {
                    fields:[
                        {
                            key:'check_type_code',
                            label: 'Check Type Code',
                            thStyle: {width: '150px'},
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'check_type_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'account_title',
                            label: 'Account Title',
                            tdClass: 'align-middle',
                            thStyle: {width: '25%'},
                            sortable: true
                        },
                        {
                            key:'action',
                            label:'',
                            thStyle: {width: '75px'},
                            tdClass: 'text-center align-middle',
                        }
                    ],
                    items: []
                }
            },
            filters: {
                checktypes: {
                    criteria: null
                }
            },
            paginations: {
                checktypes: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
        }
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('7-27') || this.checkRights('7-28')) == false){
                    this.tables.checktypes.fields.pop()
                }
            }
            return true
        }
    },
    created () {
        this.fillTableList('checktypes');
    },
  }
</script>


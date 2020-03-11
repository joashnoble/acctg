<template>
    <div><!-- main container -->
        <notifications group="notification" />
        <div class="animated fadeIn"> <!-- main div -->
            <b-row> <!-- main row -->
                <b-col sm="12">
                    <b-card class="border-dark card-accent-dark" > <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span >
                                <!-- <i class="fa fa-bars"></i>  -->
                            Department List
                            <small class="font-italic">- List of all registered departments.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('2-6')" variant="success" @click="$refs.departmententry.setCreate()">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Department
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.departments.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                                <!-- <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend"><span type="text" class="input-group-text" ><i class="fa fa-search"></i> &nbsp; Search
                                    </span></div>
                                    <input type="text" class="form-control search" placeholder="">
                                </div> -->
                            </b-col>
                        </b-row> <!-- row button and search input -->
                        <b-row> <!-- row table -->
                            <b-col sm="12">
                                <b-table 
                                    v-if="checkAction"
                                    :filter="filters.departments.criteria"
                                    :fields="tables.departments.fields"
                                    :items.sync="tables.departments.items"
                                    :current-page="paginations.departments.currentPage"
                                    :per-page="paginations.departments.perPage"
                                    @filtered="onFiltered($event,'departments')"
                                    hover  small show-empty
                                > <!-- table -->
                                <template v-slot:cell(action)="data" > <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('2-7')" :size="'sm'" variant="primary" @click="$refs.departmententry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('2-8')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.department_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.departments.totalRows" :per-page="paginations.departments.perPage" v-model="paginations.departments.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->
        </div><!-- main div -->
        <departmententry type="reference" ref="departmententry"></departmententry>
        <deleteentry entity="department" table="departments" primary_key="department_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->
</template>

<script>
import departmententry from '../modals/DepartmentEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'departments',
    components: {
        departmententry,
        deleteentry
    },
    data () {
      return {
        entryMode: 'Add',
        showModalEntry: false, //if true show modal
        showModalDelete: false,
        tables: {
          departments: {
                fields: [
                {
                    key: 'department_code',
                    label: 'Code',
                    thStyle: {width: '150px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'department_desc',
                    label: 'Department Name',
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_1',
                    label: 'Signatory 1',
                    thStyle: {width: '120px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_1_position',
                    label: 'Position',
                    thStyle: {width: '130px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_2',
                    label: 'Signatory 2',
                    thStyle: {width: '120px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_2_position',
                    label: 'Position',
                    thStyle: {width: '130px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_3',
                    label: 'Signatory 3',
                    thStyle: {width: '120px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'signatory_3_position',
                    label: 'Position',
                    thStyle: {width: '130px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {   
                    key: 'action',
                    label: '',
                    thStyle: {width: '80px'},
                    tdClass: 'text-center align-middle'
                },
                ],
                items: []
            }
        },
        filters: {
          departments: {
            criteria: null
          }
        },
        paginations: {
          departments: {
            totalRows: 0,
            currentPage: 1,
            perPage: 10
          }
        }
      }
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('2-7') || this.checkRights('2-8')) == false){
                    this.tables.departments.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('departments')
    }
  }
</script>
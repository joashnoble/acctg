<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Departments
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.departmententry.showModalEntry = true, 
                            $refs.departmententry.entryMode='Add', 
                            $refs.departmententry.clearFields('department')">
                    New Department
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.departments.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.departments.fields"
            :items.sync="tables.departments.items"
            :filter="filters.departments.criteria"
            :current-page="paginations.departments.currentPage"
            :per-page="paginations.departments.perPage"
            @filtered="onFiltered($event,'departments')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.departmententry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.department_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.departments.currentPage"
                    :total-rows="paginations.departments.totalRows"
                    :per-page="paginations.departments.perPage"
                    prev-text="Previous"
                    next-text="Next"
                    hide-goto-end-buttons
                    size="sm"
                    align="right"
                    ></b-pagination>
                </b-col>
            </b-row> 
        </template>  
      </b-card>
    <departmententry type="reference" ref="departmententry"></departmententry>
    <deleteentry entity="department" table="departments" primary_key="department_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import departmententry from '../modals/DepartmentEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'departments',
    components :{
        departmententry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                departments :{
                    fields:[
                        {
                            key:'department_name',
                            label: 'Department Name',
                            sortable: true
                        },
                        {
                            key:'department_desc',
                            label: 'Description'
                        },
                        {
                            key:'action',
                            label:'Action',
                            thStyle: {width: '10%'},
                        }
                    ],
                     items: [],
                },
            }, // END OF TABLES
            filters: {
                departments: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                departments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('departments');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
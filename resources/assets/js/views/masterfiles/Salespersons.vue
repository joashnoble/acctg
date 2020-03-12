<template>
<div>
        <notifications group="notification" />
<div class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Salespersons
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.salespersonentry.showModalEntry = true, 
                            $refs.salespersonentry.entryMode='Add', 
                            $refs.salespersonentry.clearFields('salesperson')">
                        <i class="fa fa-file-o"></i> &nbsp;New Salesperson
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.salespersons.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.salespersons.fields"
            :items.sync="tables.salespersons.items"
            :filter="filters.salespersons.criteria"
            :current-page="paginations.salespersons.currentPage"
            :per-page="paginations.salespersons.perPage"
            @filtered="onFiltered($event,'salespersons')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.salespersonentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.salesperson_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>
            </b-table>
        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.salespersons.currentPage"
                    :total-rows="paginations.salespersons.totalRows"
                    :per-page="paginations.salespersons.perPage"
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
</div>
    <salespersonentry type="reference" ref="salespersonentry"></salespersonentry>
    <deleteentry entity="salesperson" table="salespersons" primary_key="salesperson_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import deleteentry from '../modals/DeleteEntry'
import salespersonentry from '../modals/SalespersonEntry'
export default {
    name: 'salespersons',
    components :{
        salespersonentry,
        deleteentry

    },
    data(){
        return {
            entryMode: 'Add',
            showModalEntry: false,
            showModalDelete: false,
            tables: {
                salespersons :{
                    fields:[
                        {
                            key:'salesperson_code',
                            label: 'Code',
                            sortable: true
                        },
                        {
                            key:'full_name',
                            label: 'Name',
                        },
                        {
                            key:'contact_no',
                            label: 'Contact Number',
                        },
                        {
                            key:'action',
                            label:'Action'
                        }
                    ],
                     items: [],
                },
            }, // END OF TABLES
            filters: {
                salespersons: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                salespersons: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
        } // END OF  RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('salespersons');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
<template>
<div>
        <notifications group="notification" />
<div class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Customers
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.customerentry.showModalEntry = true, 
                            $refs.customerentry.entryMode='Add', 
                            $refs.customerentry.clearFields('customer')">
                        <i class="fa fa-file-o"></i> &nbsp;New Customer
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.customers.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.customers.fields"
            :items.sync="tables.customers.items"
            :filter="filters.customers.criteria"
            :current-page="paginations.customers.currentPage"
            :per-page="paginations.customers.perPage"
            @filtered="onFiltered($event,'customers')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.customerentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.customer_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>
            </b-table>
        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.customers.currentPage"
                    :total-rows="paginations.customers.totalRows"
                    :per-page="paginations.customers.perPage"
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
    <customerentry type="reference" ref="customerentry"></customerentry>
    <deleteentry entity="customer" table="customers" primary_key="customer_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import deleteentry from '../modals/DeleteEntry'
import customerentry from '../modals/CustomerEntry'
export default {
    name: 'customers',
    components :{
        customerentry,
        deleteentry

    },
    data(){
        return {
            entryMode: 'Add',
            showModalEntry: false,
            showModalDelete: false,
            tables: {
                customers :{
                    fields:[
                        {
                            key:'customer_name',
                            label: 'Name',
                            sortable: true
                        },
                        {
                            key:'contact_name',
                            label: 'Contact Person',
                        },
                        {
                            key:'contact_no',
                            label: 'Contact No',
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
                customers: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                customers: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
        } // END OF  RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('customers');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
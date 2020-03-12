<template>
<div>
        <notifications group="notification" />
<div class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Suppliers
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.supplierentry.showModalEntry = true, 
                            $refs.supplierentry.entryMode='Add', 
                            $refs.supplierentry.clearFields('supplier')">
                        <i class="fa fa-file-o"></i> &nbsp;New Supplier
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.suppliers.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.suppliers.fields"
            :items.sync="tables.suppliers.items"
            :filter="filters.suppliers.criteria"
            :current-page="paginations.suppliers.currentPage"
            :per-page="paginations.suppliers.perPage"
            @filtered="onFiltered($event,'suppliers')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.supplierentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.supplier_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>
            </b-table>
        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.suppliers.currentPage"
                    :total-rows="paginations.suppliers.totalRows"
                    :per-page="paginations.suppliers.perPage"
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
    <supplierentry type="reference" ref="supplierentry"></supplierentry>
    <deleteentry entity="supplier" table="suppliers" primary_key="supplier_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import deleteentry from '../modals/DeleteEntry'
import supplierentry from '../modals/SupplierEntry'
export default {
    name: 'suppliers',
    components :{
        supplierentry,
        deleteentry

    },
    data(){
        return {
            entryMode: 'Add',
            showModalEntry: false,
            showModalDelete: false,
            tables: {
                suppliers :{
                    fields:[
                        {
                            key:'supplier_name',
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
                suppliers: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                suppliers: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
        } // END OF  RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('suppliers');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
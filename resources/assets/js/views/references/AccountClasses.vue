<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Account Classifications
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="4">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.accountclassentry.showModalEntry = true, 
                            $refs.accountclassentry.entryMode='Add', 
                            $refs.accountclassentry.clearFields('accountclass')">
                        New Account Classification
                </b-button>
            </b-col>
            <b-col sm="4">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.accountclasses.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.accountclasses.fields"
            :items.sync="tables.accountclasses.items"
            :filter="filters.accountclasses.criteria"
            :current-page="paginations.accountclasses.currentPage"
            :per-page="paginations.accountclasses.perPage"
            @filtered="onFiltered($event,'accountclasses')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.accountclassentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.account_class_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.accountclasses.currentPage"
                    :total-rows="paginations.accountclasses.totalRows"
                    :per-page="paginations.accountclasses.perPage"
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
    <accountclassentry type="reference" ref="accountclassentry"></accountclassentry>
    <deleteentry entity="accountclass" table="accountclasses" primary_key="account_class_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import accountclassentry from '../modals/AccountClassentry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'accountclasses',
    components :{
        accountclassentry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                accountclasses :{
                    fields:[
                        {
                            key:'account_class',
                            label: 'Account Class',
                            sortable: true
                        },
                        {
                            key:'description',
                            label: 'Description'
                        },
                        {
                            key:'account_type',
                            label: 'Description',
                            sortable: true
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
                accountclasses: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                accountclasses: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('accountclasses');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
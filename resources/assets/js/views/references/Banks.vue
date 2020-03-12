<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Banks
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.bankentry.showModalEntry = true, 
                            $refs.bankentry.entryMode='Add', 
                            $refs.bankentry.clearFields('bank')">
                        <i class="fa fa-file-o"></i> &nbsp;New Bank
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.banks.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.banks.fields"
            :items.sync="tables.banks.items"
            :filter="filters.banks.criteria"
            :current-page="paginations.banks.currentPage"
            :per-page="paginations.banks.perPage"
            @filtered="onFiltered($event,'banks')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.bankentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.bank_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.banks.currentPage"
                    :total-rows="paginations.banks.totalRows"
                    :per-page="paginations.banks.perPage"
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
    <bankentry type="reference" ref="bankentry"></bankentry>
    <deleteentry entity="bank" table="banks" primary_key="bank_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import bankentry from '../modals/BankEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'banks',
    components :{
        bankentry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                banks :{
                    fields:[
                        {
                            key:'bank_code',
                            label: 'Bank Code',
                        },
                        {
                            key:'bank_name',
                            label: 'Bank Name',
                            sortable: true
                        },
                        {
                            key:'account_number',
                            label: 'Account Number',
                        },
                        {
                            key:'account_type_desc',
                            label: 'Account Type',
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
                banks: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                banks: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('banks');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
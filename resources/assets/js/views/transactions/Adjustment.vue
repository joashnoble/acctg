<template>
    <div>
        <notifications group="notification" />
        <div v-show="showList === true" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></!-->
                                Adjustment List
                                <small>( {{ month_name + ' ' + app_year }} )</small>
                                <small class="font-italic">List of all adjustments.</small></span>
                            <span class="float-right"><b-btn @click="$refs.billingperiodfilter.showModalPeriod = true" variant="primary"><i class="fa fa-refresh"></i></b-btn></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button variant="success" 
                                        @click="!$refs.billingperiodfilter.checkIfSent() ? ($refs.adjustmententry.showModalEntry = true, $refs.adjustmententry.entryMode='Add', $refs.adjustmententry.clearFields('adjustment')) : ''">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Adjustment
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.adjustments.criteria"
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
                                    responsive
                                    :filter="filters.adjustments.criteria"
                                    :fields="tables.adjustments.fields"
                                    :items.sync="tables.adjustments.items"
                                    :current-page="paginations.adjustments.currentPage"
                                    :per-page="paginations.adjustments.perPage"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(action)="data" >
                                        <b-btn :size="'sm'" variant="primary" @click="!$refs.billingperiodfilter.checkIfSent() ? $refs.adjustmententry.setUpdate(data) : ''">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn :size="'sm'" variant="danger" @click="!$refs.billingperiodfilter.checkIfSent() ? $refs.deleteentry.setDelete(data.item.adjustment_id) : ''">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.adjustments.totalRows" :per-page="paginations.adjustments.perPage" v-model="paginations.adjustments.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <adjustmententry type="reference" ref="adjustmententry"></adjustmententry>
        <billingperiodfilter type="adjustment" ref="billingperiodfilter"></billingperiodfilter>
        <deleteentry entity="adjustment" table="adjustments" primary_key="adjustment_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import adjustmententry from '../modals/AdjustmentEntry'
import billingperiodfilter from '../modals/BillingPeriodFilter'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'adjustment',
    components: {
        adjustmententry,
        billingperiodfilter,
        deleteentry
    },
    data () {
        return {
            showList: false,
            options: {
                months: {
                    items: []
                },
                tenants: {
                    items: []
                },
                charges: {
                    items: []
                }
            },
            tables: {
                adjustments: {
                    fields: [
                        {
                            key: 'adjustment_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'adjustment_no',
                            label: 'Adjustment No.',
                            tdClass: 'align-middle',
                            thStyle: {width: "120px"},
                            sortable: true
                        },
                        {
                            key: 'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key: 'charge_desc',
                            label: 'Charge',
                            tdClass: 'align-middle',
                            thStyle: {width: "15%"},
                            sortable: true
                        },
                        {
                            key: 'adjustment_type',
                            label: 'Adj. Type',
                            tdClass: 'align-middle',
                            thStyle: {width: "85px"},
                            sortable: true,
                            formatter: (value) => {
                                if(value == 0){
                                    return 'In'
                                }
                                return 'Out'
                            }
                        },
                        {
                            key: 'amount',
                            label: 'Amount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: "15%"},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key: 'notes',
                            label: 'Notes',
                            thStyle: {width: "20%"},
                            tdClass: 'align-middle',
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            thStyle: {width: '115px'},
                            tdClass: 'text-center'
                        }
                    ],
                    items: []
                }
            },
            filters: {
                adjustments: {
                    criteria: null
                }
            },
            paginations: {
                adjustments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            month_name: '',
            app_year: ''
        }
    },
    created(){
        this.fillOptionsList('periods')
        this.fillOptionsList('months')
    },
    // watch: {
    //     showModalEntry: function (showModalEntry) {
    //         if(showModalEntry){
    //             let self = this
    //             Vue.nextTick(function(){
    //                 self.focusElement('tenant_id', true)
    //             })
    //         }
    //     },
    // }
}
</script>
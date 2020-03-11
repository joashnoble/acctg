<template>
    <div>
        <notifications group="notification" />
        <div v-show="showList === true" class="animated fadeIn">
        <!-- <div class="animated fadeIn"> -->
            <b-row>
                <b-col sm=12>
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                Charge Slip List
                                <small>( {{ month_name + ' ' + app_year }} )</small>
                                <small class="font-italic">List of all charge slips</small>
                            </span>
                            <span class="float-right"><b-btn @click="$refs.billingperiodfilter.showModalPeriod = true" variant="primary"><i class="fa fa-refresh"></i></b-btn></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button v-if="checkRights('20-74')" variant="success" @click="$refs.chargeslipentry.showModalEntry = true, $refs.chargeslipentry.entryMode='Add', $refs.chargeslipentry.clearFields('chargeslip')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Charge Slip
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.chargeslips.criteria"
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
                                    :filter="filters.chargeslips.criteria"
                                    :fields="tables.chargeslips.fields"
                                    :items.sync="tables.chargeslips.items"
                                    :current-page="paginations.chargeslips.currentPage"
                                    :per-page="paginations.chargeslips.perPage"
                                    @filtered="onFiltered($event,'chargeslips')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(status)="data">
                                        <span v-html="data.value"></span>
                                    </template>
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('20-77')" title="Approve" :size="'sm'" variant="secondary" @click="$refs.chargeslipentry.approvalDisapproval(data, true)">
                                            <i class="fa fa-thumbs-o-up"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('20-77')" title="Disapprove" :size="'sm'" variant="warning" @click="$refs.chargeslipentry.approvalDisapproval(data, false)">
                                            <i class="fa fa-thumbs-o-down"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('20-75')" :size="'sm'" variant="primary" @click="$refs.chargeslipentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('20-78')" :size="'sm'" variant="success" @click="$refs.chargeslipentry.print(data.item.charge_slip_id)">
                                            <i class="fa fa-print"></i>
                                        </b-btn>
                                        <b-btn v-if="checkRights('20-76')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.charge_slip_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.chargeslips.totalRows" :per-page="paginations.chargeslips.perPage" v-model="paginations.chargeslips.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
        <chargeslipentry type="reference" ref="chargeslipentry"></chargeslipentry>
        <billingperiodfilter type="chargeslip" ref="billingperiodfilter"></billingperiodfilter>
        <deleteentry entity="chargeslip" table="chargeslips" primary_key="charge_slip_id" ref="deleteentry"></deleteentry>
    </div>
</template>
<script>
import chargeslipentry from '../modals/ChargeSlipEntry'
import billingperiodfilter from '../modals/BillingPeriodFilter'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'chargeslip',
    components: {
        chargeslipentry,
        billingperiodfilter,
        deleteentry
    },
    data() {
        return {
            showList: false,
            tables: {
                chargeslips: {
                    fields:[
                        {
                            key:'charge_slip_no',
                            label: 'Charge Slip No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '100px'},
                            sortable: true
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'charge_desc',
                            label: 'Charge',
                            tdClass: 'align-middle',
                            thStyle: {width: '12%'},
                            sortable: true
                        },
                        {
                            key:'charge_slip_type',
                            label: 'Type',
                            tdClass: 'align-middle',
                            thStyle: {width: '150px'},
                            formatter: (value) => {
                                if(value == 1){
                                    return 'Non-Compliance to House Rules'
                                }
                                else if(value == 2){
                                    return 'Overtime (OT) Payment'
                                }
                                else{
                                    return 'OTHERS'
                                }
                            }
                        },
                        {
                            key:'charge_slip_datetime',
                            label: 'Datetime',
                            tdClass: 'align-middle',
                            thStyle: {width: '120px'},
                            sortable: true,
                            formatter: (value) => {
                                return this.moment(value, 'MMMM DD, YYYY')
                            }
                        },
                        {
                            key:'charge_ref_wp_no',
                            label: 'Ref: WP#',
                            tdClass: 'align-middle',
                            thStyle: {width: '100px'},
                        },
                        {
                            key:'charge_slip_amount',
                            label: 'Amount',
                            tdClass: 'align-middle text-right',
                            thClass: 'text-right',
                            thStyle: {width: '140px'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'status',
                            label: 'Status',
                            tdClass: 'align-middle',
                            thStyle: {width: '80px'},
                            formatter: (value) => {
                                if (value == 0){
                                    return '<span style="color: orange"><i class="fa fa-history"></i> Pending</span>'
                                }
                                else if (value == 1){
                                    return '<span class="text-success"><i class="fa fa-thumbs-up""></i> Approved</span>'
                                }
                                else {
                                    return '<span class="text-danger"><i class="fa fa-thumbs-down""></i> Disapproved</span>'
                                }
                            }
                        },
                        {
                            key:'action',
                            label:'',
                            thStyle: {width: '160px'},
                            tdClass: 'align-middle text-center'
                        }
                    ],
                    items: []
                }
            },
            filters: {
                chargeslips: {
                    criteria: null
                }
            },
            paginations: {
                chargeslips: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            month_name: '',
            app_year: ''
        }
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('10-39') || this.checkRights('10-40')) == false){
                    this.tables.chargeslips.fields.pop()
                }
            }
            return true
        }
    }
}
</script>
<template>
    <div>
        <notifications group="notification" />
        <div class="animated fadeIn"> <!-- main div -->
            <b-row> <!-- main row -->
                <b-col sm="12">
                    <b-card class="card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Sent Payment List
                                <small class="font-italic">List of all sent payment to accounting.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="2">
                                    <!-- <b-button v-if="checkRights('19-67')" variant="primary" @click="showModalPeriod = true">
                                            <i class="fa fa-plus-circle"></i> Send Payment
                                    </b-button> -->
                            </b-col>
                            <b-col  sm="1">
                                 <label class="col-form-label float-right">From :</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker 
                                    @input="getSentPayment()"
                                    v-model="filter_start_date" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">To :</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker
                                    @input="getSentPayment()"
                                    v-model="filter_end_date" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.payments.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row> <!-- row button and search input -->
                    
                        <b-row>
                            <b-col sm="12">
                                <b-table 
                                    responsive
                                    :filter="filters.payments.criteria"
                                    :fields="tables.payments.fields"
                                    :items.sync="tables.payments.items"
                                    :current-page="paginations.payments.currentPage"
                                    :per-page="paginations.payments.perPage"
                                    @filtered="onFiltered($event,'payments')"
                                    striped hover small show-empty
                                >
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.payments.totalRows" :per-page="paginations.payments.perPage" v-model="paginations.payments.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                    <b-card class="card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span class="text-danger">
                                <!-- <i class="fa fa-bars"></i>  -->
                                Pending Payment List
                                <small class="font-italic">List of all pending payment to accounting.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="2">
                                    <b-button v-if="checkRights('19-67')" variant="success" @click="showModalPeriod = true">
                                            <i class="fa fa-paper-plane"></i> &nbsp; Send Payment
                                    </b-button>
                            </b-col>
                            <b-col  sm="1">
                                 <label class="col-form-label float-right">From :</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker 
                                    @input="getSentPayment()"
                                    v-model="filter_start_date" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">To :</label>
                            </b-col>
                            <b-col sm="2">
                                <date-picker
                                    @input="getSentPayment()"
                                    v-model="filter_end_date" 
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.ppayments.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row> <!-- row button and search input -->
                    
                        <b-row>
                            <b-col sm="12">
                                <b-table 
                                    responsive
                                    :filter="filters.ppayments.criteria"
                                    :fields="tables.ppayments.fields"
                                    :items.sync="tables.ppayments.items"
                                    :current-page="paginations.ppayments.currentPage"
                                    :per-page="paginations.ppayments.perPage"
                                    @filtered="onFiltered($event,'ppayments')"
                                    striped hover small show-empty
                                >
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.ppayments.totalRows" :per-page="paginations.ppayments.perPage" v-model="paginations.ppayments.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->

        </div>
        <div>
            <b-modal
                v-model="showModalPeriod"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                @shown="focusElement('period_id')"
            >
                <div slot="modal-title">
                    Payment Date
                </div>
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                                <label for="start_date">Start Date</label>
                                <date-picker 
                                    ref="start_date"
                                    id="start_date"
                                    v-model="forms.period.fields.start_date"
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-form-group>

                            <b-form-group>
                                <label for="end_date">End Date</label>
                                <date-picker 
                                    ref="end_date"
                                    id="end_date"
                                    v-model="forms.period.fields.end_date"
                                    lang="en" 
                                    input-class="form-control mx-input"
                                    format="MMMM DD, YYYY"
                                    :clearable="false">
                                </date-picker>
                            </b-form-group>
                    </b-col>
                </b-row>
                <div v-if="showConfirmation==false" slot="modal-footer">
                    <b-button :disabled="isSending" variant="success" @click="showConfirmation=true">
                        <icon v-if="isSending" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        Send to Accounting
                    </b-button>
                    <b-button variant="danger" @click="showModalPeriod=false">Close</b-button>            
                </div>
                <div v-else slot="modal-footer">
                    Are you sure you want to continue? This transaction is irreversable.
                    <b-button class="ml-2" variant="success" @click="showConfirmation=false, onInsertAr()">
                        <i class="fa fa-check"></i>
                        Yes
                    </b-button>
                    <b-button variant="danger" @click="showConfirmation=false">No</b-button>            
                </div>
            </b-modal>
        </div>   
    </div>
</template>

<script>
export default {
    name: 'ar_payment',
    data () {
        return {
            showModalPeriod: false,
            showConfirmation: false,
            isSending: false,
            filter_start_date : moment().startOf('month').format('YYYY-MM-DD'),
            filter_end_date : moment().endOf('month').format('YYYY-MM-DD'),
            forms: {
                period: {
                    fields: {
                        start_date: moment().startOf('month').format('YYYY-MM-DD'),
                        end_date: moment().endOf('month').format('YYYY-MM-DD'),
                    }
                }
            },
            tables: {
                payments: {
                    fields:[
                        {
                            key:'transaction_no',
                            label: 'Trans No',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'reference_no',
                            label: 'Reference No',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'payment_date',
                            label: 'Payment Date',
                            formatter: (value) => {
                                return this.moment(value, 'MMMM DD, YYYY')
                            },
                            tdClass: 'align-middle',
                        },
                        {
                            key:'discount',
                            label: 'Discount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'balance_paid',
                            label: 'Balance Paid',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'amount_paid',
                            label: 'Amount Paid',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                    ],
                    items: []
                },
                ppayments: {
                    fields:[
                        {
                            key:'transaction_no',
                            label: 'Trans No',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'reference_no',
                            label: 'Reference No',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'payment_date',
                            label: 'Payment Date',
                            formatter: (value) => {
                                return this.moment(value, 'MMMM DD, YYYY')
                            },
                            tdClass: 'align-middle',
                        },
                        {
                            key:'discount',
                            label: 'Discount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'balance_paid',
                            label: 'Balance Paid',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                        {
                            key:'amount_paid',
                            label: 'Amount Paid',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        },
                    ],
                    items: []
                },
            },
            filters: {
                payments: {
                    criteria: null
                },
                ppayments: {
                    criteria: null
                },
            },
            paginations: {
                payments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                },
                ppayments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                },
            },
        }
    },
    methods:{
        async onInsertAr () {
            this.isSending = true
            var start_date = this.moment(this.forms.period.fields.start_date, 'YYYY-MM-DD')
            var end_date = this.moment(this.forms.period.fields.end_date, 'YYYY-MM-DD')
            await this.$http.get('api/arpayment/getjournalinfo/' + start_date + '/' + end_date,  {
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                this.$http.post('api/arpayment/insertjournalinfo', {info: response.data, start_date: start_date, end_date: end_date}, {
                    headers: {
                            Authorization: 'Bearer ' + localStorage.getItem('token')
                        }
                })
                .then((response) => {
                    this.$notify({
                        type: 'success',
                        group: 'notification',
                        title: 'Success',
                        text: "Successfully sent to Accounting."
                    })
                    this.isSending = false
                    this.showModalPeriod = false
                    this.filter_start_date = this.forms.period.fields.start_date
                    this.filter_end_date = this.forms.period.fields.end_date
                    this.getSentPayment()
                })
                .catch(error => {
                    this.isSending = false
                    if (!error.response) return
                    console.log(error)
                })
            })
            .catch(error => {
                this.isSending = false
                if (!error.response) return
                console.log(error)
            })
        },
        getSentPayment(){
            var start_date = this.moment(this.filter_start_date, 'YYYY-MM-DD')
            var end_date = this.moment(this.filter_end_date, 'YYYY-MM-DD')
            this.$http.get('api/arpayment/getsentpayment/' + start_date + '/' + end_date, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    this.tables.payments.items = response.data.data.filter(d => d.is_sent == 1)
                    this.tables.ppayments.items = response.data.data.filter(d => d.is_sent == 0)
                    this.paginations.payments.totalRows = response.data.data.length
                })
                .catch(error => {
                    if (!error.response) return
                    console.log(error)
                })
        },
    },
    created(){
        this.getSentPayment()
    },
}
</script>
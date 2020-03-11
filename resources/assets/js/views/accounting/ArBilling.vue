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
                                Sent Billing List
                                <small class="font-italic">List of all sent billing to accounting.</small></span>
                                <span class="text-primary float-right">
                                <small>Total Rows : {{tables.billings.items.length}}</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                <!-- <b-button v-if="checkRights('18-65')" variant="primary" @click="showModalPeriod = true">
                                        <i class="fa fa-plus-circle"></i> Send Billing
                                </b-button> -->
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">Filter :</label>
                            </b-col>
                            <b-col  sm="3">
                                <select2
                                    ref="filter_period_id"
                                    :allowClear="false"
                                    :placeholder="'Select Billing Period'"
                                    v-model="filter_period_id"
                                    @input="filterBilling()"
                                >
                                    <option :value="0">ALL</option>
                                    <option v-for="period in options.periods.items" :key="period.period_id" :value="period.period_id">{{ moment(period.period_start_date, "MMMM DD, YYYY")}} - {{ moment(period.period_end_date, "MMMM DD, YYYY")}}</option>
                                </select2>
                            </b-col>

                            <b-col  sm="4">
                                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.billings.criteria"
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
                                    :filter="filters.billings.criteria"
                                    :fields="tables.billings.fields"
                                    :items.sync="tables.billings.items"
                                    :current-page="paginations.billings.currentPage"
                                    :per-page="paginations.billings.perPage"
                                    @filtered="onFiltered($event,'billings')"
                                    striped hover small show-empty
                                >
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.billings.totalRows" :per-page="paginations.billings.perPage" v-model="paginations.billings.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                    <b-card class="card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span class="text-danger">
                                <!-- <i class="fa fa-bars"></i>  -->
                                Pending Billing List
                                <small class="font-italic">List of all pending billing to accounting.</small></span>
                                <span class="text-danger float-right">
                                <small>Total Rows : {{tables.pbillings.items.length}}</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                <b-button v-if="checkRights('18-65')" variant="success" @click="showModalPeriod = true">
                                        <i class="fa fa-paper-plane"></i> &nbsp; Send Billing
                                </b-button>
                            </b-col>
                            <b-col sm="1">
                                <label class="col-form-label float-right">Filter :</label>
                            </b-col>
                            <b-col  sm="3">
                                <select2
                                    ref="filter_period_id"
                                    :allowClear="false"
                                    :placeholder="'Select Billing Period'"
                                    v-model="filter_period_id"
                                    @input="filterBilling()"
                                >
                                    <option :value="0">ALL</option>
                                    <option v-for="period in options.periods.items" :key="period.period_id" :value="period.period_id">{{ moment(period.period_start_date, "MMMM DD, YYYY")}} - {{ moment(period.period_end_date, "MMMM DD, YYYY")}}</option>
                                </select2>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.pbillings.criteria"
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
                                    :filter="filters.pbillings.criteria"
                                    :fields="tables.pbillings.fields"
                                    :items.sync="tables.pbillings.items"
                                    :current-page="paginations.pbillings.currentPage"
                                    :per-page="paginations.pbillings.perPage"
                                    @filtered="onFiltered($event,'pbillings')"
                                    striped hover small show-empty
                                >
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.pbillings.totalRows" :per-page="paginations.pbillings.perPage" v-model="paginations.pbillings.currentPage"
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
                    Billing Period
                </div>
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                            <label>Billing Period</label>
                            <select2
                                ref="period_id"
                                :allowClear="false"
                                :placeholder="'Select Billing Period'"
                                v-model="forms.period.fields.period_id"
                                @input="getPeriodInfo"
                            >
                                <option v-for="period in options.periods.items" :key="period.period_id" :value="period.period_id">{{ moment(period.period_start_date, "MMMM DD, YYYY")}} - {{ moment(period.period_end_date, "MMMM DD, YYYY")}}</option>
                            </select2>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                            <label>Due Date</label>
                            <b-form-input 
                                v-model="forms.period.fields.period_due_date"
                                type="text" 
                                placeholder="Due Date"
                                readonly>
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col lg=6>
                        <b-form-group>
                            <label>App Year</label>
                            <b-form-input 
                                v-model="forms.period.fields.app_year"
                                type="text" 
                                placeholder="App Year"
                                readonly>
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col lg=6>
                        <b-form-group>
                            <label>Month</label>
                            <b-form-input 
                                v-model="forms.period.fields.month_name"
                                type="text" 
                                placeholder="Month"
                                readonly>
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div v-if="showConfirmation==false" slot="modal-footer">
                    <b-button :disabled="isSending" variant="success" @click="checkIfSent()">
                        <icon v-if="isSending" name="sync" spin></icon>
                        <i class="fa fa-check"></i>
                        Send to Accounting
                    </b-button>
                    <b-button variant="danger" @click="showModalPeriod=false">Close</b-button>            
                </div>
                <div v-else slot="modal-footer">
                    Are you sure you want to continue? This transaction is irreversible.
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
    name: 'ar_billing',
    data () {
        return {
            tables: {
                billings: {
                    fields: [
                        {
                            key: 'billing_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'billing_no',
                            label: 'Billing No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'contract_no',
                            label: 'Contract No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'app_year',
                            label: 'App Year/Month',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            formatter: (value, key, item) => {
                                return item.app_year + '/' + item.month_name
                            }
                        },
                        {
                            key: 'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key: 'tenant_code',
                            label: 'Tenant Code',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                        },
                        {
                            key: 'total_amount_due',
                            label: 'Total Amount Due',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '200px'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        }
                    ],
                    items: []
                },
                pbillings: {
                    fields: [
                        {
                            key: 'billing_id',
                            thClass: 'd-none',
                            tdClass: 'd-none'
                        },
                        {
                            key: 'billing_no',
                            label: 'Billing No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'contract_no',
                            label: 'Contract No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            sortable: true
                        },
                        {
                            key: 'app_year',
                            label: 'App Year/Month',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                            formatter: (value, key, item) => {
                                return item.app_year + '/' + item.month_name
                            }
                        },
                        {
                            key: 'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key: 'tenant_code',
                            label: 'Tenant Code',
                            tdClass: 'align-middle',
                            thStyle: {width: '110px'},
                        },
                        {
                            key: 'total_amount_due',
                            label: 'Total Amount Due',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: '200px'},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        }
                    ],
                    items: []
                },
            },
            showModalPeriod: false,
            showConfirmation: false,
            isSending: false,
            filter_period_id: 0,
            forms: {
                period: {
                    fields: {
                        period_id: null,
                        period_due_date: null,
                        app_year: null,
                        month_id: null,
                        month_name: null,
                        is_sent: null
                    }
                }
            },
            filters: {
                billings: {
                    criteria: null
                },
                pbillings: {
                    criteria: null
                },
            },
            paginations: {
                billings: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                },
                pbillings: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                },
            },
            options: {
                periods: {
                    items: []
                },
            },
        }
    },
    methods:{
        async onInsertAr () {
            this.isSending = true
            await this.$http.get('api/arbilling/getjournalinfo/' + this.forms.period.fields.period_id, {
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                // this.$http.post('api/arbilling/insertjournalinfo', {info: response.data, period_id: this.forms.period.fields.period_id}, {
                //     headers: {
                //             Authorization: 'Bearer ' + localStorage.getItem('token')
                //         }
                // })
                // .then((response) => {
                    this.$notify({
                        type: 'success',
                        group: 'notification',
                        title: 'Success',
                        text: "Successfully sent to Accounting."
                    })
                    this.isSending = false
                    this.showModalPeriod = false
                    this.filter_period_id = this.forms.period.fields.period_id
                    this.getSentBilling(this.filter_period_id)
                // })
                // .catch(error => {
                //     this.isSending = false
                //     if (!error.response) return
                //     console.log(error)
                // })
            })
            .catch(error => {
                this.isSending = false
                if (!error.response) return
                console.log(error)
            })
        },
        getPeriodInfo: function (value, data){
            if(data.length > 0){
                var period = this.options.periods.items[data[0].element.index]
                this.forms.period.fields.period_due_date = moment(period.period_due_date).format('MMMM DD, YYYY')
                this.forms.period.fields.app_year = period.app_year
                this.forms.period.fields.month_name = period.month_name
                this.forms.period.fields.month_id = period.month_id
                this.forms.period.fields.due_date = moment(period.period_due_date).format('MMMM DD, YYYY')
                this.forms.period.fields.is_sent = period.is_sent
            }
        },
        filterBilling(){
            this.getSentBilling(this.filter_period_id)
        },
        checkIfSent(){
            // if(this.forms.period.fields.is_sent){
            //      this.$notify({
            //         type: 'error',
            //         group: 'notification',
            //         title: 'Error',
            //         text: "This period was already been sent."
            //     })
            //     return
            // }
            this.showConfirmation = true
        },
        getSentBilling(period_id){
            this.$http.get('api/arbilling/getsentbilling/' + period_id, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    this.tables.billings.items = response.data.data.filter(d => d.is_sent == 1)
                    this.tables.pbillings.items = response.data.data.filter(d => d.is_sent == 0)
                    this.paginations.billings.totalRows = this.tables.billings.items.length
                    this.paginations.pbillings.totalRows = this.tables.pbillings.items.length
                })
                .catch(error => {
                    if (!error.response) return
                    console.log(error)
                })
        },
        fillOptions(entity){
            this.$http.get('api/' + entity,{
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then((response) => {
                const items = response.data.data
                this.options[entity].items = items
            })
            .then(() => {
                var filter = this.options.periods.items.filter(p => 
                    p.month_id == this.moment(new Date, 'M') && p.app_year == this.moment(new Date, 'YYYY')
                )

                this.filter_period_id = filter[0].period_id
                this.getSentBilling(this.filter_period_id)
            })
            .catch(error => {
                if (!error.response) return
                console.log(error)
            })
        }
    },
    created(){
        this.fillOptions('periods')
    },
}
</script>
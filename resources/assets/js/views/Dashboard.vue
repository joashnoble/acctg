<style scoped>
    .info-report{
        cursor:pointer;
    }
    .info-box {
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        border-radius: 2px;
        margin-bottom: 15px;
    }   
    .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 45px;
        line-height: 90px;
        background: rgba(0,0,0,0.2);
    }
    .info-box-content {
        text-align: center;
        vertical-align: middle;
        display: inherit;
    }
    .info-box-text {
        text-transform: uppercase;
       
    }
    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }
</style>

<template>
    <div>
        <div class="animated fadeIn"> <!-- main div -->
            <b-row>
                <b-col sm="12">
                    <b-row sm="12">
                        <b-col sm="3">
                            <!-- <div class="info-box bg-primary" >
                                <span class="info-box-icon bg-aqua">
                                    <i class="fa fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <h6>TOTAL TENANTS</h6>
                                    </span>
                                    <div class="info-box-number">
                                        <h3>{{no_of_tenants}}</h3>
                                    </div>
                                </div>
                            </div>   -->
                            <b-card no-body class="bg-primary">
                                <b-card-body class="p-3 clearfix">
                                    <i class="fa fa-users p-3 font-2xl mr-3 float-left"></i>
                                    <div class="h4 mb-0 mt-2">{{no_of_tenants}}</div>
                                    <div class="text-uppercase font-weight-bold font-xs">TOTAL TENANTS</div>
                                </b-card-body>
                            </b-card>
                        </b-col>
                        <b-col sm="3">
                            <!-- <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="fa fa-paperclip"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <h6>TOTAL CONTRACTS</h6>
                                    </span>
                                    <div class="info-box-number">
                                        <h3>{{no_of_contracts}}</h3>
                                    </div>
                                </div>
                            </div>   -->
                            <b-card no-body class="bg-success">
                                <b-card-body class="p-3 clearfix">
                                    <i class="fa fa-list p-3 font-2xl mr-3 float-left"></i>
                                    <div class="h4 mb-0 mt-2">{{no_of_contracts}}</div>
                                    <div class="text-uppercase font-weight-bold font-xs">Total Contracts</div>
                                </b-card-body>
                            </b-card>
                        </b-col>
                        <b-col sm="3">
                            <!-- <div @click="printNew" class="info-box info-report bg-warning">
                                <span class="info-box-icon">
                                    <i class="fa fa-plus">

                                    </i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <h6>NEW CONTRACTS</h6>
                                    </span>
                                    <div class="info-box-number">
                                        <h3>{{no_of_new_contracts}}</h3>
                                    </div>
                                </div>
                            </div>   -->
                            <b-card no-body class="bg-warning">
                                <b-card-body class="p-3 clearfix">
                                    <b-dropdown class="float-right" variant="transparent p-0" right>
                                        <template slot="button-content">
                                            <i class="icon-settings"></i>
                                        </template>
                                        <b-dropdown-item @click="printNew">Preview</b-dropdown-item>
                                    </b-dropdown>
                                    <i class="fa fa-file-o p-3 font-2xl mr-3 float-left"></i>
                                    <div class="h4 mb-0 mt-2">{{no_of_new_contracts}}</div>
                                    <div class="text-uppercase font-weight-bold font-xs">New Contracts</div>
                                </b-card-body>
                            </b-card>
                        </b-col>
                        <b-col sm="3">
                            <!-- <div @click="printExp" class="info-box info-report bg-danger">
                                <span class="info-box-icon">
                                    <i class="fa fa-file-o"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">
                                        <h6>EXPIRING CONTRACTS</h6>
                                    </span>
                                    <div class="info-box-number">
                                        <h3>{{no_of_expiring_contracts}}</h3>
                                    </div>
                                </div>
                            </div>   -->
                            <b-card no-body class="bg-danger">
                                <b-card-body class="p-3 clearfix">
                                    <b-dropdown class="float-right" variant="transparent p-0" right>
                                        <template slot="button-content">
                                            <i class="icon-settings"></i>
                                        </template>
                                        <b-dropdown-item @click="printExp">Preview</b-dropdown-item>
                                    </b-dropdown>
                                    <i class="fa fa-clock-o p-3 font-2xl mr-3 float-left"></i>
                                    <div class="h4 mb-0 mt-2">{{no_of_expiring_contracts}}</div>
                                    <div class="text-uppercase font-weight-bold font-xs">Expiring Contracts</div>
                                </b-card-body>
                            </b-card>
                        </b-col>
                    </b-row>
                    <b-row sm="12">
                        <b-col sm="6">
                            <b-card class="card-accent-dark" header="Pending Contracts" style="min-height: 450px;">
                                <b-row class="mb-2">
                                    <!-- <b-col sm="4">
                                        <date-picker 
                                            @input="filterPayments()"
                                            v-model="date_from" 
                                            lang="en" 
                                            input-class="form-control mx-input"
                                            format="MMMM DD, YYYY"
                                            :clearable="false">
                                        </date-picker>
                                    </b-col>
                                    <b-col sm="1">
                                        <label class="col-form-label">to</label>
                                    </b-col>
                                    <b-col sm="4">
                                        <date-picker
                                            @input="filterPayments()"
                                            v-model="date_to" 
                                            lang="en" 
                                            input-class="form-control mx-input"
                                            format="MMMM DD, YYYY"
                                            :clearable="false">
                                        </date-picker>
                                    </b-col> -->
                                    <b-col sm="6"></b-col>
                                    <b-col sm="6">
                                       <b-input-group prepend-html='<i class="fa fa-search"></i>'>
                                            <b-form-input 
                                                        v-model="filters.contracts.criteria"
                                                        type="text" 
                                                        placeholder="Search"
                                                        debounce="250">
                                            </b-form-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                                <b-table 
                                    :filter="filters.contracts.criteria"
                                    :fields="tables.contracts.fields"
                                    :items.sync="tables.contracts.items"
                                    :current-page="paginations.contracts.currentPage"
                                    :per-page="paginations.contracts.perPage"
                                    @filtered="onFiltered($event,'contracts')"
                                    hover show-empty
                                    responsive
                                    small
                                    head-variant="light"
                                >
                                    
                                </b-table>
                                <template v-slot:footer>
                                        <b-pagination
                                            :align="'right'"
                                            :total-rows="paginations.contracts.totalRows"
                                            :per-page="paginations.contracts.perPage"
                                            v-model="paginations.contracts.currentPage" />
                                </template>
                            </b-card>
                        </b-col>
                        <b-col sm="6">
                            <b-card class="card-accent-dark" header="Pending Adjustments" style="min-height: 450px;">
                                <b-row>
                                    <b-col sm=6></b-col>
                                    <b-col sm=6>
                                        <b-input-group class="mb-2" prepend-html='<i class="fa fa-search"></i>'>
                                            <b-form-input 
                                                        v-model="filters.adjustments.criteria"
                                                        type="text" 
                                                        placeholder="Search"
                                                        debounce="250">
                                            </b-form-input>
                                        </b-input-group>
                                    </b-col>
                                </b-row>
                                <b-table 
                                    :filter="filters.adjustments.criteria"
                                    :fields="tables.adjustments.fields"
                                    :items.sync="tables.adjustments.items"
                                    :current-page="paginations.adjustments.currentPage"
                                    :per-page="paginations.adjustments.perPage"
                                    @filtered="onFiltered($event,'adjustments')"
                                    hover show-empty
                                    responsive
                                    small
                                    head-variant="light"
                                >
                                    
                                </b-table>
                                <template v-slot:footer>
                                    <b-pagination
                                            :align="'right'"
                                            :total-rows="paginations.adjustments.totalRows"
                                            :per-page="paginations.adjustments.perPage"
                                            v-model="paginations.adjustments.currentPage" />
                                </template>
                            </b-card>
                        </b-col>
                    </b-row>

                    <b-row>
                        
                        <b-col sm="12">
                            <b-card class="card-accent-dark" header="Line Chart">
                                <div class="chart-wrapper" style="height:400px;border: 1px solid gray; padding: 15px;">
                                    <line-example :height="386" :data="line_data"/>
                                </div>
                            </b-card>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
        </div>
    </div>
    
</template>
<script>
export default {
    name: 'dashboard',
    data () {
      return {
            payment_type: -1,
            tables: {
                adjustments: {
                    fields:[
                        {
                            key:'adjustment_no',
                            label: 'Adjustment No.',
                            tdClass: 'align-middle',
                            thStyle: {width: "120px"}
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                        },
                        {
                            key:'charge_desc',
                            label: 'Charge',
                            tdClass: 'align-middle',
                            thStyle: {width: "22%"}
                        },
                        {
                            key:'adjustment_type',
                            label: 'Adj. Type',
                            tdClass: 'align-middle',
                            thStyle: {width: "15%"}
                        },
                        {
                            key:'amount',
                            label: 'Amount',
                            thClass: 'text-right',
                            tdClass: 'text-right align-middle',
                            thStyle: {width: "15%"},
                            formatter: (value) => {
                                return this.formatNumber(value)
                            }
                        }
                    ],
                    items: []
                },
                contracts: {
                    fields:[
                        {
                            key:'contract_no',
                            label: 'Contract No.',
                            tdClass: 'align-middle',
                            thStyle: {width: '120px'}
                        },
                        {
                            key:'trade_name',
                            label: 'Tenant',
                            tdClass: 'align-middle',
                        },
                        {
                            key:'department_desc',
                            label: 'Department',
                            tdClass: 'align-middle',
                            thStyle: {width: "22%"}
                        },
                        {
                            key:'location_desc',
                            label: 'Location',
                            tdClass: 'align-middle',
                            thStyle: {width: "22%"}
                        },
                    ],
                    items: []
                }
            },
            filters: {
                adjustments: {
                    criteria: null
                },
                contracts: {
                    criteria: null
                }
            },
            paginations: {
                adjustments: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 8
                },
                contracts: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 8
                }
            },
            date_from: moment().startOf('month').format('YYYY-MM-DD'),
            date_to: moment().endOf('month').format('YYYY-MM-DD'),
            line_data: [],
            no_of_tenants: 0,
            no_of_contracts: 0,
            no_of_new_contracts: 0,
            no_of_expiring_contracts: 0,
        }
    },
    methods: {
        filterPayments(){
            var from = this.moment(this.date_from, 'YYYY-MM-DD')
            var to = this.moment(this.date_to, 'YYYY-MM-DD')
            this.filterTableList('payments', from, to)
        },
        filterPaymentLine(){
            this.$http.get('api/dashboard/payment/'+this.payment_type+'/'+false,{
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then((response) => {  
                var data = response.data
                var dArray = []
                data.forEach(element => {
                    dArray.push(element.amount)
                });
                this.line_data = dArray
            }).catch(error => {
                console.log(error)
            })
        },
        printExp(){
            window.open('api/reports/expiringcontracts')
            // let routeData = this.$router.resolve({name: 'Expiring Contracts'});
            // window.open(routeData.href, '_blank');
        },
        printNew(){
            window.open('api/reports/newcontracts')
            // let routeData = this.$router.resolve({name: 'New Contracts'});
            // window.open(routeData.href, '_blank');
        },
    },
    created () {
        // this.filterTableList('payments', this.date_from, this.date_to)
        this.$http.get('api/dashboard/index/'+this.payment_type,{
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
        })
        .then((response) => {  
            var data = response.data
                this.no_of_tenants = data.tenants[0].no_of_tenants
                this.no_of_contracts = data.contracts[0].no_of_contracts
                this.no_of_new_contracts = data.new_contracts[0].no_of_new_contracts
                this.no_of_expiring_contracts = data.expiring_contracts[0].no_of_expiring_contracts
                this.tables.contracts.items = data.pending_contracts
                this.paginations.contracts.totalRows = data.pending_contracts.length
                this.tables.adjustments.items = data.pending_adjustments
                this.paginations.adjustments.totalRows = data.pending_adjustments.length
                var cash = []
                var check = []
                var online = []

                data.payment_line.cash.forEach(element => {
                    cash.push(element.amount)
                });
                data.payment_line.check.forEach(element => {
                    check.push(element.amount)
                });
                data.payment_line.online.forEach(element => {
                    online.push(element.amount)
                });
                var line_data = [
                    {
                        label: 'Cash',
                        backgroundColor: this.convertHex('#E46651', 0.75),
                        data: cash
                    },
                    {
                        label: 'Check',
                        backgroundColor: this.convertHex('#00D8FF', 0.75),
                        data: check
                    },
                    {
                        label: 'Online',
                        backgroundColor: this.convertHex('#41B883', 0.75),
                        data: online
                    }
                ]
                this.line_data = line_data
        }).catch(error => {
            console.log(error)
        })
    }
}
</script>
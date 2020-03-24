<template>
<div>
        <notifications group="notification" />
<div v-show="showEntry === false" class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Cash Disbursement Journal
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="showEntry = true, clearJournal(), forms.cashdisbursement.fields.ref_type = 'CV'">
                    New Journal
                </b-button>
            </b-col>
            <b-col sm="6">

            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.cashdisbursements.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table borderless striped show-empty
            :fields="tables.cashdisbursements.fields"
            :items.sync="tables.cashdisbursements.items"
            :filter="filters.cashdisbursements.criteria"
            :current-page="paginations.cashdisbursements.currentPage"
            :per-page="paginations.cashdisbursements.perPage"
            @filtered="onFiltered($event,'cashdisbursements')"
            >
                <template v-slot:cell(row_data)="row">
                    <i
                    @click.stop="row.toggleDetails(),CashDisbursementDetails(row) " 
                    :class="row.detailsShowing ? 'fa fa-folder-open text-danger' : 'fa fa-folder text-success'"
                    ></i>
                </template>
                <template v-slot:row-details="row">
                    <b-row class="table-background-white">
                        <b-col sm="12" class="mt-1 mb-1">
                            <table  width="100%" class="background-white table-info" >
                                <tr>
                                    <td class="bold" width="15%">Date:</td>
                                    <td width="35%" >{{ row.item.date_txn}}</td>
                                    <td width="15%" class="bold">Reference: </td>
                                    <td width="35%" >{{ row.item.ref_type }} - {{ row.item.ref_no }}</td>
                                </tr>
                                <tr>
                                    <td class="bold" width="15%">TXN #:</td>
                                    <td width="35%">{{ row.item.txn_no}}</td>
                                    <td class="bold" width="15%">Payment Method:</td>
                                    <td>{{ row.item.payment_method}}</td>

                                </tr>
                                <tr>
                                    <td class="bold" width="15%">Particular:</td>
                                    <td :colspan="row.item.payment_method_id == '2' ?'1':'3' ">{{ row.item.particular}}</td>
                                    <td width="15%" :class="row.item.payment_method_id == '2' ?' bold ':'d-none'" >Check Number: </td>
                                    <td width="35%" :class="row.item.payment_method_id == '2' ?'':'d-none'">{{ row.item.check_no }}</td>
                                </tr>
                                <tr>
                                    <td width="15%" class="bold"> </td>
                                    <td width="35%" :colspan="row.item.payment_method_id == '2' ?'1':'3' "></td>
                                    <td width="15%" :class="row.item.payment_method_id == '2' ?' bold ':'d-none'" >Check Date: </td>
                                    <td width="35%" :class="row.item.payment_method_id == '2' ?'':'d-none'">{{ row.item.check_date }}</td>
                                </tr>
                            </table>
                            <table width="100%" class="table-accounts-bordered background-white table-accounts">
                                <b-thead>
                                    <b-tr >
                                        <b-th class="align-center" width="10%">Account #</b-th>
                                        <b-th>Account</b-th>
                                        <b-th width="">Memo</b-th>
                                        <b-th width="15%" class="align-right">Debit</b-th>
                                        <b-th width="15%" class="align-right">Credit</b-th>
                                    </b-tr>
                                </b-thead>
                                <b-tbody>
                                    <b-tr v-for="account in row.item.accounts" :key="account.journal_accounts_id">
                                        <b-td class="align-center">{{account.account_no}}</b-td>
                                        <b-td>{{account.account_title}}</b-td>
                                        <b-td>{{account.memo}}</b-td>
                                        <b-td class="align-right">{{formatNumber(account.dr_amount)}}</b-td>
                                        <b-td class="align-right">{{formatNumber(account.cr_amount)}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="2" class="bold">Remarks:</b-td>
                                        <b-td class="align-right bold" >Total:</b-td>
                                        <b-td class="align-right bold">{{formatNumber(row.item.dr_total)}}</b-td>
                                        <b-td class="align-right bold">{{formatNumber(row.item.cr_total)}}</b-td>
                                    </b-tr>
                                    <b-tr>
                                        <b-td colspan="5">{{ row.item.remarks}}</b-td>
                                    </b-tr>
                                </b-tbody>
                            </table>
                        </b-col>
                    </b-row> 
                    <br><i><small>End of {{row.item.txn_no }} </small></i>
                    <hr class="small-top">                  
                </template>
                <template v-slot:cell(particular)="data">  
                    <span data-toggle="tooltip" :title="data.item.particular">{{data.item.particular}}</span>
                </template>
                <template v-slot:cell(remarks)="data">  
                    <span data-toggle="tooltip" :title="data.item.remarks">{{data.item.remarks}}</span>
                </template>
                <template v-slot:cell(posted_by)="data">  
                    <span data-toggle="tooltip" :title="data.item.posted_by">{{data.item.posted_by}}</span>
                </template>
                <template v-slot:cell(status)="data">  
                    <span><i
                            :class="data.item.is_active == 1 ?'fa fa-check-circle-o text-success fa-lg': 'fa fa-times-circle-o text-danger fa-lg'">
                        </i>
                    </span>
                </template>
                <template v-slot:cell(action)="data">
                    <b-btn v-if="data.item.payment_method_id == 2" :size="'sm'" variant="success">
                        <i class="fa fa-window-maximize" title="Cheque"></i>
                    </b-btn>
                    <b-btn :size="'sm'" variant="primary" >
                        <i class="fa fa-print" title="Voucher"></i>
                    </b-btn>
                    <b-btn :size="'sm'" variant="danger" @click="$refs.canceljournal.setCancel(data.item)">
                        <b-icon-x></b-icon-x>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.cashdisbursements.currentPage"
                    :total-rows="paginations.cashdisbursements.totalRows"
                    :per-page="paginations.cashdisbursements.perPage"
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
<div v-show="showEntry" class="animated fadeIn">
    <b-card border-variant="default" class="text-left card-header-primary">
        <h5 slot="header">
            <span >Cash Disbursement | New
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h5>
        <b-form autocomplete="off">
            <b-row>
                <b-col sm="3">
                        <label class="required" for="date_txn">Date</label>
                        <date-picker  style="width:100%"
                            v-model="forms.cashdisbursement.fields.date_txn" 
                            lang="en" 
                            input-class="form-control mx-input"
                            format="MMMM DD, YYYY"
                            :clearable="false">
                        </date-picker>
                </b-col>
                <b-col sm="3">
                    <b-form-group>
                        <label for="txn_no">Transaction # (Auto)</label>
                        <b-form-input
                            ref="txn_no"
                            id="txn_no"
                            v-model="forms.cashdisbursement.fields.txn_no"
                            debounce="250"
                            type="text"
                            placeholder="TXN-YYYYMMDD-XXX"
                            readonly=""
                            >
                        </b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="2"></b-col>
                <b-col sm="4">
                    <b-form-group>
                        <label class="required"> Payment Method </label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Payment Method'"
                            v-model="forms.cashdisbursement.fields.payment_method_id"
                        >
                                <option v-for="payment in options.paymentmethods.items" :key="payment.payment_method_id" :value="payment.payment_method_id">{{payment.payment_method}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="3">
                    <b-form-group>
                        <label class="required"> Reference Type </label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Reference Type'"
                            v-model="forms.cashdisbursement.fields.ref_type"
                        >
                                <option value="CV">CV</option>
                                <option value="JV">JV</option>
                        </select2>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group>
                        <label>Reference No.(Auto)</label>
                        <b-form-input
                            v-model="forms.cashdisbursement.fields.ref_no"
                            debounce="250"
                            type="text"
                            placeholder="XXX"
                            readonly="">
                        </b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="2"></b-col>
                <b-col sm="4">
                    <b-form-group>
                        <label> Bank </label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Payment Method'"
                            v-model="forms.cashdisbursement.fields.bank_id"
                        >
                                <option v-for="banks in options.banks.items" :key="banks.bank_id" :value="banks.bank_id">{{banks.account_number}} - {{banks.bank_name}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="6">
                    <b-form-group>
                        <label class="required"> Departments </label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Department'"
                            v-model="forms.cashdisbursement.fields.department_id"
                        >
                                <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_name}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
                <b-col sm="2"></b-col>
                <b-col sm="2">
                    <label >Date</label>
                    <date-picker  style="width:100%"
                        v-model="forms.cashdisbursement.fields.check_date" 
                        lang="en" 
                        input-class="form-control mx-input"
                        format="MMMM DD, YYYY"
                        :clearable="false">
                    </date-picker>
                </b-col>
                <b-col sm="2">
                    <b-form-group>
                        <label>Check No.</label>
                        <b-form-input
                            v-model="forms.cashdisbursement.fields.check_no"
                            debounce="250"
                            type="text"
                        >
                        </b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>

                <b-col sm="6">
                <b-form-group>
                    <label class="required"> Particular </label>
                    <select2
                        :allowClear="false"
                        :placeholder="'Select Particular'"
                        v-model="forms.cashdisbursement.fields.particular_id"
                    >
                        <optgroup label="Suppliers">
                            <option v-for="supplier in options.journalsuppliers.items" :key="supplier.particular_id" :value="supplier.particular_id">{{supplier.supplier_name}}</option>
                        </optgroup>
                    </select2>
                </b-form-group>
                </b-col>
                <b-col sm="2"></b-col>
                <b-col sm="4">
                    <label> Check Amount </label>
                    <vue-autonumeric 
                        :class="'form-control text-right'"
                        v-model="forms.cashdisbursement.fields.amount" 
                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                    </vue-autonumeric>
                </b-col>

            </b-row>
            <b-icon-info-fill></b-icon-info-fill> <small><b>Journal Entries</b></small>
            <hr class="small-top">
            <b-row>
                <b-col sm="">
                    <b-table 
                        responsive
                        small borderless
                        :fields="tables.journalentries.fields"
                        :items.sync="tables.journalentries.items"
                        show-empty>
                            <template v-slot:cell(account_id)="data">
                                <select2
                                    ref="account_id"
                                    :allowClear="false"
                                    :placeholder="'Select Account Title'"
                                    v-model="data.item.account_id"
                                >
                                <option v-for="accounttitle in options.accounttitlesoptions.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                </select2>
                            </template>
                            <template v-slot:cell(memo)="data">
                                <b-form-input 
                                    placeholder=""
                                    v-model="data.item.memo">
                                </b-form-input>
                            </template>
                            <template v-slot:cell(dr_amount)="data">
                                <vue-autonumeric 
                                    :class="'form-control text-right'"
                                    v-model="data.item.dr_amount" 
                                    @input="zeroOpposite(data.index, 'dr')"
                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                </vue-autonumeric>
                            </template>
                            <template v-slot:cell(cr_amount)="data">
                                <vue-autonumeric 
                                    :class="'form-control text-right'"
                                    v-model="data.item.cr_amount" 
                                    @input="zeroOpposite(data.index, 'cr')"
                                    :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                                </vue-autonumeric>
                            </template>
                            <template v-slot:cell(action)="data" >
                                <b-btn :size="'sm'" variant="success" @click="addJournalEntry()">
                                   <b-icon-plus></b-icon-plus>
                                </b-btn>

                                <b-btn :size="'sm'" variant="danger" @click="removeJournalEntry(data.index)">
                                    <b-icon-x></b-icon-x>
                                </b-btn>
                            </template>

                            <template slot="bottom-row">
                            <td colspan="2" class="align-right"></td>
                            <td class="align-right"><b>{{ formatNumber(drTotal) }}</b></td>
                            <td class="align-right"><b>{{ formatNumber(crTotal) }}</b></td>
                            <td></td>
                            </template>
                    </b-table>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="12">
                    <b-form-group>
                        <label>Remarks </label>
                        <b-form-textarea
                            v-model="forms.cashdisbursement.fields.remarks"
                            debounce="250"
                            type="text"
                            rows="2"
                            no-resize
                            placeholder="Remarks">
                        </b-form-textarea>
                    </b-form-group> 
                </b-col>
            </b-row>
        </b-form>
        <template v-slot:footer>
            <b-row class="">
                <b-col sm="12">
                    <b-button 
                        :disabled="forms.cashdisbursement.isSaving" 
                        variant="success" 
                        @click="onCashDisbursementEntry">
                        <b-spinner v-if="forms.cashdisbursement.isSaving" small variant="light" label="Spinning"></b-spinner>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showEntry=false">Close</b-button>
                </b-col>
            </b-row>
        </template>
    </b-card>

</div>
    <canceljournal entity="cashdisbursement" table="cashdisbursements" primary_key="journal_id" ref="canceljournal"></canceljournal>
</div>
</template>

<script>
import canceljournal from '../modals/CancelJournal'
export default {
    name: 'cashdisbursements',
    components :{canceljournal
    },
    data(){
        return {
        entryMode: 'Add',
        showEntry: false,
        showModalCancel: false,
            options:{
                journalsuppliers:{
                    items: []
                },
                departments:{
                    items: []
                },
                accounttitlesoptions:{
                    items: []
                },
                paymentmethods:{
                    items: []
                },
                banks:{
                    items: []
                },
            },
            forms :{
                cashdisbursement :{
                    isSaving : false,
                    fields:{
                        txn_no: null,
                        date_txn : new Date(),
                        department_id : null,
                        particular_id : null,
                        ref_type : null,
                        ref_no : null,
                        remarks : '',
                        check_date : '',
                        check_no : '',
                        payment_method_id: null,
                        bank_id: null,
                        ammount: 0.00,
                        journalentry : [],
                    }
                }
            },
            tables: {
                cashdisbursements :{
                    fields:[
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle',
                            thStyle: {width: '2%'}
                        },
                        {
                            key:'txn_no',
                            label: 'Transaction No',
                            thStyle: {width: '15%'},
                            sortable: true
                        },
                        {
                            key:'reference_no',
                            label: 'Voucher No',
                            thStyle: {width: '10%'},
                            sortable: true
                        },
                        {
                            key:'particular',
                            label: 'Particular',
                            thStyle: {width: '25%'},
                            tdClass: 'ellipsis',
                            sortable: true

                        },
                        {
                            key:'payment_method',
                            label: 'Method',
                            thStyle: {width: '10%'},
    
                        },
                        {
                            key:'date_txn',
                            label: 'Date',
                            thStyle: {width: '8%'},

                        },
                        {
                            key:'posted_by',
                            label: 'Posted By',
                            tdClass: 'ellipsis',
                            thStyle: {width: '13%'},

                        },
                        {
                            key:'status',
                            label:'Status',
                            tdClass: 'align-center',
                            thStyle: {width: '5%'},
                        },
                        {
                            key:'action',
                            label:'Action',
                            tdClass: 'align-right',
                            thClass: 'align-right',
                            thStyle: {width: '12%'},
                            
                        }

                    ],
                     items: [],
                },
                journalentries:{
                    items: [
                        {
                            account_id: null,
                            dr_amount: 0.00,
                            cr_amount: 0.00,
                            memo: '',
                        },
                        {
                            account_id: null,
                            dr_amount: 0.00,
                            cr_amount: 0.00,
                            memo: '',
                        },
                    ],
                    fields: [
                        {
                            key:'account_id',
                            label: 'Account',
                            thStyle: {width: '40%'},
                            sortable: false
                        }, 
                        {
                            key:'memo',
                            label: 'Memo',
                            thStyle: {width: '20%'},
                            sortable: false
                        }, 
                        {
                            key:'dr_amount',
                            label: 'Debit',
                            thClass: 'align-right',
                            thStyle: {width: '15%'},
                            sortable: false
                        }, 
                        {
                            key:'cr_amount',
                            label: 'Credit',
                            thClass: 'align-right',
                            thStyle: {width: '15%'},
                            sortable: false
                        }, 
                        {
                            key: 'action',
                            label: 'Action',
                            thClass: 'text-center',
                            tdClass: 'align-middle text-center',
                            thStyle: {width: '10%'},
                        }
                    ]
                }
            }, // END OF TABLES
            filters: {
                cashdisbursements: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                cashdisbursements: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('cashdisbursements');
        this.fillOptionsList('journalsuppliers');
        this.fillOptionsList('departments');
        this.fillOptionsList('accounttitlesoptions');
        this.fillOptionsList('paymentmethods');
        this.fillOptionsList('banks');
    }, // END OF CREATED
    methods:{
        async CashDisbursementDetails(row){
            if(row.detailsShowing == true){
                return
            }
            await this.$http.get('/api/cashdisbursement_details/' + row.item.journal_id, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                      'Content-Type' : 'multipart/form-data'
                  }
            })
            .then((response) => {
                row.item.accounts = response.data.accounts
                row.item.dr_total = response.data.dr_total
                row.item.cr_total = response.data.cr_total
            })
        },
        clearJournal(){
            this.clearFields('cashdisbursement')
            this.forms.cashdisbursement.fields.date_txn = new Date()
            this.forms.cashdisbursement.fields.check_date = ''
            this.tables.journalentries.items = [], 
            this.addJournalEntry(), this.addJournalEntry()
        },
        zeroOpposite(index,type){
            var closest = this.tables.journalentries.items[index];
                if(type == 'dr' && closest.cr_amount != 0 && closest.dr_amount != 0){
                    closest.cr_amount = 0
                }else if (type == 'cr' && closest.dr_amount != 0 && closest.cr_amount != 0){
                    closest.dr_amount = 0
                }
        },
        addJournalEntry(){
            this.tables.journalentries.items.push({
                account_id: null,
                dr_amount: 0.00,
                cr_amount: 0.00,
                memo: '',
            })
        },
        removeJournalEntry(index){
            var rowCount = this.tables.journalentries.items.length;
                if(rowCount  == 1){
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!',
                        text: 'Cannot Remove All Rows.'
                    })
                }else{
                    this.tables.journalentries.items.splice(index, 1)
                }
            
        },
        async onCashDisbursementEntry(){
            if(this.forms.cashdisbursement.fields.date_txn == '' || this.forms.cashdisbursement.fields.date_txn ==  null){
                this.$notify({ type: 'error', group: 'notification', title: '<b>Date Error!</b>', text: 'Please make sure transaction date is valid!' })
            }else {
                var checkDate = { 'date_txn': this.forms.cashdisbursement.fields.date_txn}
                if(await this.CheckJournalEntries()){ 
                    if( await this.checkPeriod(checkDate) == true){ // MEANS period is not accepted
                        this.$notify({ type: 'error', group: 'notification', title: '<b>Accounting Period is Closed!</b>', text: 'Please make sure transaction date is valid!' })
                    }
                    else{
                        this.forms.cashdisbursement.fields.journalentry = this.tables.journalentries.items;
                        await this.createEntity('cashdisbursement', false, 'cashdisbursements')
                    }
                }
            }
        },
        CheckJournalEntries(){
            var stat = true  
                var tblft = this.tables.journalentries.items.filter(ft => ft.account_id == null )
                if(tblft.length > 0){ // CHECK IF ALL ACCOUNT IDS ARE NOT ZERO
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: 'Please select an Account Title.' })  
                    stat = false; return false;
                }
                if(Number(this.drTotal) != Number(this.crTotal)){ // CHECK IF TOTALS NOT EQUAL
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: 'Please make sure Debit and Credit amounts are equal.' }) 
                    stat = false; return false;
                }
                if((Number(this.drTotal) + Number(this.crTotal)) == 0){ // CHECK IF TOTALS ARE ZERO
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: 'Please make sure Debit and Credit total is not equal to zero.' })  
                    stat = false ; return false;
                }

            return stat;
        },
    }, // END OF METHODS
    computed :{
        drTotal() { 
            return this.tables.journalentries.items.reduce((accum, item) => { 
                return accum + item.dr_amount }, 0)
        },
        crTotal() {
            return this.tables.journalentries.items.reduce((accum, item) => { 
                return accum + item.cr_amount }, 0)
        },
    }
}// END OF EXPORT 
</script>
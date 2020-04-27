<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Petty Cash Journal
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="3">
                <b-form-group>
                    <select2
                        :allowClear="false"
                        :placeholder="'Select Department'"
                         @input="filterPettyCash()"
                        v-model="forms.pettycashjournal.fields.department_id"
                    >
                            <option value="0">SHOW ALL TRANSACTIONS</option>
                            <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_name}}</option>
                    </select2>
                </b-form-group>
            </b-col>
            <b-col sm="3">
                <date-picker 
                    @input="filterPettyCash()"
                    v-model="as_of_date" 
                    lang="en" 
                    input-class="form-control mx-input"
                    format="MMMM DD, YYYY"
                    :clearable="false">
                </date-picker>
            </b-col>
            <b-col sm="2">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.pettycashjournals.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.pettycashjournals.fields"
            :items.sync="tables.pettycashjournals.items"
            :filter="filters.pettycashjournals.criteria"
            :current-page="paginations.pettycashjournals.currentPage"
            :per-page="paginations.pettycashjournals.perPage"
            @filtered="onFiltered($event,'pettycashjournals')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="setUpdate(data)"
                         :disabled="forms.pettycashjournal.fields.department_id == 0 ? true : false"
                    >
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="setDelete(data.item.journal_id)"
                         :disabled="forms.pettycashjournal.fields.department_id == 0 ? true : false"
                    >
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>
                <template v-slot:cell(amount)="data">
                    {{formatNumber(data.item.amount )}}
                </template>
                <template v-slot:cell(remarks)="data">  
                    <span data-toggle="tooltip" :title="data.item.remarks">{{data.item.remarks}}</span>
                </template>
            </b-table>
        </b-row>
        <b-row>

        <b-table-simple width="100%" class="table table-borderless table-summary">

            <b-tbody>
                <b-tr>
                    <b-td width="90%" class="align-right bold">Unreplenished:</b-td>
                    <b-td class="bold align-right">
                    <vue-autonumeric 
                        readonly=""
                        :class="'form-control text-right '"
                        v-model="unreplenished_amount" 
                        :options="{modifyValueOnWheel: false, emptyInputBehavior: 0}">
                    </vue-autonumeric>
                    </b-td>
                </b-tr>
                <b-tr>
                    <b-td width="90%" class="align-right bold">Remaining:</b-td>
                    <b-td  class="bold align-right">
                    <vue-autonumeric 
                        readonly =""
                        :class="'form-control text-right '"
                        v-model="remaining_amount" 
                        :options="{modifyValueOnWheel: false, emptyInputBehavior: 0}">
                    </vue-autonumeric>
                    </b-td>
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="6" class="my-1 mb-0">
                    <b-button class="mb-2" variant="success" 
                        @click="setAdd()"
                        :disabled="forms.pettycashjournal.fields.department_id == 0 ? true : false"
                        >
                        New Expense
                    </b-button>
                    <b-button class="mb-2" variant="primary" 
                        :disabled="forms.pettycashjournal.fields.department_id == 0 ? true : false"
                        @click="setReplenish()"
                        >
                        Replenish
                    </b-button>
                </b-col>

                <b-col sm="6" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.pettycashjournals.currentPage"
                    :total-rows="paginations.pettycashjournals.totalRows"
                    :per-page="paginations.pettycashjournals.perPage"
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

<b-modal 
        v-model="showModalEntry"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        @shown="focusElement('department_name')"
        size="lg"
    >
        <div slot="modal-title">
            Petty Cash Journal  <small> | {{entryMode}}</small>
        </div>
        <b-col lg=12>
            <b-row>

                <b-col sm="6">
                    <b-form-group>
                        <label class="required"> Supplier </label>
                    <select2
                        :allowClear="false"
                        :placeholder="'Select Particular'"
                        v-model="forms.pettycashjournal.fields.supplier_id"
                    >
                        <optgroup label="Suppliers">
                            <option v-for="supplier in options.minimalsuppliers.items" :key="supplier.supplier_id" :value="supplier.supplier_id">{{supplier.supplier_name}}</option>
                        </optgroup>
                    </select2>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <b-form-group>
                        <label for="ref_no">Transaction # (Auto)</label>
                        <b-form-input
                            ref="ref_no"
                            id="ref_no"
                            v-model="forms.pettycashjournal.fields.ref_no"
                            debounce="250"
                            type="text"
                            placeholder="TXN-YYYYMMDD-XXX"
                            readonly=""
                            >
                        </b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <label class="required" for="date_txn">Date</label>
                    <date-picker  style="width:100%"
                        v-model="forms.pettycashjournal.fields.date_txn" 
                        lang="en" 
                        input-class="form-control mx-input"
                        format="MMMM DD, YYYY"
                        :clearable="false">
                    </date-picker>
                </b-col>

            </b-row>
            <b-row>
                <b-col sm="9">
                    <b-form-group>
                        <label class="required">Expense Account</label>
                        <select2
                            ref="account_id"
                            :allowClear="false"
                            :placeholder="'Select Expense Account'"
                            v-model="forms.pettycashjournal.fields.account_id"
                        >
                        <option v-for="accounttitle in options.pettycashaccounts.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
                <b-col sm="3">
                    <label class="required">  Amount </label>
                    <vue-autonumeric 
                        :class="'form-control text-right'"
                        v-model="forms.pettycashjournal.fields.amount" 
                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}">
                    </vue-autonumeric>
                </b-col>
            </b-row>
            <b-row>
                <b-col sm="12">
                    <b-form-group>
                        <label>Remarks </label>
                        <b-form-textarea
                            v-model="forms.pettycashjournal.fields.remarks"
                            debounce="250"
                            type="text"
                            rows="2"
                            no-resize
                            placeholder="Remarks">
                        </b-form-textarea>
                    </b-form-group> 
                </b-col>
            </b-row>
        </b-col>
        <div slot="modal-footer">
                    <b-button 
                        :disabled="forms.pettycashjournal.isSaving" 
                        variant="success" 
                        @click="onPettyCashEntry">
                        <b-spinner v-if="forms.pettycashjournal.isSaving" small variant="light" label="Spinning"></b-spinner>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
            <b-button variant="danger" @click="showModalEntry=false">Close</b-button>            
        </div>
    </b-modal>

    <b-modal 
        v-model="showModalDelete"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
    >
        <div slot="modal-title">
            Delete Expense
        </div>
        <b-col lg="12">
            Are you sure you want to delete this expense?
        </b-col>
        <div slot="modal-footer">
                <b-button :disabled="forms.pettycashjournal.isDeleting" variant="success" @click="onPettyCashDelete">
                    <icon v-if="forms.pettycashjournal.isDeleting" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Accept
                </b-button>
            <b-button variant="danger" @click="showModalDelete=false, selectedID = 0">Close</b-button>            
        </div>
    </b-modal>

    <b-modal 
        v-model="showModalReplenish"
        :noCloseOnEsc="true"
        :noCloseOnBackdrop="true"
        size="lg"
    >
        <div slot="modal-title">
            Confirm Replenishment
        </div>
        <b-col lg=12>
            <b-form autocomplete="off">
                <b-row>
                    <b-col sm="6">
                        <b-form-group>
                            <label class="required"> Payment Method </label>
                            <select2
                                :allowClear="false"
                                :placeholder="'Select Payment Method'"
                                v-model="forms.replenishment.fields.payment_method_id"
                            >
                                    <option v-for="payment in options.paymentmethods.items" :key="payment.payment_method_id" :value="payment.payment_method_id">{{payment.payment_method}}</option>
                            </select2>
                        </b-form-group>
                    </b-col>
                    <b-col sm="6">
                    <b-form-group>
                        <label class="required"> Particular </label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Particular'"
                            v-model="forms.replenishment.fields.particular_id"
                        >
                            <optgroup label="Suppliers">
                                <option v-for="supplier in options.journalsuppliers.items" :key="supplier.particular_id" :value="supplier.particular_id">{{supplier.supplier_name}}</option>
                            </optgroup>
                        </select2>
                    </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col sm="4">
                        <b-form-group>
                            <label> Bank </label>
                            <select2
                                :allowClear="false"
                                :placeholder="'Select Bank Account'"
                                v-model="forms.replenishment.fields.bank_id"
                            >
                                    <option v-for="banks in options.banks.items" :key="banks.bank_id" :value="banks.bank_id">{{banks.account_number}} - {{banks.bank_name}}</option>
                            </select2>
                        </b-form-group>
                    </b-col>
                    <b-col sm="4">
                        <label >Check Date</label>
                        <date-picker  style="width:100%"
                            v-model="forms.replenishment.fields.check_date" 
                            lang="en" 
                            input-class="form-control mx-input"
                            format="MMMM DD, YYYY"
                            :clearable="false">
                        </date-picker>
                    </b-col>
                    <b-col sm="4">
                        <b-form-group>
                            <label >Check Number</label>
                            <b-form-input
                                v-model="forms.replenishment.fields.check_no"
                                debounce="250"
                                type="text"
                                >
                            </b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                <b-col sm="12">
                    <b-form-group>
                        <label class="required">Credit Entry Account Title</label>
                        <select2
                            :allowClear="false"
                            :placeholder="'Select Credit Entry Account'"
                            v-model="forms.replenishment.fields.account_id_credit"
                        >
                        <option v-for="accounttitleop in options.accounttitlesoptions.items" :key="accounttitleop.account_id" :value="accounttitleop.account_id">{{accounttitleop.account_title}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
                </b-row>
            </b-form>
        </b-col>
        <div slot="modal-footer">
                <b-button :disabled="forms.replenishment.isSaving" variant="success" @click="onPettyCashReplenishment">
                    <icon v-if="forms.replenishment.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Accept
                </b-button>
            <b-button variant="danger" @click="showModalReplenish=false, selectedID = 0">Close</b-button>            
        </div>
    </b-modal>


</div>
</template>
<style scoped>
input[readonly]{
    background-color:transparent;
    border: 0;
    font-size: 1em;
}
.table-summary td{
    padding: 0px!important;
}
.table-summary{
    border:none;
}
</style>
<script>
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'pettycashjournals',
    components :{
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
        showModalReplenish : false,
        unreplenished_amount: 0,
        remaining_amount:0,
        selectedID: 0,
        as_of_date: moment().format('YYYY-MM-DD'),
            options:{
                pettycashaccounts: { items:[]},
                minimalsuppliers: { items:[]},
                departments: { items:[]},
                paymentmethods:{ items: [] },
                banks:{ items: [] },
                accounttitlesoptions : { items: []},
                journalsuppliers : { items: []},

            },
            tables: {
                pettycashjournals :{
                    fields:[
                        {
                            key:'date_txn',
                            label: 'Txn Date',
                            sortable: true
                        },
                        {
                            key:'txn_no',
                            label: 'Transaction No'
                        },
                        {
                            key:'ref_no',
                            label: 'Reference No'
                        },
                        {
                            key:'ref_no',
                            label: 'Reference No'
                        },
                        {
                            key:'supplier_name',
                            label: 'Particular'
                        },
                        {
                            key:'department_name',
                            label: 'Department '
                        },
                        {
                            key:'remarks',
                            label: 'remarks',
                            tdClass: 'ellipsis',
                            thStyle: {width: '10%'},
                        },
                        {
                            key:'amount',
                            label:'Amount',
                            thStyle: {width: '10%'},
                            thClass:"align-right",
                            tdClass:"align-right"
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
            forms:{
                pettycashjournal:{
                    isDeleting: false,
                    isSaving:false,
                    fields :{
                        supplier_id : null,
                        department_id : 1, // ON LOAD ADMIN WILL BE THE DEFAULT
                        account_id : null,
                        remarks : '',
                        ref_no : '',
                        amount : 0.00,
                        date_txn : new Date(),
                    }

                },
                replenishment :{
                    isSaving: false,
                    fields:{
                        payment_method_id: null,
                        check_date : new Date(),
                        check_no : '',
                        bank_id : null,
                        account_id_credit : null,
                        on_or_before : new Date(),
                        department_id : null,
                        particular_id : null,
                    }
                }
            },
            filters: {
                pettycashjournals: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                pettycashjournals: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.filterTableList('pettycashjournals', this.as_of_date, this.forms.pettycashjournal.fields.department_id) // FIRST DEPARTMENT IS ALWAYS ADMIN
        this.fillOptionsList('pettycashaccounts');
        this.fillOptionsList('minimalsuppliers');
        this.fillOptionsList('departments');
        this.fillOptionsList('paymentmethods');
        this.fillOptionsList('banks');
        this.fillOptionsList('accounttitlesoptions');
        this.fillOptionsList('journalsuppliers');
        this.refreshPettyCashTotals();
    }, // END OF CREATED
    methods:{
        async filterPettyCash(){
            var as_of_date = this.moment(this.as_of_date, 'YYYY-MM-DD')
            await this.filterTableList('pettycashjournals', as_of_date, this.forms.pettycashjournal.fields.department_id)
            await this.refreshPettyCashTotals()
        },
        clearJournal(){
            this.forms.pettycashjournal.fields.supplier_id = null,
            this.forms.pettycashjournal.fields.account_id = null,
            this.forms.pettycashjournal.fields.remarks = '',
            this.forms.pettycashjournal.fields.ref_no = '',
            this.forms.pettycashjournal.fields.amount = 0.00,
            this.forms.pettycashjournal.fields.date_txn = new Date()
        },
        setAdd(){
            if(this.remaining_amount <= 0 ) {
                this.$notify({
                    type: 'error',group: 'notification',title: 'Error!',text: 'Petty cash amount must not be zero in your chosen department..'
                })
            }else{
                this.showModalEntry = true
                this.entryMode='Add'
                this.clearJournal()

            }
        },
        setReplenish(){
            this.forms.replenishment.fields.payment_method_id =  1;
            this.forms.replenishment.fields.check_date = new Date();
            this.forms.replenishment.fields.check_no = '';
            this.forms.replenishment.fields.bank_id = null;
            this.forms.replenishment.fields.account_id_credit = null;
            this.forms.replenishment.fields.particular_id = null;
            this.showModalReplenish = true
            this.forms.replenishment.fields.on_or_before = this.moment(this.as_of_date, 'YYYY-MM-DD');
            this.forms.replenishment.fields.department_id = this.forms.pettycashjournal.fields.department_id;
        },
        setUpdate(data){
            this.row = data.item
            console.log(this.row);
            this.clearJournal();
            this.fillEntityForm('pettycashjournal', data.item.journal_id, 'showModalEntry')
            this.entryMode='Edit'
        },
        setDelete(journal_id){
            this.selectedID = journal_id
            this.showModalDelete = true;

        },
        async onPettyCashReplenishment(){
            this.forms['replenishment'].isSaving = true
                var as_of_date = this.moment(this.as_of_date, 'YYYY-MM-DD')
                await this.$http.get('/api/pettycashtotals/'+as_of_date +'/'+this.forms.pettycashjournal.fields.department_id,{
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
                    .then((response) => {
                        
                        if(response.data.unreplenished_amount <= 0){
                            this.forms['replenishment'].isSaving = false
                            this.$notify({
                            type: 'error',group: 'notification',title: 'Error!',text: 'No Transactions to process.'
                            })
                        }else {


                        this.$http.post('api/pettycashreplenishment', this.forms['replenishment'].fields,{
                            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }
                        })
                        .then((response) => {  
                            this.forms['replenishment'].isSaving = false
                            this.$notify({
                            type: 'success',group: 'notification',title: 'Success!',text: 'Petty Cash successfully replenished.'
                            })
                            this.showModalReplenish = false;
                            this.refreshPettyCashTotals()
                            this.filterPettyCash()
                        }).catch(error => {
                            this.forms['replenishment'].isSaving = false
                            if (!error.response) return
                            const errors = error.response.data.errors
                            var a = 0
                            for (var key in errors) {
                                if(a == 0){
                                this.$notify({type: 'error',group: 'notification',title: 'Error!',text: errors[key][0]
                                })
                                }
                                a++
                            }
                        })



                        }
                    })
                    .catch(error => { if (!error.response) return console.log(error)
                    })


        },
        async onPettyCashEntry(){
            if(this.entryMode == 'Add'){
                await this.refreshPettyCashTotals()
                if(this.remaining_amount < this.forms.pettycashjournal.fields.amount ) {
                    this.$notify({
                        type: 'error',group: 'notification',title: 'Error!',text: 'Expense amount must not exceed petty cash amount .'
                    })
                }else {
                    this.forms['pettycashjournal'].isSaving = true
                    await this.$http.post('api/pettycashjournal', this.forms['pettycashjournal'].fields,{
                        headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }
                    })
                    .then((response) => {  
                        this.forms['pettycashjournal'].isSaving = false
                        this.clearJournal();
                        this.$notify({
                        type: 'success',group: 'notification',title: 'Success!',text: 'The record has been successfully created.'
                        })
                        this.tables['pettycashjournals'].items.unshift(response.data.data)
                        this.paginations['pettycashjournals'].totalRows++
                        this.showModalEntry = false
                        this.refreshPettyCashTotals()
                    }).catch(error => {
                        this.forms['pettycashjournal'].isSaving = false
                        if (!error.response) return
                        const errors = error.response.data.errors
                        var a = 0
                        for (var key in errors) {
                            if(a == 0){
                            this.$notify({type: 'error',group: 'notification',title: 'Error!',text: errors[key][0]
                            })
                            }
                            a++
                        }
                    })
                }
            }
            else{
                var as_of_date = this.moment(this.as_of_date, 'YYYY-MM-DD')
                await this.$http.get('/api/pettycashtotalsexcept/'+as_of_date +'/'+this.forms.pettycashjournal.fields.department_id+'/'+ this.forms.pettycashjournal.fields.journal_id,{
                    headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
                    .then((response) => {
                        
                    if(response.data.balance_amount < this.forms.pettycashjournal.fields.amount ) { // CHECK AMOUNT WITHOUT CURRENT JOURNAL TO CHECK 
                        this.$notify({
                            type: 'error',group: 'notification',title: 'Error!',text: 'Expense amount must not exceed petty cash amount .'
                        })
                    }else { // AMOUNT IS OK
                        this.forms['pettycashjournal'].isSaving = true
                        this.$http.put('api/pettycashjournal/' + this.forms['pettycashjournal'].fields['journal_id'], this.forms['pettycashjournal'].fields ,{
                        headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
                        .then((response) => {
                            this.forms['pettycashjournal'].isSaving = false
                            this.$notify({ type: 'success', group: 'notification', title: 'Success!', text: 'The record has been successfully updated.'})
                            for (var key in response.data.data) {
                                this.row[key] = response.data.data[key]
                            }
                            this.showModalEntry = false
                            this.refreshPettyCashTotals()
                        }).catch(error => {
                        this.forms['pettycashjournal'].isSaving = false
                        if (!error.response) return
                        const errors = error.response.data.errors
                        var a = 0
                        for (var key in errors) {
                            if(a == 0){
                                this.focusElement(key, is_tab)
                                this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]
                            })
                            }
                            a++
                        }
                        }) // END OF AXIOS UPDATE
                    } // END OF IF ELSE 

                })
                .catch(error => { if (!error.response) return console.log(error)
                }) // END OF AXIOS GET TOTALS EXCEPT            
        }
        },
        async onPettyCashDelete(){
          this.forms['pettycashjournal'].isDeleting = true
          this.$http.put('api/pettycashjournal/delete/' + this.selectedID , this.forms['pettycashjournal'].fields  ,{
              headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }
            })
            .then((response) => {
                this.forms['pettycashjournal'].isDeleting = false
                this.$notify({type: 'success',group: 'notification',title: 'Success!',text: 'The record has been deleted.'
                })
                const index = this.tables['pettycashjournals'].items.findIndex(item => item['journal_id'] === response.data.data['journal_id'])
                this.$delete(this.tables['pettycashjournals'].items, index)
                this.paginations['pettycashjournals'].totalRows--
                this.showModalDelete = false
                this.refreshPettyCashTotals()
            }).catch(error => {
              this.forms['pettycashjournal'].isDeleting = false
              if (!error.response) return
              const errors = error.response.data.errors
              console.log(errors)
            })

        },
        async refreshPettyCashTotals(){
            var as_of_date = this.moment(this.as_of_date, 'YYYY-MM-DD')
            await this.$http.get('/api/pettycashtotals/'+as_of_date +'/'+this.forms.pettycashjournal.fields.department_id,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
                .then((response) => {
                    if(response.data.balance_amount == null){ this.remaining_amount = 0;  } else { this.remaining_amount = response.data.balance_amount; }
                    if(response.data.unreplenished_amount == null){ this.unreplenished_amount = 0;  } else { this.unreplenished_amount = response.data.unreplenished_amount; }
                })
                .catch(error => { if (!error.response) return console.log(error)
                })
        },
    } // END OF METHODS
}// END OF EXPORT 
</script>
<template>
    <div>
        <b-modal
            v-model="showModalPeriod"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('period_id')">
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
                            @input="getPeriodInfo">
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
            <b-row v-show="type=='billing'">
                <b-col lg=12>
                    <b-form-group>
                        <label>Department</label>
                        <select2
                            ref="department_id"
                            :allowClear="false"
                            :placeholder="'Select Department'"
                            v-model="forms.period.fields.department_id"
                        >
                            <option value="0">All Departments</option>
                            <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_desc}}</option>
                        </select2>
                    </b-form-group>
                </b-col>
            </b-row>
            <div slot="modal-footer">
                <b-button variant="success" @click="showList()">
                    <i class="fa fa-check"></i>
                    OK
                </b-button>
                <b-button variant="danger" @click="showModalPeriod=false">Close</b-button>            
            </div>
        </b-modal>
    </div>
</template>
<script>
export default {
    name: 'billingperiodfilter',
    props: ['type'],
    data() {
        return {
            showModalPeriod: true,
            options: {
                periods: {
                    items: []
                },
                departments: {
                    items: []
                },
            },
            forms: {
                period: {
                    fields: {
                        period_id: null,
                        period_due_date: null,
                        app_year: null,
                        month_id: null,
                        month_name: null,
                        department_id: 0,
                        is_sent: null
                    }
                }
            }
        }
    },
    methods:{
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
        showList(){
            //if parent is billing.vue
            if(this.type == "billing"){
                this.$parent.month_name = this.forms.period.fields.month_name
                this.$parent.app_year = this.forms.period.fields.app_year

                if(this.forms.period.fields.department_id != 0){
                    this.$parent.department_name = this.options.departments.items.filter(i => i.department_id == this.forms.period.fields.department_id)[0].department_desc
                }
                else{
                    this.$parent.department_name = "All Departments"
                }
                if(this.forms.period.fields.period_id != null && this.forms.period.fields.department_id != null){
                    this.$parent.filterTableList('billings', this.forms.period.fields.period_id, this.forms.period.fields.department_id)
                    this.$parent.showList = true
                    this.showModalPeriod = false
                }
            }
            //if parent is adjustment.vue
            else if(this.type == "adjustment"){
                this.$parent.month_name = this.forms.period.fields.month_name
                this.$parent.app_year = this.forms.period.fields.app_year

                if(this.forms.period.fields.period_id != null){
                    this.$http.get('/api/adjustments/' + this.forms.period.fields.period_id ,{
                        headers: {
                                Authorization: 'Bearer ' + localStorage.getItem('token')
                            }
                        })
                        .then((response) => {
                            const records = response.data
                            this.$parent.tables.adjustments.items = records.data
                            this.$parent.paginations.adjustments.totalRows = records.data.length
                        }).catch(error => {
                            if (!error.response) return
                            console.log(error)
                        })
                    this.$parent.showList = true
                    this.showModalPeriod = false
                }
            }
            //if parent is chargeslip.vue
            else {
                this.$parent.month_name = this.forms.period.fields.month_name
                this.$parent.app_year = this.forms.period.fields.app_year
                if(this.forms.period.fields.period_id != null){
                    this.$parent.filterTableList('chargeslips', this.forms.period.fields.app_year, this.forms.period.fields.month_id)
                    this.$parent.showList = true
                    this.showModalPeriod = false
                }
            }
        },
        // checkIfSent(){
        //     if(this.forms.period.fields.is_sent == 1){
        //         this.$notify({
        //             type: 'error',
        //             group: 'notification',
        //             title: 'Error',
        //             text: "This billing period was already locked. You can't add, edit and delete billing."
        //         })
        //         return true
        //     }
        //     return false
        // }
    },
    created() {
        this.fillOptionsList('periods')
        if(this.type == "billing"){
            this.fillOptionsList('departments')
        }
    }
}
</script>
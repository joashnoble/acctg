<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('period_start_date')">
            
            <div slot="modal-title"> <!-- modal title -->
                Billing Period Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('period')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="period_start_date">Period Start Date</label>
                        <date-picker 
                            ref="period_start_date"
                            id="period_start_date"
                            v-model="forms.period.fields.period_start_date"
                            lang="en" 
                            input-class="form-control  "
                            format="MMMM DD, YYYY"
                            :clearable="false">
                        </date-picker>
                    </b-form-group>

                    <b-form-group>
                        <label class="required" for="period_end_date">Period End Date</label>
                        <date-picker 
                            ref="period_end_date"
                            id="period_end_date"
                            v-model="forms.period.fields.period_end_date"
                            lang="en" 
                            input-class="form-control  "
                            format="MMMM DD, YYYY"
                            :clearable="false">
                        </date-picker>
                    </b-form-group>

                    <b-form-group>
                        <label class="required">Applicable Month </label>
                        <select2
                            ref="month_id"
                            :allowClear="false"
                            :placeholder="'Select Applicable Month'"
                            v-model="forms.period.fields.month_id"
                        >
                            <option v-for="month in options.months.items" :key="month.month_id" :value="month.month_id">{{month.month_name}}</option>
                        </select2>
                    </b-form-group>

                    <b-form-group>
                        <label class="required" for="app_year">Applicable Year</label>
                        <date-picker 
                            ref="app_year"
                            id="app_year"
                            v-model="year"
                            lang="en" 
                            input-class="form-control  "
                            format="YYYY"
                            type="year"
                            :clearable="false">
                        </date-picker>
                    </b-form-group>

                    <b-form-group>
                        <label class="required" for="period_due_date">Period Due Date</label>
                        <date-picker 
                            id="period_due_date"
                            v-model="forms.period.fields.period_due_date"
                            lang="en" 
                            input-class="form-control  "
                            format="MMMM DD, YYYY"
                            :clearable="false">
                        </date-picker>
                    </b-form-group>

                </b-form>
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.period.isSaving" variant="success" @click="onPeriodEntry">
                    <icon v-if="forms.period.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>
            </div> <!-- modal footer buttons -->

        </b-modal>
    </div> <!-- modal div -->
</template>
<script>
export default {
    name: 'billingperiodentry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false, //if true show modal
            options:{
                months: {
                    items: []
                }
            },
            forms:{
                period : {
                    isSaving: false,
                    fields: {
                        period_id: null,
                        period_start_date: null,
                        period_end_date: null,
                        month_id: null,
                        app_year: null,
                        period_due_date: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onPeriodEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('period', true, 'periods', 'billingperiodentry')
                }
                else{
                    this.$parent.updateEntityRef('period', 'period_id', true, this.row, 'billingperiodentry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('period', 'showModalEntry', 'periods', this.type, 'period_id', 'billingperiodentry')
            }
        },
        setUpdate(data){
            this.row = data.item
            this.fillEntityForm('period', data.item.period_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
    computed: {
        year:{
            get: function(){
                if(this.forms.period.fields.app_year != null && this.forms.period.fields.app_year != 0){
                    if(typeof this.forms.period.fields.app_year == 'number'){
                        console.log(this.forms.period.fields.app_year)
                        return moment(this.forms.period.fields.app_year + '-01' + '-01').format("MMMM DD, YYYY")
                    }
                    else{
                        return moment(this.forms.period.fields.app_year).format("MMMM DD, YYYY")
                    }
                }
                else{
                    this.forms.period.fields.app_year = null
                    return null
                }
                
            },
            set: function(newValue){
                this.forms.period.fields.app_year = newValue
            }
        },
    },
    created(){
        this.fillOptionsList('months')
    }
}
</script>
<style>
</style>

<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <div>
            <b-modal
                @hidden="$router.go(-1)"
                v-model="showModal"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                @shown="focusElement('date_to')"
            >
                <div slot="modal-title">
                    Collection Report
                </div>
                
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                            <label>Date From</label>
                            <date-picker 
                                ref="date_from"
                                id="date_from"
                                v-model="date_from"
                                lang="en" 
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-form-group>
                            <label>Date To</label>
                            <date-picker 
                                ref="date_to"
                                id="date_to"
                                v-model="date_to"
                                lang="en" 
                                input-class="form-control mx-input"
                                format="MMMM DD, YYYY"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-form-group>
                            <label>Department</label>
                            <select2
                                ref="department_id"
                                id="department_id"
                                v-model="department_id"
                                :allowClear="false"
                                :placeholder="'Select Department'">
                                <option value="0">All Departments</option>
                                <option v-for="department in options.departments.items" :key="department.department_id" :value="department.department_id">{{department.department_desc}}</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                             
                            <b-form-radio-group 
                                ref="deposit_status"
                                id="is_auto"
                                v-model="type"
                                >
                                <b-form-radio value="0">All</b-form-radio>
                                <b-form-radio value="1">Payment</b-form-radio>
                                <b-form-radio value="2">Withholding</b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-btn 
                        @click="previewReport"
                        variant="success"
                    >
                        <i class="fa fa-print"></i>
                        Print
                    </b-btn>
                    <b-button variant="secondary" @click="showModal=false">Close</b-button>            
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>
export default {
    name: 'collectionreport',
    data () {
        return {
            showModal: true,
            date_from: new Date(),
            date_to: new Date(),
            department_id: 0,
            type: 0,
            options: {
                departments: {
                    items: []
                }
            }
        }
    },
    created(){
        this.fillOptionsList('departments')
    },
    methods: {
        async fetchData(){
            var year = this.moment(this.app_year, 'YYYY')
            await this.$http.get('api/reports/rentalandcharges/' + this.location_id +'/'+year+'/'+ this.month_id+'/'+this.charge_type, {
              responseType: 'blob',
              headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                if(response.data.size > 0){
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'RentalAndCharges.xlsx');
                    document.body.appendChild(link);
                    link.click();
                }
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
        previewReport(){
            if(this.date_from != null && this.date_to != null){
                window.open('api/reports/collectionreport/'+this.moment(this.date_from, "YYYY-MM-DD")+'/'+this.moment(this.date_to, "YYYY-MM-DD")+'/'+this.department_id+'/'+this.type)
                // let routeData = this.$router.resolve({name: 'Tenant Per Sqm Rate', query: {location_id: this.location_id}});
                // window.open(routeData.href, '_blank');
            }
        },
    },
  }
</script>


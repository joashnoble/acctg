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
                @shown="focusElement('location_id')"
            >
                <div slot="modal-title">
                    Rental Rates and Charges
                </div>
                
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                            <label>Location</label>
                            <select2
                                ref="location_id"
                                :allowClear="false"
                                :placeholder="'Select Location'"
                                v-model="location_id"
                            >
                                <option value="0">All</option>
                                <option v-for="location in options.locations.items" :key="location.location_id" :value="location.location_id">{{  location.location_desc }}</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                            <label>App Year</label>
                            <date-picker 
                                ref="app_year"
                                id="app_year"
                                v-model="app_year"
                                lang="en" 
                                input-class="form-control mx-input"
                                format="YYYY"
                                type="year"
                                :clearable="false">
                            </date-picker>
                        </b-form-group>
                        <b-form-group>
                            <label>Month</label>
                            <select2
                                ref="month_id"
                                :allowClear="false"
                                :placeholder="'Select Month'"
                                v-model="jj"
                            >
                                <option v-for="month in options.months.items" :key="month.month_id" :value="month.month_id">{{  month.month_name }}</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                            <label>Charge Type</label>
                            <select2
                                ref="charge_type"
                                :allowClear="false"
                                :placeholder="'Select Charge Type'"
                                v-model="charge_type"
                            >
                                <option value="0">All</option>
                                <option value="1">Utilities</option>
                                <option value="2">Miscellaneous</option>
                                <option value="3">Other</option>
                            </select2>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-btn 
                        @click="fetchData"
                        variant="success"
                    >
                        <i class="fa fa-file-excel-o"></i>
                        Excel
                    </b-btn>
                    <b-button variant="secondary" @click="showModal=false">Close</b-button>            
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>
export default {
    name: 'rentalAndCharges',
    data () {
        return {
            showModal: true,
            location_id: 0,
            app_year: new Date,
            month_id: 0,
            charge_type: 0,
            options: {
                locations: {
                    items: []
                },
                months: {
                    items: []
                }
            },
        }
    },
    async created(){
        await this.fillOptionsList('months')
        await this.fillOptionsList('locations')
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
    },
    computed: {
        jj: {
            get: function(){
                if(this.options.months.items.length > 0 && this.month_id == 0){
                    setTimeout(function(){
                        this.month_id = this.moment(new Date, 'M')
                    }.bind(this), 1)
                }
                return this.month_id
            },
            set: function(value){
                this.month_id = value
            }
        }
    }
  }
</script>


<template>
    <div><!-- main container -->
        <notifications group="notification" />
        <div class="animated fadeIn"> <!-- main div -->
            <b-row> <!-- main row -->
                <b-col sm="12">
                    <b-card class="card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Billing Period List
                                <small class="font-italic">List of all registered billing periods.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('9-34')" variant="success" @click="$refs.billingperiodentry.showModalEntry = true, $refs.billingperiodentry.entryMode='Add', $refs.billingperiodentry.clearFields('period')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Billing Period
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.periods.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row> <!-- row button and search input -->
                        <b-row> <!-- row table -->
                            <b-col sm="12">
                                <b-table
                                    v-if="checkAction"
                                    responsive
                                    :filter="filters.periods.criteria"
                                    :fields="tables.periods.fields"
                                    :items.sync="tables.periods.items"
                                    :current-page="paginations.periods.currentPage"
                                    :per-page="paginations.periods.perPage"
                                    @filtered="onFiltered($event,'periods')"
                                    striped hover small show-empty
                                    
                                > <!-- table -->

                                <template v-slot:cell(action)="data" > <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('9-35')" :size="'sm'" variant="primary" @click="$refs.billingperiodentry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('9-36')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.period_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->

                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.periods.totalRows" :per-page="paginations.periods.perPage" v-model="paginations.periods.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->
        </div><!-- main div -->
        <billingperiodentry type="reference" ref="billingperiodentry"></billingperiodentry>
        <deleteentry entity="period" table="periods" primary_key="period_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->
</template>

<script>
import billingperiodentry from '../modals/BillingPeriodEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'periods',
    components: {
        billingperiodentry,
        deleteentry
    },
    data () {
      return {
        tables: {
          periods: {
                fields: [
                {
                    key: 'period_start_date',
                    label: 'Start Date',
                    tdClass: 'align-middle',
                    thStyle: {width: '19%'},
                    formatter: (value) => {
                            return this.moment(value,'MMMM DD, Y')
                                
                        }
                },
                {
                    key: 'period_end_date',
                    label: 'End Date',
                    tdClass: 'align-middle',
                    thStyle: {width: '19%'},
                    formatter: (value) => {
                            return this.moment(value,'MMMM DD, Y')
                                
                        }
                },
                {
                    key: 'month_name',
                    label: 'Applicable Month',
                    tdClass: 'align-middle',
                    thStyle: {width: '18%'},
                },
                {
                    key: 'app_year',
                    label: 'Applicable Year',
                    tdClass: 'align-middle',
                    thStyle: {width: '18%'},
                },
                {
                    key: 'period_due_date',
                    label: 'Due Date',
                    tdClass: 'align-middle',
                    thStyle: {width: '18%'},
                    formatter: (value) => {
                            return this.moment(value,'MMMM DD, Y')
                                
                        }
                },
                {
                    
                    key: 'action',
                    label: '',
                    thStyle: {width: '75px'},
                    tdClass: 'text-center align-middle'
                },
                ],
                items: []
            }
        },
        filters: {
          periods: {
            criteria: null
          }
        },
        paginations: {
          periods: {
            totalRows: 0,
            currentPage: 1,
            perPage: 10
          }
        },
      }
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('9-35') || this.checkRights('9-36')) == false){
                    this.tables.periods.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('periods')
      //console.log(this.tables.locations.items);
    }
  }
</script>


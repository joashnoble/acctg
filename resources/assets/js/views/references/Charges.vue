<template>
    <div><!-- main container -->
        <notifications group="notification" />
        <div class="animated fadeIn"> <!-- main div -->
            <b-row> <!-- main row -->
                <b-col sm="12">
                    <b-card class="border-dark card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span >
                                <!-- <i class="fa fa-bars"></i>  -->
                                Charge List
                                <small class="font-italic"> - List of all registered charges.</small>
                            </span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('3-10')" variant="success" @click="$refs.chargeentry.showModalEntry = true, $refs.chargeentry.entryMode='Add', $refs.chargeentry.clearFields('charge')">
                                            <i class="fa fa-file-o"></i>  &nbsp; Create New Charge
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.charges.criteria"
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
                                    :filter="filters.charges.criteria"
                                    :fields="tables.charges.fields"
                                    :items.sync="tables.charges.items"
                                    :current-page="paginations.charges.currentPage"
                                    :per-page="paginations.charges.perPage"
                                    @filtered="onFiltered($event,'charges')"
                                    striped hover small  show-empty
                                    
                                > <!-- table -->

                                <template v-slot:cell(action)="data"> <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('3-11')" :size="'sm'" variant="primary" @click="$refs.chargeentry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('3-12')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.charge_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->
                        
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.charges.totalRows" :per-page="paginations.charges.perPage" v-model="paginations.charges.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->
        </div><!-- main div -->
        <chargeentry type="reference" ref="chargeentry"></chargeentry>
        <deleteentry entity="charge" table="charges" primary_key="charge_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->
</template>

<script>
import chargeentry from '../modals/ChargeEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'charges',
    components: {
        chargeentry,
        deleteentry
    },
    data () {
      return {
        tables: {
          charges: {
                fields: [
                {
                    key: 'charge_code',
                    label: 'Code',
                    thStyle: {width: '150px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'charge_desc',
                    label: 'Charge Name',
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'account_title',
                    label: 'Account Title',
                    thStyle: {width: '25%'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'sort',
                    label: 'Sort',
                    thStyle: {width: '50px'},
                    tdClass: 'align-middle text-center',
                    thClass: 'text-center'
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
          charges: {
            criteria: null
          }
        },
        paginations: {
          charges: {
            totalRows: 0,
            currentPage: 1,
            perPage: 10
          }
        }
      }
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('3-11') || this.checkRights('3-12')) == false){
                    this.tables.charges.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('charges')
    }
  }
</script>


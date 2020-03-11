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
                                Contract Type List
                                <small class="bfont-italic">List of all registered contract types.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('6-22')" variant="success" @click="$refs.contracttypeentry.showModalEntry = true, $refs.contracttypeentry.entryMode='Add', $refs.contracttypeentry.clearFields('contracttype')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Contract Type
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.contracttypes.criteria"
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
                                    :filter="filters.contracttypes.criteria"
                                    :fields="tables.contracttypes.fields"
                                    :items.sync="tables.contracttypes.items"
                                    :current-page="paginations.contracttypes.currentPage"
                                    :per-page="paginations.contracttypes.perPage"
                                    @filtered="onFiltered($event,'contracttypes')"
                                    striped hover small show-empty
                                    
                                > <!-- table -->

                                <template v-slot:cell(action)="data"> <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('6-23')" :size="'sm'" variant="primary" @click="$refs.contracttypeentry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('6-24')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.contract_type_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->

                       <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.contracttypes.totalRows" :per-page="paginations.contracttypes.perPage" v-model="paginations.contracttypes.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->

        </div><!-- main div -->
        <contracttypeentry type="reference" ref="contracttypeentry"></contracttypeentry>
        <deleteentry entity="Contract Type" table="contracttypes" primary_key="contract_type_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->
</template>

<script>
import contracttypeentry from '../modals/ContractTypeEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'contracttypes',
    components: {
        contracttypeentry,
        deleteentry
    },
    data () {
      return {
        tables: {
          contracttypes: {
                fields: [
                {
                    key: 'contract_type_code',
                    label: 'Code',
                    thStyle: {width: '150px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'contract_type_desc',
                    label: 'Location Name',
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    
                    key: 'action',
                    label: '',
                    thStyle: {width: '75px'},
                    tdClass: 'text-center align-middle',
                },
                ],
                items: []
            }
        },
        filters: {
          contracttypes: {
            criteria: null
          }
        },
        paginations: {
          contracttypes: {
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
                if((this.checkRights('6-23') || this.checkRights('6-24')) == false){
                    this.tables.contracttypes.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('contracttypes')
      //console.log(this.tables.contracttypes.items);
    }
  }
</script>


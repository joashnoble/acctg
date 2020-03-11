<template>
    <div><!-- main container -->
        <notifications group="notification" />
        <div class="animated fadeIn"> <!-- main div -->
            <b-row> <!-- main row -->
                <b-col sm="12">
                    <b-card class="card-accent-dark"> <!-- main card -->
                        <h5 slot="header">  <!-- table header -->
                            <span>
                                <!-- <i class="fa fa-bars"></!-->
                                Nature Of Business List
                                <small class="font-italic">List of all registered nature of business.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('8-30')" variant="success" @click="$refs.natureofbusinessentry.showModalEntry = true, $refs.natureofbusinessentry.entryMode='Add', $refs.natureofbusinessentry.clearFields('natureofbusiness')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Nature Of Business
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.natureofbusinesses.criteria"
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
                                    :filter="filters.natureofbusinesses.criteria"
                                    :fields="tables.natureofbusinesses.fields"
                                    :items.sync="tables.natureofbusinesses.items"
                                    :current-page="paginations.natureofbusinesses.currentPage"
                                    :per-page="paginations.natureofbusinesses.perPage"
                                    @filtered="onFiltered($event,'natureofbusinesses')"
                                    striped hover small show-empty
                                    
                                > <!-- table -->

                                <template v-slot:cell(action)="data" > <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('8-31')" :size="'sm'" variant="primary" @click="$refs.natureofbusinessentry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('8-32')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.nature_of_business_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->

                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.natureofbusinesses.totalRows" :per-page="paginations.natureofbusinesses.perPage" v-model="paginations.natureofbusinesses.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->
        </div><!-- main div -->
        <natureofbusinessentry type="reference" ref="natureofbusinessentry"></natureofbusinessentry>
        <deleteentry entity="Nature of Business" table="natureofbusinesses" primary_key="nature_of_business_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->
</template>

<script>
import natureofbusinessentry from '../modals/NatureOfBusinessEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'natureofbusiness',
    components: {
        natureofbusinessentry,
        deleteentry
    },
    data () {
      return {
        tables: {
          natureofbusinesses: {
                fields: [
                {
                    key: 'nature_of_business_code',
                    label: 'Code',
                    thStyle: {width: '150px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'nature_of_business_desc',
                    label: 'Description',
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
          natureofbusinesses: {
            criteria: null
          }
        },
        paginations: {
          natureofbusinesses: {
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
                if((this.checkRights('8-31') || this.checkRights('8-32')) == false){
                    this.tables.natureofbusinesses.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('natureofbusinesses')
      //console.log(this.tables.natureofbusiness.items);
    }
  }
</script>


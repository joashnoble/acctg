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
                                Location List
                                <small class="font-italic">List of all registered locations.</small></span>
                        </h5>
                        
                        <b-row class="mb-2"> <!-- row button and search input -->
                            <b-col sm="4">
                                    <b-button v-if="checkRights('5-18')" variant="success" @click="$refs.locationentry.showModalEntry = true, $refs.locationentry.entryMode='Add', $refs.locationentry.clearFields('location')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Location
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.locations.criteria"
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
                                    :filter="filters.locations.criteria"
                                    :fields="tables.locations.fields"
                                    :items.sync="tables.locations.items"
                                    :current-page="paginations.locations.currentPage"
                                    :per-page="paginations.locations.perPage"
                                    @filtered="onFiltered($event,'locations')"
                                    striped hover small show-empty
                                    
                                > <!-- table -->

                                <template v-slot:cell(action)="data"> <!-- action slot  :to="{path: 'categories/' + data.item.id } -->
                                    <b-btn v-if="checkRights('5-19')" :size="'sm'" variant="primary" @click="$refs.locationentry.setUpdate(data)">
                                        <i class="fa fa-edit"></i>
                                    </b-btn>

                                    <b-btn v-if="checkRights('5-20')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.location_id)">
                                        <i class="fa fa-trash"></i>
                                    </b-btn>
                                </template>

                                </b-table> <!-- table -->
                            </b-col>
                        </b-row> <!-- row table -->

                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.locations.totalRows" :per-page="paginations.locations.perPage" v-model="paginations.locations.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>
                        
                    </b-card><!-- main card -->
                </b-col>
            </b-row> <!-- main row -->

        </div><!-- main div -->
        <locationentry type="reference" ref="locationentry"></locationentry>
        <deleteentry entity="location" table="locations" primary_key="location_id" ref="deleteentry"></deleteentry>
    </div> <!-- main container -->

   
</template>

<script>
import locationentry from '../modals/LocationEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'locations',
    components: {
        locationentry,
        deleteentry
    },
    data () {
      return {
        tables: {
          locations: {
                fields: [
                {
                    key: 'location_code',
                    label: 'Code',
                    thStyle: {width: '150px'},
                    tdClass: 'align-middle',
                    sortable: true
                },
                {
                    key: 'location_desc',
                    label: 'Location Name',
                    tdClass: 'align-middle',
                    sortable: true
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
          locations: {
            criteria: null
          }
        },
        paginations: {
          locations: {
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
                if((this.checkRights('5-19') || this.checkRights('5-20')) == false){
                    this.tables.locations.fields.pop()
                }
            }
            return true
        }
    },
    created () {
      this.fillTableList('locations')
      //console.log(this.tables.locations.items);
    }
  }
</script>


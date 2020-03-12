<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Locations
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.locationentry.showModalEntry = true, 
                            $refs.locationentry.entryMode='Add', 
                            $refs.locationentry.clearFields('location')">
                        New Location
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.locations.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.locations.fields"
            :items.sync="tables.locations.items"
            :filter="filters.locations.criteria"
            :current-page="paginations.locations.currentPage"
            :per-page="paginations.locations.perPage"
            @filtered="onFiltered($event,'locations')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.locationentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.location_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.locations.currentPage"
                    :total-rows="paginations.locations.totalRows"
                    :per-page="paginations.locations.perPage"
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
    <locationentry type="reference" ref="locationentry"></locationentry>
    <deleteentry entity="location" table="locations" primary_key="location_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import locationentry from '../modals/LocationEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'locations',
    components :{
        locationentry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                locations :{
                    fields:[
                        {
                            key:'location_name',
                            label: 'Location Name',
                            sortable: true
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
            filters: {
                locations: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                locations: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 5
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('locations');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
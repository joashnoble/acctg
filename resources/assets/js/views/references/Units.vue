<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Unit of Measurements
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.unitentry.showModalEntry = true, 
                            $refs.unitentry.entryMode='Add', 
                            $refs.unitentry.clearFields('unit')">
                    New Unit
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.units.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.units.fields"
            :items.sync="tables.units.items"
            :filter="filters.units.criteria"
            :current-page="paginations.units.currentPage"
            :per-page="paginations.units.perPage"
            @filtered="onFiltered($event,'units')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.unitentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.unit_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.units.currentPage"
                    :total-rows="paginations.units.totalRows"
                    :per-page="paginations.units.perPage"
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
    <unitentry type="reference" ref="unitentry"></unitentry>
    <deleteentry entity="unit" table="units" primary_key="unit_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import unitentry from '../modals/UnitEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'units',
    components :{
        unitentry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                units :{
                    fields:[
                        {
                            key:'unit_name',
                            label: 'Unit Name',
                            sortable: true
                        },
                        {
                            key:'unit_desc',
                            label: 'Description'
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
                units: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                units: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('units');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
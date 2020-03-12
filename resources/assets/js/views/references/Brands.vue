<template>
<div>
        <notifications group="notification" />
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Brands
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>


        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="$refs.brandentry.showModalEntry = true, 
                            $refs.brandentry.entryMode='Add', 
                            $refs.brandentry.clearFields('brand')">
                        <i class="fa fa-file-o"></i> &nbsp;New Brand
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.brands.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.brands.fields"
            :items.sync="tables.brands.items"
            :filter="filters.brands.criteria"
            :current-page="paginations.brands.currentPage"
            :per-page="paginations.brands.perPage"
            @filtered="onFiltered($event,'brands')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="$refs.brandentry.setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.brand_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>

            </b-table>

        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.brands.currentPage"
                    :total-rows="paginations.brands.totalRows"
                    :per-page="paginations.brands.perPage"
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
    <brandentry type="reference" ref="brandentry"></brandentry>
    <deleteentry entity="brand" table="brands" primary_key="brand_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import brandentry from '../modals/BrandEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'brands',
    components :{
        brandentry,
        deleteentry

    },
    data(){
        return {
        entryMode: 'Add',
        showModalEntry: false,
        showModalDelete: false,
            tables: {
                brands :{
                    fields:[
                        {
                            key:'brand_name',
                            label: 'Brand Name',
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
                brands: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                brands: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            } // END OF PAGINATION
        } // END OF DATA RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('brands');
    }, // END OF CREATED
    methods:{
    } // END OF METHODS
}// END OF EXPORT 
</script>
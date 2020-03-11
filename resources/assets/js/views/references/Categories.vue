<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <notifications group="notification" />
        <div class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Category List
                                <small class="font-italic">List of all registered category.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col sm="4">
                                    <b-button v-if="checkRights('4-14')" variant="success" @click="$refs.categoryentry.showModalEntry = true, $refs.categoryentry.entryMode='Add', $refs.categoryentry.clearFields('category')">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Category
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.categories.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row>

                        <b-row>
                            <b-col sm="12">
                                <b-table 
                                    v-if="checkAction"
                                    responsive
                                    :filter="filters.categories.criteria"
                                    :fields="tables.categories.fields"
                                    :items.sync="tables.categories.items"
                                    :current-page="paginations.categories.currentPage"
                                    :per-page="paginations.categories.perPage"
                                    @filtered="onFiltered($event,'categories')"
                                    striped hover small show-empty
                                >
                                    <template v-slot:cell(action)="data">
                                        <b-btn v-if="checkRights('4-15')" :size="'sm'" variant="primary" @click="$refs.categoryentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('4-16')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.category_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>

                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.categories.totalRows" :per-page="paginations.categories.perPage" v-model="paginations.categories.currentPage"
                                    class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <categoryentry type="reference" ref="categoryentry"></categoryentry>
        <deleteentry entity="category" table="categories" primary_key="category_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import categoryentry from '../modals/CategoryEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'categories',
    components: {
        categoryentry,
        deleteentry
    },
    data () {
        return {
            tables: {
                categories: {
                    fields:[
                        {
                            key:'category_code',
                            label: 'Category Code',
                            thStyle: {width: '150px'},
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'category_desc',
                            label: 'Description',
                            tdClass: 'align-middle',
                            sortable: true
                        },
                        {
                            key:'action',
                            label:'',
                            thStyle: {width: '75px'},
                            tdClass: 'text-center align-middle',
                        }
                    ],
                    items: []
                }
            },
            filters: {
                categories: {
                    criteria: null
                }
            },
            paginations: {
                categories: {
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
                if((this.checkRights('4-15') || this.checkRights('4-16')) == false){
                    this.tables.categories.fields.pop()
                }
            }
            return true
        }
    },
    created () {
        this.fillTableList('categories');
    },
  }
</script>


<template>
<div>
        <notifications group="notification" />
<div v-show="showEntry === false" class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Products
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row class="mb-2">
            <b-col sm="2">
                <b-button class="mb-2" variant="success" 
                    @click="showEntry = true, entryMode='Add', clearFields('product')">
                        <i class="fa fa-file-o"></i> &nbsp;New Product
                </b-button>
            </b-col>
            <b-col sm="6">
            </b-col>
            <b-col sm="4">
                <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                    <b-form-input 
                                v-model="filters.products.criteria"
                                type="text" 
                                placeholder="Search"
                                debounce="250">
                    </b-form-input>
                </b-input-group>
            </b-col>
        </b-row>
        <b-row mb="2">
            <b-table striped borderless show-empty
            :fields="tables.products.fields"
            :items.sync="tables.products.items"
            :filter="filters.products.criteria"
            :current-page="paginations.products.currentPage"
            :per-page="paginations.products.perPage"
            @filtered="onFiltered($event,'products')"
            >
                <template v-slot:cell(action)="data">
                    <b-btn :size="'sm'" variant="primary" @click="setUpdate(data)">
                        <b-icon-pencil></b-icon-pencil>
                    </b-btn>

                    <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.product_id)">
                        <b-icon-trash></b-icon-trash>
                    </b-btn>
                </template>
            </b-table>
        </b-row>
        <template v-slot:footer> 
            <b-row mb="0">
                <b-col sm="12" class="my-1 mb-0" >
                    <b-pagination mb="0"
                    v-model="paginations.products.currentPage"
                    :total-rows="paginations.products.totalRows"
                    :per-page="paginations.products.perPage"
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
</div>
<div v-show="showEntry" class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Products
                <small> |  {{entryMode}}</small>
            </span>
        </h6>


    <!-- FORM -->
            <b-form autocomplete="off">
                <b-row>
                    <b-col sm="4">
                        <b-form-group>
                            <label class="required" for="product_code">Product Look Up / PLU</label>
                            <b-form-input
                                ref="product_code"
                                id="product_code"
                                v-model="forms.product.fields.product_code"
                                debounce="250"
                                type="text"
                                placeholder="">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required" for="product_desc">Name</label>
                            <b-form-input
                                ref="product_desc"
                                id="product_desc"
                                v-model="forms.product.fields.product_desc"
                                debounce="250"
                                type="text"
                                placeholder="">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label for="product_desc1">Description</label>
                            <b-form-input
                                ref="product_desc1"
                                id="product_desc1"
                                v-model="forms.product.fields.product_desc1"
                                debounce="250"
                                type="text"
                                placeholder="">
                            </b-form-input>
                        </b-form-group>
                        <b-form-group>
                            <label class="required"> Brand </label>
                            <select2
                                ref="brand_id"
                                :allowClear="false"
                                :placeholder="'Select Brand'"
                                v-model="forms.product.fields.brand_id"
                            >
                            <option v-for="brand in options.brands.items" :key="brand.brand_id" :value="brand.brand_id">{{brand.brand_name}}</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                            <label class="required"> Category </label>
                            <select2
                                ref="category_id"
                                :allowClear="false"
                                :placeholder="'Select Category'"
                                v-model="forms.product.fields.category_id"
                            >
                                <option v-for="category in options.categories.items" :key="category.category_id" :value="category.category_id">{{category.category_name}}</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                            <label class="required"> Tax Type </label>
                            <select2
                                ref="tax_type_id"
                                :allowClear="false"
                                :placeholder="'Select Tax Type'"
                                v-model="forms.product.fields.tax_type_id"
                            >
                                <option v-for="taxtype in options.taxtypes.items" :key="taxtype.tax_type_id" :value="taxtype.tax_type_id">{{taxtype.tax_type}}</option>
                            </select2>
                        </b-form-group>
                    </b-col> 
                    <!-- END OF COL-4 -->
                    <b-col sm="8">
                      <b-row>
                          <b-col sm="4">
                                <b-form-group>
                                    <label class="required">Unit of Measurement</label>
                                    <select2
                                        ref="account_id"
                                        :allowClear="false"
                                        :placeholder="'Select Bulk Unit'"
                                        v-model="forms.product.fields.parent_unit_id"
                                    >
                                    <option v-for="unit in options.units.items" :key="unit.unit_id" :value="unit.unit_id">{{unit.unit_name}}</option>
                                    </select2>
                                </b-form-group>
                          </b-col>
                          <b-col sm="2">
                            <b-form-group>
                                <label  for="is_bulk"> Use Retail ?</label>
                                <b-form-checkbox
                                    id="is_bulk"
                                    v-model="forms.product.fields.is_bulk"
                                    size="lg"
                                    value="1"
                                    unchecked-value="0">
                                    =   
                                </b-form-checkbox>
                            </b-form-group>
                          </b-col>
                          <b-col sm="2">
                                <b-form-group>
                                    <label  for="child_unit_desc"> Equivalency</label>
                                    <b-form-input
                                        ref="child_unit_desc"
                                        id="child_unit_desc"
                                        v-model="forms.product.fields.child_unit_desc"
                                        debounce="250"
                                        type="text"
                                        placeholder="">
                                    </b-form-input>
                                </b-form-group>
                          </b-col>
                          <b-col sm="4">
                                <b-form-group>
                                    <label>Retail Unit</label>
                                    <select2
                                        ref="account_id"
                                        :allowClear="false"
                                        :placeholder="'Select Retail Unit'"
                                        v-model="forms.product.fields.child_unit_id"
                                    >
                                    <option v-for="unit in options.units.items" :key="unit.unit_id" :value="unit.unit_id">{{unit.unit_name}}</option>
                                    </select2>
                                </b-form-group>
                          </b-col>
                      </b-row>
                    <b-row>
                            <b-col sm="6">
                                <b-form-group>
                                    <label class="required"> Inventory Type </label>
                                    <select2
                                        ref="item_type_id"
                                        :class="'no-search'"
                                        :allowClear="false"
                                        :placeholder="'Select Inventory Type'"
                                        v-model="forms.product.fields.item_type_id"
                                    >
                                        <option v-for="itemtype in options.itemtypes.items" :key="itemtype.item_type_id" :value="itemtype.item_type_id">{{itemtype.item_type}}</option>
                                    </select2>
                                </b-form-group>
                                <b-form-group>
                                    <label for="sale_price">Suggested Retail Price (SRP)</label>
                                    <vue-autonumeric 
                                        id="sale_price"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.sale_price"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>
                                <b-form-group>
                                    <label for="discounted_price">Discounted Price</label>
                                    <vue-autonumeric 
                                        id="discounted_price"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.discounted_price"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>

                                <b-form-group>
                                    <label for="dealer_price">Dealer's Price</label>
                                    <vue-autonumeric 
                                        id="dealer_price"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.dealer_price"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>

                                <b-form-group>
                                    <label for="distributor_price">Distributor's Price</label>
                                    <vue-autonumeric 
                                        id="distributor_price"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.distributor_price"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>

                            </b-col>
                            <b-col sm="6">
                                <b-form-group>
                                    <label for="purchase_cost">Purchase Cost</label>
                                    <vue-autonumeric 
                                        id="purchase_cost"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.purchase_cost"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>
                                <b-form-group>
                                    <label for="product_warn">Warning Quantity</label>
                                    <vue-autonumeric 
                                        id="product_warn"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.product_warn"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>
                                <b-form-group>
                                    <label for="product_ideal">Ideal Quantity</label>
                                    <vue-autonumeric 
                                        id="product_ideal"
                                        :class="'form-control text-right'"
                                        v-model="forms.product.fields.product_ideal"
                                        :options="{minimumValue: 0,modifyValueOnWheel: false, emptyInputBehavior: 0}"
                                    ></vue-autonumeric>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Sales Income Journal Account Link (Credit)</label>
                                    <select2
                                        ref="account_id"
                                        :allowClear="false"
                                        :placeholder="'Select Sales Income Account'"
                                        v-model="forms.product.fields.income_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitlesoptions.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                </b-form-group>
                                <b-form-group>
                                    <label class="required">Purchase Expense Journal Account Link (Debit)</label>
                                    <select2
                                        ref="account_id"
                                        :allowClear="false"
                                        :placeholder="'Select Purchase Expense Account'"
                                        v-model="forms.product.fields.expense_account_id"
                                    >
                                    <option v-for="accounttitle in options.accounttitlesoptions.items" :key="accounttitle.account_id" :value="accounttitle.account_id">{{accounttitle.account_title}}</option>
                                    </select2>
                                </b-form-group>
                            </b-col>
                    </b-row>
                        

                </b-col> 
                <!-- END OF COL-8 -->
                </b-row>

            </b-form>
        <template v-slot:footer>
            <b-row class="">
                <b-col sm="12">
                    <b-button 
                        :disabled="forms.product.isSaving" 
                        variant="success" 
                        @click="onProductEntry">
                        <b-spinner v-if="forms.product.isSaving" small variant="light" label="Spinning"></b-spinner>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showEntry=false">Close</b-button>
                </b-col>
            </b-row>
        </template>
      </b-card>
</div>
    <deleteentry entity="product" table="products" primary_key="product_id" ref="deleteentry"></deleteentry>
</div>
</template>

<script>
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'products',
    components :{
        deleteentry

    },
    data(){
        return {
            entryMode: 'Add',
            showEntry: false,
            showModalDelete: false,
            options:{
                brands:{ items : [] },
                categories:{ items : [] },
                taxtypes:{ items : [] },
                itemtypes:{ items : [] },
                accounttitlesoptions:{ items : [] },
                units:{ items : [] },
            },
            tables: {
                products :{
                    fields:[
                        {
                            key:'product_code',
                            label: 'Code',
                        },
                        {
                            key:'product_desc',
                            label: 'Name',
                            sortable: true
                        },
                        {
                            key:'parent_unit_name',
                            label: 'Unit',
                        },
                        {
                            key:'category_name',
                            label: 'Category',
                            sortable: true
                        },
                        {
                            key:'action',
                            label:'Action'
                        }
                    ],
                     items: [],
                },
            }, // END OF TABLES
            filters: {
                products: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                products: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
            forms:{
                product:{
                    isSaving: false,
                    fields:{
                        product_id : null,
                        product_code : null,
                        product_desc : null,
                        product_desc1 : null,
                        brand_id : null,
                        category_id : null,
                        tax_type_id : null,
                        is_bulk:0,
                        parent_unit_id:null,
                        child_unit_desc:null,
                        child_unit_id:null,
                        item_type_id : null,
                        purchase_cost : 0.00,
                        sale_price : 0.00,
                        discounted_price: 0.00,
                        dealer_price: 0.00,
                        distributor_price: 0.00,
                        product_warn: 0.00,
                        product_ideal: 0.00,
                        income_account_id:null,
                        expense_account_id:null,
                    }
                }

            
            },
        } // END OF  RETURN
    }, // END OF DATA
    created(){
        this.fillOptionsList('accounttitlesoptions');
        this.fillOptionsList('brands');
        this.fillOptionsList('categories');
        this.fillOptionsList('itemtypes');
        this.fillOptionsList('units');
        this.fillOptionsList('taxtypes');

        this.fillTableList('products');
    }, // END OF CREATED
    methods:{
        onProductEntry(){
            if(this.entryMode == 'Add'){
                this.createEntity('product', false, 'products', true)
            }else{
                this.updateEntity('product', 'product_id', false, this.row)
            }
        },
        setUpdate(data){
            this.row = data.item
            this.clearFields('product')
            this.fillEntityForm('product', data.item.product_id, 'showEntry')
            this.entryMode='Edit'
        }
    } // END OF METHODS
}// END OF EXPORT 
</script>
<style  scoped>
ul {
    margin: 0!important;
}
</style>
<template>
<div>
        <notifications group="notification" />
<div v-show="showEntry === false" class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h6 slot="header">
            <span >Chart of Accounts
                <small>  <b-icon-question></b-icon-question></small>
            </span>
        </h6>
        <b-row>
            <b-col sm="4">
                <div v-for="type in treeData.types" :key="type.account_class_id" >
                    <b>{{type.account_class}}</b>
                <li v-for="tree in treeData.parent" :key="tree.account_id" v-if="tree.account_class_id == type.account_class_id ">
                    {{ tree.account_title }}
                    <ul v-for="child in treeData.child" :key="child.account_id" v-if="tree.account_id == child.parent_account_id ">
                        <li >{{ child.account_title }}</li>
                    </ul>

                </li><br>
                </div>
            </b-col>
            <b-col sm="8">
                 <b-row class="mb-2">
                    <b-col sm="4">
                        <b-button class="mb-2" variant="success" 
                            @click="showEntry = true, 
                                    entryMode='Add', 
                                    clearFields('accounttitle'),
                                    reloadAccountTitlesOptions(),
                                    forms.accounttitle.fields.parent_account_id = 0
                        ">
                                <i class="fa fa-file-o"></i> &nbsp;New Account Title
                        </b-button>
                    </b-col>
                    <b-col sm="4">
                    </b-col>
                    <b-col sm="4">
                        <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                            <b-form-input 
                                        v-model="filters.accounttitles.criteria"
                                        type="text" 
                                        placeholder="Search"
                                        debounce="250">
                            </b-form-input>
                        </b-input-group>
                    </b-col>
                </b-row>
                <b-row mb="2">
                    <b-table striped borderless show-empty
                    :fields="tables.accounttitles.fields"
                    :items.sync="tables.accounttitles.items"
                    :filter="filters.accounttitles.criteria"
                    :current-page="paginations.accounttitles.currentPage"
                    :per-page="paginations.accounttitles.perPage"
                    @filtered="onFiltered($event,'accounttitles')"
                    >
                    <template v-slot:cell(action)="data">
                        <b-btn :size="'sm'" variant="primary" @click="setUpdate(data)"><b-icon-pencil></b-icon-pencil></b-btn>
                        <b-btn :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.account_id)"><b-icon-trash></b-icon-trash></b-btn>
                    </template>
                    </b-table>
                </b-row>
                <div>
                    <b-row mb="0">
                        <b-col sm="12" class="my-1 mb-0" >
                            <b-pagination mb="0"
                            v-model="paginations.accounttitles.currentPage"
                            :total-rows="paginations.accounttitles.totalRows"
                            :per-page="paginations.accounttitles.perPage"
                            prev-text="Previous"
                            next-text="Next"
                            hide-goto-end-buttons
                            size="sm"
                            align="right"
                            ></b-pagination>
                        </b-col>
                    </b-row> 
                </div>
            </b-col>
    </b-row>
</b-card>
</div>
<div v-show="showEntry" class="animated fadeIn">
      <b-card border-variant="default" class="text-left card-header-primary">
        <h5 slot="header">
            <span >Chart of Accounts
                <small> |  {{entryMode}}</small>
            </span>
        </h5>
    <!-- FORM -->
            <b-form autocomplete="off">
                <b-row>
                        <b-col sm="6">
                            <b-form-group>
                                <label class="required" for="account_no">Account Number</label>
                                <b-form-input
                                    ref="account_no"
                                    id="account_no"
                                    v-model="forms.accounttitle.fields.account_no"
                                    debounce="250"
                                    type="text"
                                    placeholder="">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label class="required" for="account_title">Account Title</label>
                                <b-form-input
                                    ref="account_title"
                                    id="account_title"
                                    v-model="forms.accounttitle.fields.account_title"
                                    debounce="250"
                                    type="text"
                                    placeholder="">
                                </b-form-input>
                            </b-form-group>
                            <b-form-group>
                                <label for="description">Description</label>
                                <b-form-input
                                    ref="description"
                                    id="description"
                                    v-model="forms.accounttitle.fields.description"
                                    debounce="250"
                                    type="text"
                                    placeholder="">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col sm="6">
                            <b-form-group>
                                <label class="required"> Parent Account </label>
                                <select2
                                    ref="brand_id"
                                    @input="selParentAccount"
                                    :allowClear="false"
                                    :placeholder="'Select Parent Account'"
                                    v-model="forms.accounttitle.fields.parent_account_id"
                                >
                                <option value="0">No Parent Account</option>
                                <option v-for="accounttitleoption in options.accounttitlesoptions.items" :key="accounttitleoption.account_id" :value="accounttitleoption.account_id">{{accounttitleoption.account_title}}</option>
                                </select2>
                                <small><i>If there is a Parent Account. Account Class will be inherited from it.</i></small>
                            </b-form-group>
                            <b-form-group v-show="forms.accounttitle.fields.parent_account_id == 0">
                                <label class="required"> Classification </label>
                                <select2
                                    ref="brand_id"
                                    :allowClear="false"
                                    :placeholder="'Select Classification'"
                                    v-model="forms.accounttitle.fields.account_class_id"
                                >
                                <option v-for="accountclass in options.accountclasses.items" :key="accountclass.account_class_id" :value="accountclass.account_class_id">{{accountclass.account_class}}</option>
                                </select2>
                            </b-form-group>

                        </b-col>
                </b-row>

            </b-form>
        <template v-slot:footer>
            <b-row class="">
                <b-col sm="12">
                    <b-button 
                        :disabled="forms.accounttitle.isSaving" 
                        variant="success" 
                        @click="onAccountTitlesEntry">
                        <b-spinner v-if="forms.accounttitle.isSaving" small variant="light" label="Spinning"></b-spinner>
                        <i class="fa fa-check"></i>
                        Save
                    </b-button>
                    <b-button variant="danger" @click="showEntry=false">Close</b-button>
                </b-col>
            </b-row>
        </template>
      </b-card>
</div>
    <deleteentry entity="Account Title" table="accounttitles" primary_key="account_id" ref="deleteentry"></deleteentry>

</div>
</template>

<script>
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'accounttitles',
    components :{
        deleteentry

    },
    data(){
        return {
            treeData:{
                parent:{
                    items: []
                },
                child:{
                    items: []
                },
                types:{
                    items: []
                }
            },
            entryMode: 'Add',
            showEntry: false,
            showModalDelete: false,
            options:{
                accountclasses:{
                    items : []
                },
                accounttitlesoptions:{
                    items : []
                }
            },
            tables: {
                accounttitles :{
                    fields:[
                        {
                            key:'account_no',
                            label: 'Code',
                            sortable: true,
                            thStyle: {width: '10%'},
                        },
                        {
                            key:'account_title',
                            label: 'Name',
                            sortable: true
                        },
                        {
                            key:'parent_account',
                            label: 'Parent',
                            sortable: true
                        },
                        {
                            key:'account_class',
                            label: 'Class',
                            sortable: true
                        },

                        {
                            key:'action',
                            label:'Action',
                            thStyle: {width: '15%'},
                        }
                    ],
                     items: [],
                },
            }, // END OF TABLES
            filters: {
                accounttitles: {
                    criteria: null
                }
            }, // END OF FILTERS
            paginations: {
                accounttitles: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            }, // END OF PAGINATION
            forms:{
                accounttitle:{
                    isSaving: false,
                    fields:{
                        account_id: null,
                        account_no:null,
                        account_title:null,
                        account_class_id:0,
                        parent_account_id:0,
                        description:null,
                    }
                }            
            },
        } // END OF  RETURN
    }, // END OF DATA
    created(){
        this.fillTableList('accounttitles');
        this.fillOptionsList('accountclasses');
        this.refreshChartofAccountsTree();

    }, // END OF CREATED
    methods:{
        refreshChartofAccountsTree(){
        this.$http.get('/api/accounttitlestree',{
            headers: { Authorization: 'Bearer ' + localStorage.getItem('token') }})
            .then((response) => {
                const records = response.data
                this.treeData.parent = records.accounts
                this.treeData.child = records.child
                this.treeData.types = records.types
            }).catch(error => { if (!error.response) return console.log(error)
            })
        },
        async onAccountTitlesEntry(){
            if(this.entryMode == 'Add'){
                // await  this.createEntity('accounttitle', false, 'accounttitles', true)


          this.forms.accounttitle.isSaving = true
          this.resetFieldStates('accounttitle')
          await this.$http.post('api/accounttitle', this.forms.accounttitle.fields,{
             headers: { Authorization: 'Bearer ' + localStorage.getItem('token')}
          })
          .then((response) => {  
            this.forms.accounttitle.isSaving = false
            this.clearFields('accounttitle')
            this.$notify({ type: 'success', group: 'notification', title: 'Success!',
              text: 'The Account Title has been successfully created.'
            })
            this.tables.accounttitles.items.unshift(response.data.data) // ADDING DATA TO TABLE
            this.paginations.accounttitles.totalRows++ // REFRESH PAGINATION
            this.showEntry = false
          }).catch(error => {
                this.forms.accounttitle.isSaving = false
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                    if(a == 0){
                    this.focusElement(key)
                    this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
          })
        await  this.refreshChartofAccountsTree();
            }else{
                
        this.forms.accounttitle.isSaving = true
          await this.resetFieldStates('accounttitle')
          await this.$http.put('api/accounttitle/' + this.forms.accounttitle.fields.account_id, this.forms.accounttitle.fields ,{
                headers: { Authorization: 'Bearer ' + localStorage.getItem('token') } })
                .then((response) => {
                this.forms.accounttitle.isSaving = false
                this.$notify({ type: 'success', group: 'notification', title: 'Success!',
                    text: 'The Account Title has been successfully updated.'
                })
                    for (var key in response.data.data) {
                        this.row[key] = response.data.data[key]
                    }
                    this.showEntry = false // BACK TO MAIN TABLE
                }).then(()=>{
                    this.refreshChartofAccountsTree();
                }).catch(error => {
                    this.forms.accounttitle.isSaving = false
                    if (!error.response) return
                    const errors = error.response.data.errors
                    var a = 0
                for (var key in errors) {
                    if(a == 0){
                        this.focusElement(key)
                        this.$notify({ type: 'error', group: 'notification', title: 'Error!', text: errors[key][0]})
                    } a++
                }
                }); // END OF ERROR CATCH
            } // END OF IF ELSE 
        },
        async setUpdate(data){
            await this.fillOptionsList('accounttitlesoptions')
            this.row = data.item
            await this.clearFields('accounttitle')
            await this.fillEntityForm('accounttitle', data.item.account_id, 'showEntry')
            this.entryMode='Edit'
        },
        selParentAccount: function (value, data) {
            if(value == 0){
                this.forms.accounttitle.fields.account_class_id = 0; // Set the class as 0
            }
        },
        reloadAccountTitlesOptions(){
            this.fillOptionsList('accounttitlesoptions')
        },
    } // END OF METHODS
}// END OF EXPORT 
</script>
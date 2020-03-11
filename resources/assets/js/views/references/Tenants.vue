<template>
    <div>
        <notifications group="notification" />
        <div v-show="$refs.tenantentry !== undefined ? $refs.tenantentry.showEntry === false : true" class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Tenant List
                                <small class="font-italic">List of all registered tenants.</small></span>
                        </h5>
                        <b-row class="mb-2">
                            <b-col  sm="4">
                                    <b-button v-if="checkRights('1-2')" variant="success" @click="$refs.tenantentry.setEntry()">
                                            <i class="fa fa-file-o"></i> &nbsp; Create New Tenant
                                    </b-button>
                            </b-col>

                            <b-col  sm="4">
                                <span></span>
                            </b-col>

                            <b-col  sm="4">
                                 <b-input-group prepend-html='<i class="fa fa-search"></i> &nbsp; Search'>
                                    <b-form-input 
                                                v-model="filters.tenants.criteria"
                                                type="text" 
                                                placeholder="Search"
                                                debounce="250">
                                    </b-form-input>
                                </b-input-group>
                            </b-col>
                        </b-row>
                        
                        <b-row>
                            <b-col sm="12">
                                <b-table v-if="checkAction"
                                    responsive
                                    :filter="filters.tenants.criteria"
                                    :fields="tables.tenants.fields"
                                    :items.sync="tables.tenants.items"
                                    :current-page="paginations.tenants.currentPage"
                                    :per-page="paginations.tenants.perPage"
                                    @filtered="onFiltered($event, 'tenants')"
                                    striped small show-empty
                                >
                                    <template v-slot:cell(row_data)="row">
                                        <!-- <b-btn :size="'sm'" variant="success" @click.stop="row.toggleDetails(), tenantFiles(row)"> -->
                                            <i @click.stop="row.toggleDetails(), tenantFiles(row)" :class="row.detailsShowing ? 'fa fa-minus fa-lg text-danger' : 'fa fa-plus fa-lg text-success'"></i>
                                        <!-- </b-btn> -->
                                    </template>
                                    <template v-slot:row-details="row">
                                        <b-row>
                                            <b-col lg="2"></b-col>
                                            <b-col lg="8">
                                                <b-row class="font-weight-bold">
                                                    <b-col lg="6">
                                                        <p>Tenant Code : {{row.item.tenant_code}}</p>
                                                        <p>Trade Name : {{row.item.trade_name}}</p>
                                                        <p>Company Name : {{row.item.company_name}}</p>
                                                        <p>Space Code : {{row.item.space_code}}</p>
                                                        <p>Business Concept : {{row.item.business_concept}}</p>
                                                        <p>Head Office Address : {{row.item.head_office_address}}</p>
                                                        <p>Billing Address : {{row.item.billing_address}}</p>
                                                    </b-col>
                                                    <b-col lg="6">
                                                        <p>Contact Person : {{row.item.contact_person}}</p>
                                                        <p>Designation : {{row.item.designation}}</p>
                                                        <p>Contact Number : {{row.item.contact_number}}</p>
                                                        <p>Email Address : {{row.item.email_address}}</p>
                                                        <p>TIN : {{row.item.tin_number}}</p>
                                                    </b-col>
                                                </b-row>
                                                <b-form-file @change="fieldChange($event, row)" ref="file" plain style="display: none;"></b-form-file>
                                                <b-btn class="float-right mb-2" variant="primary" @click="$refs.file.$el.click()">
                                                    <i class="fa fa-file-o"></i>
                                                    Add File
                                                </b-btn>
                                                <table class="w-100 mb-2 responsive">
                                                    <thead>
                                                        <tr>
                                                            <th>Filename</th>
                                                            <th class="text-center" style="width:75px;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-if="row.item.files.length == 0" >
                                                            <td colspan="2">No files found.</td>
                                                        </tr>
                                                        <tr v-for="files in row.item.files">
                                                            <td class="align-middle">{{ files.file_name }}</td>
                                                            <td class="text-center">
                                                                <b-btn size="sm" :href="files.file_path+'/'+files.file_name" download="" variant="primary"><i class="fa fa-download"></i></b-btn>
                                                                <b-btn @click="fileDelete(row, files)" size="sm" variant="danger"><i class="fa fa-trash"></i></b-btn>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </b-col>
                                            <b-col lg="2"></b-col>
                                        </b-row>
                                    </template>
                                    <template v-slot:cell(action)="data" >
                                        <b-btn v-if="checkRights('1-3')" :size="'sm'" variant="primary" @click="$refs.tenantentry.setUpdate(data)">
                                            <i class="fa fa-edit"></i>
                                        </b-btn>

                                        <b-btn v-if="checkRights('1-4')" :size="'sm'" variant="danger" @click="$refs.deleteentry.setDelete(data.item.tenant_id)">
                                            <i class="fa fa-trash"></i>
                                        </b-btn>
                                    </template>
                                    
                                </b-table>
                            </b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row >  <!-- Pagination -->
                                <b-col sm="12" class="my-1 ">
                                    <b-pagination size="sm" align="right" :total-rows="paginations.tenants.totalRows" :per-page="paginations.tenants.perPage" v-model="paginations.tenants.currentPage"
                                     class="my-0" />
                                </b-col>
                            </b-row> <!-- Pagination -->
                        </template>

                    </b-card>
                </b-col>
            </b-row>
        </div>
        <tenantentry type="reference" ref="tenantentry"></tenantentry>
        <deleteentry entity="tenant" table="tenants" primary_key="tenant_id" ref="deleteentry"></deleteentry>
    </div>
</template>

<script>
import tenantentry from '../modals/TenantEntry'
import deleteentry from '../modals/DeleteEntry'
export default {
    name: 'tenants',
    components: {
        tenantentry,
        deleteentry
    },
    data () {
      return {
            tables: {
                tenants: {
                    fields:[
                        {
                            key:'row_data',
                            label: '',
                            tdClass: 'align-middle',
                            thStyle: {width: '2%'}
                        },
                        {
                            key:'tenant_code',
                            label: 'Tenant Code',
                            thStyle: {width: '100px'},
                            tdClass: 'align-middle',
                            sortable:true
                        },
                        {
                            key:'trade_name',
                            label: 'Trade Name',
                            thStyle: {width: '14%'},
                            tdClass: 'align-middle',
                            sortable:true
                        },
                        {
                            key:'company_name',
                            label: 'Company Name',
                            thStyle: {width: '15%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'head_office_address',
                            label: 'Address',
                            thStyle: {width: '15%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'contact_person',
                            label: 'Contact Person',
                            thStyle: {width: '12%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'designation',
                            label: 'Designation',
                            thStyle: {width: '12%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key:'business_concept',
                            label: 'Business Concept',
                            thStyle: {width: '12%'},
                            tdClass: 'align-middle',
                        },
                        {
                            key: 'action',
                            label: 'Action',
                            tdClass: 'text-center align-middle',
                            thClass: 'text-center',
                            thStyle: {width: '80px'}
                        }
                    ],
                    items: []
                }
            },
            filters: {
                tenants: {
                    criteria: null
                }
            },
            paginations: {
                tenants: {
                    totalRows: 0,
                    currentPage: 1,
                    perPage: 10
                }
            },
            file: new FormData,
        }
    },
    methods:{
        async tenantFiles(row){
            if(row.detailsShowing == true){
                return
            }
            await this.$http.get('/api/tenant_files/' + row.item.tenant_id, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                      'Content-Type' : 'multipart/form-data'
                  }
            })
            .then((response) => {
                row.item.files = response.data.data
            })
        },
        fieldChange(e, row){
            let attachment = e.target.files[0]
            let path = 'uploads/tenants'

            this.file.append('folder', row.item.tenant_id)
            this.file.append('file', attachment)
            this.file.append('path', path)

            this.$http.post('/api/fileupload', this.file, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                      'Content-Type' : 'multipart/form-data'
                  }
            })
            .then((response) => {
                if(response.data != null && response.data != ""){
                    row.item.files.unshift({file_name:response.data.name, file_path:response.data.path, file_id:response.data.id})
                }
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
        fileDelete(row, file){
            this.$http.post('/api/filedelete', {path: file.file_path+'/'+file.file_name, file_id: file.file_id}, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token')
                  }
            })
            .then((response) => {
                console.log(response)
                this.$delete(row.item.files, row.item.files.findIndex(f => f.file_id == file.file_id))
                this.$notify({
                    type: response.data.status,
                    group: 'notification',
                    title: response.data.title,
                    text: response.data.message
                })
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
    },
    created () {
        this.fillTableList('tenants');
    },
    computed: {
        checkAction(){
            if(this.$store.state.rights.length > 0){
                if((this.checkRights('1-3') || this.checkRights('1-4')) == false){
                    this.tables.tenants.fields.pop()
                }
            }
            return true
        }
    }
  }
</script>


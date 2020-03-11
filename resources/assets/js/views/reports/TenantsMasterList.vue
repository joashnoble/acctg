<style>
</style>

<template>
    <div>
        <div>
            <b-modal
                @hidden="$router.go(-1)"
                v-model="showModalTenants"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                @shown="focusElement('order_by')"
            >
                <div slot="modal-title">
                    Tenants Master List
                </div>
                
                <b-row>
                    <b-col lg=12>
                        Click OK to generate the report.
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-button variant="primary" @click="previewReport()">
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="secondary" @click="showModalTenants=false">Close</b-button>            
                </div>
            </b-modal>
        </div>
    </div>
</template>
<script>
export default {
    name: 'contractsMasterList',
    data () {
        return {
            company_info: [],
            contracts: [],
            showModalTenants: false,
            cssText: `
                body{
                    font-family: Calibri;
                    font-size: 8pt!important;
                }
                table {
                    font-size: 10pt!important;
                    border-collapse: collapse;
                }
                tr:last-child {
                    border-bottom: 1px solid gray;
                }
                td {
                    border-right: 1px solid gray;
                    border-left: 1px solid gray;
                }
                th {
                    border: 1px solid gray;
                }
                @page {
                    margin: 0.4in;
                    size: landscape;
                }
                `
        }
    },
    async created(){
        this.showModalTenants = true
    },
    methods: {
        previewReport(){
            this.$http.get('api/reports/tenantsmasterlist', {
            responseType: 'blob',
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            })
            .then((response) => {
                if(response.data.size > 0){
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'TenantsMasterList.xlsx');
                    document.body.appendChild(link);
                    link.click();
                }
            })
            .catch(error => {
                if (!error.response) return
                    console.log(error)
            })
        }
    },
  }
</script>


<style>
</style>

<template>
    <!--<b-animated fade-in>  main container -->
    <div>
        <div>
            <!-- <div class="animated fadeIn" ref="contractsMasterList">
                <b-row>
                    <b-col sm="12">
                        <b-card >
                            <table width="100%">
                                <tr style="border:none;">
                                    <td style="border: none" width="80%" class="align-center">
                                        <div style="font-size: 20pt;font-weight: 500;">{{company_info.company_name}}</div>
                                        <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.company_address}}</div>
                                        <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.email_address}}</div>
                                        <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.landline}}/{{company_info.mobile_number}}</div>
                                    </td>
                                    <td width="20%" style="object-fit: cover; border: none"><img :src="company_info.logo" style="height: 90px; width: 200px; text-align: right;"></td>
                                </tr>
                            </table>
                            <div style="font-size: 11pt; width: 100%;">
                                <div style="font-size: 16pt; font-weight: 500; text-align: center">CONTRACTS MASTER LIST</div>
                            </div>
                            <br>
                            <table style="width: 100%; margin-top: 5px;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;">Tenant Code</th>
                                        <th style="text-align:left;">Tenant</th>
                                        <th style="text-align:right;">Area (sqm)</th>
                                        <th style="text-align:center;">Space Code</th>
                                        <th style="text-align:center;">Commencement Date</th>
                                        <th style="text-align:center;">Termination Date</th>
                                        <th style="text-align:left;">Escalation Note</th>
                                        <th style="text-align:left;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="contract in contracts">
                                        <td style="text-align:left; width: 95px;">{{contract.tenant_code}}</td>
                                        <td style="text-align:left;">{{contract.trade_name}}</td>
                                        <td style="text-align:right;">{{formatNumber(contract.contract_floor_area)}}</td>
                                        <td style="text-align:center;">{{contract.space_code}}</td>
                                        <td style="text-align:center;">{{moment(contract.commencement_date, 'MMMM DD, YYYY')}}</td>
                                        <td style="text-align:center;">{{moment(contract.termination_date, 'MMMM DD, YYYY')}}</td>
                                        <td style="text-align:left;">{{contract.escalation_notes}}</td>
                                        <td style="text-align:left; width: 150px;">{{contract.contract_remarks}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                        </b-card>
                    </b-col>
                </b-row>
            </div> -->
        </div>
        <div>
            <b-modal
                @hidden="$router.go(-1)"
                v-model="showModalOrder"
                :noCloseOnEsc="true"
                :noCloseOnBackdrop="true"
                @shown="focusElement('order_by')"
            >
                <div slot="modal-title">
                    Contracts Master List
                </div>
                
                <b-row>
                    <b-col lg=12>
                        <b-form-group>
                            <label>Order By</label>
                            <select2
                                ref="order_by"
                                :allowClear="false"
                                :placeholder="'Order By'"
                                v-model="order_by"
                            >
                                <option value="tenant_code">Tenant Code</option>
                                <option value="space_code">Space Code</option>
                                <option value="trade_name">Trade Name</option>
                                <option value="commencement_date">Commencement Date</option>
                                <option value="termination_date">Termination Date</option>
                                <option value="escalation_notes">Escalation Notes</option>
                                <option value="contract_remarks">Remarks</option>
                            </select2>
                        </b-form-group>
                        <b-form-group>
                            <label>Order Type</label>
                            <b-form-radio-group v-model="order_type">
                                <b-form-radio value="ASC" id="asc"><label class="col-form-label" for="asc">Ascending</label></b-form-radio>
                                <b-form-radio value="DESC" id="desc"><label class="col-form-label" for="desc">Descending</label></b-form-radio>
                            </b-form-radio-group>
                        </b-form-group>
                    </b-col>
                </b-row>
                <div slot="modal-footer">
                    <b-button variant="primary" @click="previewReport()">
                        <i class="fa fa-check"></i>
                        OK
                    </b-button>
                    <b-button variant="secondary" @click="showModalOrder=false">Close</b-button>            
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
            showModalOrder: false,
            order_by: null,
            order_type: 'ASC',
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
        this.showModalOrder = true
    },
    methods: {
        previewReport(){
            if(this.order_by != null){
                // window.open('api/reports/contractsmasterlist/'+this.order_by+'/'+this.order_type)
                // let routeData = this.$router.resolve({name: 'Contracts Master List', query: {order_by: this.order_by, order_type: this.order_type}});
                // window.open(routeData.href, '_blank');
                // window.open('api/reports/contractsmasterlist/'+this.order_by+'/'+this.order_type)
                this.$http.get('api/reports/contractsmasterlist/'+this.order_by+'/'+this.order_type, {
                responseType: 'blob',
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
                })
                .then((response) => {
                    // console.log(response)
                    if(response.data.size > 0){
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', 'ContractsMasterlist.xlsx');
                        document.body.appendChild(link);
                        link.click();
                    }
                })
                .catch(error => {
                    if (!error.response) return
                        console.log(error)
                })
                // let routeData = this.$router.resolve({name: 'Contracts Master List', query: {order_by: this.order_by, order_type: this.order_type}});
                // window.open(routeData.href, '_blank');
            }
        }
    },
  }
</script>


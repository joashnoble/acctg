<style>
</style>

<template>
    <!--<b-animated fade-in>  main container -->
    <div hidden>
        <div>
            <div class="animated fadeIn" ref="newContracts">
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
                                <div style="font-size: 16pt; font-weight: 500; text-align: center">NEW CONTRACTS</div>
                                <div style="font-size: 12pt; font-weight: 500; text-align: center">As of {{moment(new Date(), 'MMMM DD, YYYY')}}</div>
                            </div>
                            <br>
                            <table style="width: 100%; margin-top: 5px;">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;">Contract No</th>
                                        <th style="text-align:left;">Trade Name</th>
                                        <th style="text-align:left;">Department</th>
                                        <th style="text-align:left;">Location</th>
                                        <th style="text-align:left;">Term Date</th>
                                        <!-- <th style="text-align:right;">Basic Rent</th>
                                        <th style="text-align:right;">Disc Rent</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="contract in contracts">
                                        <td style="text-align:left; width: 95px;">{{contract.contract_no}}</td>
                                        <td style="text-align:left;">{{contract.trade_name}}</td>
                                        <td style="text-align:left;">{{contract.department_desc}}</td>
                                        <td style="text-align:left;">{{contract.location_desc}}</td>
                                        <td style="text-align:left;">{{moment(contract.termination_date, 'MMM DD, YYYY')}}</td>
                                        <!-- <td style="text-align:right;">{{formatNumber(contract.contract_fixed_rent)}}</td>
                                        <td style="text-align:right;">{{formatNumber(contract.contract_discounted_rent)}}</td> -->
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                        </b-card>
                    </b-col>
                </b-row>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'newContracts',
    data () {
        return {
            company_info: [],
            contracts: [],
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
                    border-bottom: 1px solid #d6cfd0;
                }
                td {
                    padding: 3px;
                    border-right: 1px solid #d6cfd0;
                    border-left: 1px solid #d6cfd0;
                }
                th {
                    padding: 3px;
                    border: 1px solid #d6cfd0;
                }
                @page {
                    margin: 0.4in;
                }
                `
        }
    },
    async created(){
        await this.$http.get('/api/companysetting/1', {
        headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
        })
        .then((response) => {
            const res = response.data
            this.company_info = res.data
        })
        .catch(error => {
            if (!error.response) return
            console.log(error)
        })

        await this.$http.get('api/reports/newcontracts', {
            headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
        })
        .then((response) => {
            this.contracts = response.data.data
        })
        .catch(error => {
            if (!error.response) return
            console.log(error)
        })
        await this.d.print(this.$refs.newContracts, [this.cssText])
    },
    mounted(){
        const { Printd } = window.printd
        this.d = new Printd()

        const { contentWindow } = this.d.getIFrame()

        contentWindow.addEventListener(
            'afterprint', () => window.close()
        )
    },
    methods: {
        previewReport(){
            if(this.order_by != null){
                let routeData = this.$router.resolve({name: 'Contracts Master List', query: {order_by: this.order_by, order_type: this.order_type}});
                window.open(routeData.href, '_blank');
            }
        }
    },
  }
</script>


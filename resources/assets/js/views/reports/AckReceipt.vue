<style>
</style>

<template>
    <!--<b-animated fade-in>  main container -->
    <div hidden>
        <div class="animated fadeIn" ref="ackReceipt">
            <b-row>
                <b-col sm="12">
                    <b-card >
                        <table width="100%">
                            <tr>
                                <td style="border-bottom: none" width="80%" class="align-center">
                                    <div style="font-size: 20pt;font-weight: 500;">{{company_info.company_name}}</div>
                                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.company_address}}</div>
                                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.email_address}}</div>
                                    <div style="font-size: 11pt;font-weight: 500;margin-top: 0px;">{{company_info.landline}}/{{company_info.mobile_number}}</div>
                                </td>
                                <td width="20%" style="object-fit: cover; border-bottom: none"><img :src="company_info.logo" style="height: 90px; width: 200px; text-align: right;"></td>
                            </tr>
                        </table>
                        <div style="font-size: 11pt; width: 100%;">
                            <div style="font-size: 16pt; font-weight: 500; font-style: italic; text-align: center">ACKNOWLEDGEMENT RECEIPT</div>
                        </div>
                        <br>
                        <div style="font-size: 11pt; width: 100%;">
                            <div style="font-weight: 500;"><span style="float: left">Reference No : {{ payments.reference_no }}</span><span style="float: right">Txn Date : {{moment(payments.payment_date, 'MMMM DD, YYYY')}}</span></div>
                            <br><br>
                        </div>
                        <table style="width: 100%;" >
                            <tbody>
                                <tr>
                                    <td style="width: 23%">Received From</td>
                                    <td style="width: 2%">:</td>
                                    <td style="width: 75%">{{payments.trade_name}}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>:</td>
                                    <td>{{payments.head_office_address}}</td>
                                </tr>
                                <tr>
                                    <td>The Sum of Pesos (Php)</td>
                                    <td>:</td>
                                    <td :style="amount_paid_words.length > 92 ? 'font-size: 9pt!important;': ''">{{amount_paid_words}}</td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td>:</td>
                                    <td>{{formatNumber(payments.amount_paid)}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <br><br>
                        <div style="font-size: 11pt; width: 100%; font-weight: 500;">
                            <!-- <div style="padding: 0px 50px 5px;">
                                <div style="display: inline-block; width: 25%">
                                    Receive From
                                </div>
                                <div style="display: inline-block; width: 74%">
                                    : &nbsp;&nbsp;&nbsp;{{payments.trade_name}}
                                </div>
                            </div>
                            <div style="padding: 0px 50px 5px">
                                <div style="display: inline-block; width: 25%">
                                    Address
                                </div>
                                <div style="display: inline-block; width: 74%">
                                    : &nbsp;&nbsp;&nbsp;{{payments.head_office_address}}
                                </div>
                            </div>
                            <div style="padding: 0px 50px 5px">
                                <div style="position: relative; top: 0; left: 0; display: inline-block; width: 25%">
                                    The Sum of Pesos (Php)
                                </div>
                                <div style="display: inline-block; width: 74%">
                                    : &nbsp;&nbsp;&nbsp;{{amount_paid_words}}
                                </div>
                            </div>
                            <div style="padding: 0px 50px 5px">
                                <div style="display: inline-block; width: 25%">
                                    Amount
                                </div>
                                <div style="display: inline-block; width: 74%">
                                    : &nbsp;&nbsp;&nbsp;{{formatNumber(payments.amount_paid)}}
                                </div>
                            </div> -->
                            <!-- <br><br> -->
                            <div style="padding: 0px 50px 5px; text-align: center"><span style="float: left"><input type="checkbox" :checked="payments.payment_type == 0 ? true : false">Cash</span><input type="checkbox" :checked="payments.payment_type == 1 ? true : false">Check<span style="float: right"><input type="checkbox" :checked="payments.payment_type == 2 ? true : false">Online</span></div>
                        </div>
                        <br>
                        <div style="font-size: 11pt; width: 100%; font-weight: 500;">
                            <div style="display: inline-block; width: 50px"></div>
                            <div style="display: inline-block; border: 1px solid gray; width: 43%">
                                <div style="padding: 5px">For Check Only</div>
                                <div style="padding: 5px">Bank : <span v-if="payments.payment_type==1">{{payments.check_type_desc}}</span></div>
                                <div style="padding: 5px"><span>Check No : </span><span v-if="payments.payment_type==1">{{payments.check_no}}</span></div>
                                <div style="padding: 5px">Check Date : <span v-if="payments.payment_type==1">{{moment(payments.check_date, 'MMMM DD, YYYY')}}</span></div>
                            </div>
                            <div style="display: inline-block; width: 42%">
                                <div style="padding: 5px"></div>
                                <div style="padding: 5px"></div>
                                <div style="padding: 5px; text-align: center">_______________________________</div>
                                <div style="padding: 5px; text-align: center">Received By</div>
                            </div>
                            <div style="display: inline-block; width: 50px"></div>
                        </div>
                        <br>
                        <div style="font-size: 6pt; width: 100%;">
                            <div style="font-weight: 500;"><span style="float: left">Date Printed : {{ moment(new Date(),"MMMM DD, YYYY")}}</span><span style="float: right">Printed By : {{$store.state.user.firstname}}</span></div>
                            <br><br>
                        </div>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>
<script>
export default {
    name: 'ackReceipt',
    data () {
        return {
            company_info: [],
            payment_id: null,
            payments: [],
            amount_paid_words: '',
            cssText: `
                body{
                    font-family: Calibri;
                    font-size: 8pt!important;
                }
                table {
                    font-size: 11pt!important;
                    border-collapse: collapse;
                }
                th, td:last-child {
                    border-bottom: 1px solid gray;
                }
                tr td:first-child {
                    padding-left: 10px;
                }
                th {
                    background: lightgray;
                }
                .total {
                    background: lightgray;
                }
                .border-left {
                    border-left: 1px solid gray;
                }
                @page {
                    margin: 0.4in;
                }
                `
        }
    },
    async created(){
        this.payment_id = this.$route.query.payment_id
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
        await this.$http.get('api/payment/' + this.payment_id, {
                headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                const res = response.data.data
                this.payments = res
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
            await this.$http.get('api/payment/words/' + Number(this.payments.amount_paid).toFixed(2), {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('token')
                    }
            })
            .then((response) => {
                var words = response.data.split(' ')
                var new_word = ''
                words.forEach(element => {
                    new_word += element.charAt(0).toUpperCase() + element.slice(1) + ' '
                });

                this.amount_paid_words = new_word
            })
            .catch(error => {
                if (!error.response) return
                console.log(error)
            })
        
        await this.d.print(this.$refs.ackReceipt, [this.cssText])
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
        
    },
  }
</script>


<style scoped>
    h4, h6.h6-card { 
        text-align: center;
        color:white;
        text-transform: uppercase;
        font-family: "Segoe UI", "Source Sans Pro", Calibri, Candara, Arial, sans-serif;

    }
    h3, h5,h6 {
        font-family: "Segoe UI", "Source Sans Pro", Calibri, Candara, Arial, sans-serif;
        font-weight: 300;
    }
    h4 {
        font-weight: bold;
    }
</style>

<template>
<div style="position:relative; left:0;background-color:white;padding:20px; border-radius:10px;">
<b-row>
    <b-col>
        <h3><i class="fa fa-bar-chart" aria-hidden="true" style="color: #45aeda;"></i> <b style="color: #45aeda;">JCORE</b> Standard Accounting</h3>
        <h6>Here is a rundown of your business' performance and how your collections are doing individually.</h6>
    </b-col>
    <b-col>
        <h3>JDEV OFFICE SOLUTION INC.</h3>
        <h6>4776 Montang Ave., Diamond Subd., Balibago, Angeles City, Pampanga 2009</h6>
    </b-col>
</b-row>
<b-row>
    <b-col>
        <b-card style="background-color: #45aeda;">
            <h4>151,025.00</h4>
            <h6 class="h6-card">ACCOUNTS RECEIVABLES</h6>
        </b-card>
    </b-col>
    <b-col>
        <b-card style="background-color: #45aeda;">
            <h4>45,651.00</h4>
            <h6 class="h6-card">Accounts Payables</h6>
        </b-card>
    </b-col>
    <b-col>
        <b-card style="background-color: #45aeda;">
            <h4>621</h4>
            <h6 class="h6-card">Customers</h6>
        </b-card>
    </b-col>
    <b-col>
        <b-card style="background-color: #45aeda;">
            <h4>134</h4>
            <h6 class="h6-card">Suppliers</h6>
        </b-card>
    </b-col>

</b-row>
</div>
</template>
<script>
export default {
    name: 'dashboard',
    data () {
      return {
      }
    },
    methods: {
       
    },
    created () {
       
    }
}
</script>
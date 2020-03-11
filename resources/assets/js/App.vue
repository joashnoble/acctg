<template>
    <router-view></router-view>
</template>

<script>
export default {
  name: 'app',
    created() {
        if(localStorage.token != "" && localStorage.token != null) {
            this.$http.get('/api/auth/me', {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            },
            ).then(response => {
                this.$store.commit('loginUser')
                this.$store.commit('user', response.data)
            }).catch(error => {
                if (error.response.status === 401 || error.response.status === 403) {
                    this.$store.commit('logoutUser')
                    localStorage.setItem('token', '')
                    this.$router.push({name: 'Login'})
                }

            });
            this.$http.get('/api/session/rights', {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            }).then( response => {
                this.$store.commit('rights', response.data.data)
            }).catch(error => {
                console.log(error)
            })
        }
        else{
            this.$store.commit('logoutUser')
            localStorage.setItem('token', '')
            this.$router.push({name: 'Login'})
        }
    },
}
</script>
<style>
    .mx-datepicker {
        width: 100%;
    }

    /* required icon style */
    .i-required{
        color:red; 
        font-size:7px!important;
        text-align: center;
        vertical-align: 3px;

    }

    .form-control:not(textarea){
        height:calc(1.5em + 0.5rem + 5px)!important;
    }

    .input-group-prepend{
        height:calc(1.5em + 0.5rem + 5px)!important;
    }

    .input-group-text{
        font-size: 0.72rem!important
    }

    .input-group-append{
        height:calc(1.5em + 0.5rem + 5px)!important;
    }  
</style>


<style  lang="sass">
  $fa-font-path: "~font-awesome/fonts/";
  @import "~font-awesome/css/font-awesome.min.css";
  $simple-line-font-path: "~simple-line-icons/fonts/";
  @import "~simple-line-icons/css/simple-line-icons.css";
  @import "~select2/src/scss/core.scss";
  @import "~bootstrap-vue/dist/bootstrap-vue.css";
  @import '~bootstrap/scss/bootstrap.scss';
  @import "~select2-bootstrap4-theme/src/select2-bootstrap4.scss";
</style>
<style lang="scss">
  // Import Main styles for this application
  @import "../scss/style";
  label {
      font-weight: bold;
  }
  
  label.required:before {
      content: '* ';
      color: red;
  }

  .chartjs-tooltip {
  position: absolute;
  z-index: 1021;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-direction: column;
  flex-direction: column;
  padding: 0.25rem 0.5rem;
  color: #fff;
  pointer-events: none;
  background: rgba(0, 0, 0, 0.7);
  opacity: 0;
  transition: all 0.25s ease;
  -webkit-transform: translate(-50%, 0);
  transform: translate(-50%, 0);
  border-radius: 0.25rem;
}

.chartjs-tooltip .tooltip-header {
  margin-bottom: 0.5rem;
}

.chartjs-tooltip .tooltip-header-item {
  font-size: 0.765625rem;
  font-weight: 700;
}

.chartjs-tooltip .tooltip-body-item {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: center;
  align-items: center;
  font-size: 0.765625rem;
  white-space: nowrap;
}

.chartjs-tooltip .tooltip-body-item-color {
  display: inline-block;
  width: 0.875rem;
  height: 0.875rem;
  margin-right: 0.875rem;
}

.chartjs-tooltip .tooltip-body-item-value {
  padding-left: 1rem;
  margin-left: auto;
  font-weight: 700;
}

  
</style>

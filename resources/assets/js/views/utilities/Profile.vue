<template>
    <div>
        <notifications group="notification" />
        <div class="animated fadeIn">
            <b-row>
                <b-col sm="12">
                    <b-card class="card-accent-dark">
                        <h5 slot="header">
                            <span>
                                <!-- <i class="fa fa-bars"></i>  -->
                                Profile
                                <small class="font-italic">Update your user info.</small></span>
                        </h5>
                        <b-row>
                            <b-col sm=1></b-col>
                            <b-col sm=3 align-self="center">
                                <!-- <b-container class="border" style="height: 200px; width: 200px;"> -->
                                <center>    
                                    <b-row>
                                        <b-col lg=12>
                                            <b-img fluid thumbnail :src="forms.user.fields.path" height="200px" width="200px" />
                                        </b-col>
                                    </b-row>
                                    <!-- </b-container> -->
                                    <b-row>
                                        <b-col lg=12>
                                            <b-form-file @change="fieldChange" ref="file" accept=".jpg, .png, .gif" plain style="display: none;"></b-form-file>
                                            <b-btn class="mt-2 mr-1" variant="success" @click="$refs.file.$el.click()">
                                                <i class="fa fa-file-image-o"></i> Browse
                                            </b-btn>
                                            <b-btn class="mt-2" variant="danger" @click="$refs.file.reset(), forms.user.fields.path = 'uploads/users/default.png'">
                                                <i class="fa fa-times"></i> Remove
                                            </b-btn>
                                        </b-col>
                                    </b-row>
                                </center>
                            </b-col>
                            <b-col sm=3>
                                <b-form-group>
                                    <label class="required" for="user_code">Username</label>
                                    <b-form-input
                                        v-model="forms.user.fields.username"
                                        ref="username"
                                        id="username"
                                        debounce="250"
                                        type="text"
                                        placeholder="Username">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group description="Old password is required to verify your account.">
                                    <label class="required">Old Password</label>
                                    <b-form-input
                                        v-model="forms.user.fields.old_password"
                                        ref="old_password"
                                        id="old_password"
                                        debounce="250"
                                        type="password"
                                        placeholder="Old Password">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group description="Update Password (Please leave it blank if you do not want to update your password)">
                                    <label>New Password</label>
                                    <b-form-input
                                        v-model="forms.user.fields.password"
                                        ref="password"
                                        id="password"
                                        debounce="250"
                                        type="password"
                                        placeholder="New Password">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Confirm Password</label>
                                    <b-form-input
                                        v-model="forms.user.fields.password_confirmation"
                                        ref="password_confirmation"
                                        id="password_confirmation"
                                        debounce="250"
                                         type="password"
                                        placeholder="Confirm Password">
                                    </b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col sm=3>
                                <b-form-group>
                                    <label class="required">Email</label>
                                    <b-form-input
                                        v-model="forms.user.fields.email"
                                        ref="email"
                                        id="email"
                                        debounce="250"
                                        type="text"
                                        placeholder="Email">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Firstname</label>
                                    <b-form-input
                                        v-model="forms.user.fields.firstname"
                                        id="firstname"
                                        debounce="250"
                                        type="text"
                                        placeholder="Firstname">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Middlename</label>
                                    <b-form-input
                                        v-model="forms.user.fields.middlename"
                                        id="middlename"
                                        debounce="250"
                                        type="text"
                                        placeholder="Middlename">
                                    </b-form-input>
                                </b-form-group>
                                <b-form-group>
                                    <label>Lastname</label>
                                    <b-form-input
                                        v-model="forms.user.fields.lastname"
                                        id="lastname"
                                        debounce="250"
                                        type="text"
                                        placeholder="Lastname">
                                    </b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col sm=2></b-col>
                        </b-row>
                        <template v-slot:footer>
                            <b-row class="pull-right mt-2">
                                <b-col sm="12">
                                    <b-button 
                                        variant="success" 
                                        @click="onProfileUpdate">
                                        <i class="fa fa-check"></i>
                                        Save
                                    </b-button>
                                </b-col>
                            </b-row>
                        </template>
                    </b-card>
                </b-col>
            </b-row>
        </div>
    </div>
</template>
<script>
export default {
    name: 'profile',
    data() {
        return {
            forms: {
                user: {
                    fields: {
                        id: null,
                        username: null,
                        old_password: null,
                        password: null,
                        password_confirmation: null,
                        email: null,
                        firstname: null,
                        middlename: null,
                        lastname: null,
                        path: null
                    }
                }
            },
            image: new FormData,
        }
    },
    created(){
        this.$http.get('api/user/profile', {
            headers: {
                Authorization: 'Bearer ' + localStorage.getItem('token')
            }
        }).then(response => {
            this.forms.user.fields = response.data.data
        }).catch(error => {
            console.log(error)
        });
    },
    methods: {
        fieldChange(e){
            let attachment = e.target.files[0]
            let path = 'uploads/users'

            this.image.append('file', attachment)
            this.image.append('path', path)
            this.image.append('id', this.forms.user.fields.id)

            this.$http.post('/api/upload', this.image, {
                headers: {
                      Authorization: 'Bearer ' + localStorage.getItem('token'),
                      'Content-Type' : 'multipart/form-data'
                  }
            })
            .then((response) => {
                this.forms.user.fields.path = response.data.path
            })
            .catch(error => {
              if (!error.response) return
              console.log(error)
            })
        },
        onProfileUpdate(){
            this.$http.post('api/user/profile/update', this.forms.user.fields, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            }).then(response => {
                this.$notify({
                    type: 'success',
                    group: 'notification',
                    title: 'Success!',
                    text: 'Profile successfully updated.'
                })
                this.$store.commit('user', response.data.data)
                this.forms.user.fields.old_password = null
                this.forms.user.fields.password = null
                this.forms.user.fields.password_confirmation = null
            }).catch(error => {
                if (!error.response) return
                const errors = error.response.data.errors
                var a = 0
                for (var key in errors) {
                // this.forms[entity].states[key] = false
                // this.forms[entity].errors[key] =  errors[key]
                
                    if(a == 0){
                        this.focusElement(key)
                        this.$notify({
                            type: 'error',
                            group: 'notification',
                            title: 'Error!',
                            text: errors[key][0]
                        })
                    }
                    
                a++
                }
            })
        }
    }
}
</script>
<template>
    <div> <!-- modal div -->
        <b-modal 
            v-model="showModalEntry"
            :noCloseOnEsc="true"
            :noCloseOnBackdrop="true"
            @shown="focusElement('department_code')"
        >
        
            <div slot="modal-title"> <!-- modal title -->
                Department Entry - {{entryMode}}
            </div> <!-- modal title -->

            <b-col lg=12> <!-- modal body -->
                <b-form @keydown="resetFieldStates('department')" autocomplete="off">
                    <b-form-group>
                        <label class="required" for="department_code">Department Code</label>
                        <b-form-input
                            id="department_code"
                            v-model="forms.department.fields.department_code"
                            debounce="250"
                            type="text"
                            placeholder="Department Code"
                            ref="department_code">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Department Desc</label>
                        <b-form-input
                            ref="department_desc"
                            id="department_desc"
                            v-model="forms.department.fields.department_desc"
                            debounce="250"
                            type="text"
                            placeholder="Department Description">
                        </b-form-input>
                    </b-form-group>
                    <b-form-group>
                        <label class="required">Department Address</label>
                        <b-form-textarea
                            ref="department_address"
                            id="department_address"
                            v-model="forms.department.fields.department_address"
                            debounce="250"
                            type="text"
                            placeholder="Department Address">
                        </b-form-textarea>
                    </b-form-group>
                    <b-row>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 1</label>
                                <b-form-input
                                    ref="signatory_1"
                                    id="signatory_1"
                                    v-model="forms.department.fields.signatory_1"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 1">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 1 Position</label>
                                <b-form-input
                                    ref="signatory_1_position"
                                    id="signatory_1_position"
                                    v-model="forms.department.fields.signatory_1_position"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 1 Position">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 2</label>
                                <b-form-input
                                    ref="signatory_2"
                                    id="signatory_2"
                                    v-model="forms.department.fields.signatory_2"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 2">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 2 Position</label>
                                <b-form-input
                                    ref="signatory_2_position"
                                    id="signatory_2_position"
                                    v-model="forms.department.fields.signatory_2_position"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 2 Position">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 3</label>
                                <b-form-input
                                    ref="signatory_3"
                                    id="signatory_3"
                                    v-model="forms.department.fields.signatory_3"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 3">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                        <b-col lg=6>
                            <b-form-group>
                                <label class="required">Signatory 3 Position</label>
                                <b-form-input
                                    ref="signatory_3_position"
                                    id="signatory_3_position"
                                    v-model="forms.department.fields.signatory_3_position"
                                    debounce="250"
                                    type="text"
                                    placeholder="Signatory 3 Position">
                                </b-form-input>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col lg=4>
                            <b-form-group>
                                <label class="required">Start Day</label>
                                <select2
                                    ref="start_day"
                                    :allowClear="false"
                                    :placeholder="'Start Day'"
                                    v-model="forms.department.fields.start_day"
                                >
                                    <option value="0">Last Day</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select2>
                            </b-form-group>
                        </b-col>
                        <b-col lg=4>
                            <b-form-group>
                                <label class="required">End Day</label>
                                <select2
                                    ref="end_day"
                                    :allowClear="false"
                                    :placeholder="'End Day'"
                                    v-model="forms.department.fields.end_day"
                                >
                                    <option value="0">Last Day</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select2>
                            </b-form-group>
                        </b-col>
                        <b-col lg=4>
                            <b-form-group>
                                <label></label>
                                <b-form-checkbox
                                    id="is_same"
                                    v-model="forms.department.fields.is_same"
                                    value="1"
                                    unchecked-value="0">
                                    is Same Month?
                                </b-form-checkbox>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col lg=4>
                            <b-form-group>
                                <label class="required">Cut Off Day</label>
                                <select2
                                    ref="cut_off_day"
                                    :allowClear="false"
                                    :placeholder="'Cut Off Day'"
                                    v-model="forms.department.fields.cut_off_day"
                                >
                                    <option value="0">None</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select2>
                            </b-form-group>
                        </b-col>
                    </b-row>
                </b-form>

                
            </b-col> <!-- modal body -->

            <div slot="modal-footer"><!-- modal footer buttons -->
                <b-button :disabled="forms.department.isSaving" variant="success" @click="onDepartmentEntry">
                    <icon v-if="forms.department.isSaving" name="sync" spin></icon>
                    <i class="fa fa-check"></i>
                    Save
                </b-button>
                <b-button variant="danger" @click="showModalEntry=false">Close</b-button>
            </div> <!-- modal footer buttons -->

        </b-modal>
    </div> <!-- modal div -->
</template>
<script>
export default {
    name: 'departmententry',
    props: ['type'],
    data() {
        return {
            entryMode: 'Add',
            showModalEntry: false,
            forms:{
                department : {
                    isSaving: false,
                    fields: {
                        department_id: null,
                        department_code: null,
                        department_desc: null,
                        signatory_1: null,
                        signatory_1_position: null,
                        signatory_2: null,
                        signatory_2_position: null,
                        signatory_3: null,
                        signatory_3_position: null,
                        start_day: 1,
                        end_day: 1,
                        is_same: 0,
                        cut_off_day: 1,
                        department_address: null
                    }
                }
            },
            row: []
        }
    },
    methods:{
        onDepartmentEntry () {
            if(this.type == 'reference'){
                if(this.entryMode == 'Add'){
                    this.$parent.createEntityRef('department', true, 'departments', 'departmententry')
                }
                else{
                    this.$parent.updateEntityRef('department', 'department_id', true, this.row, 'departmententry')
                }
            }
            else{
                this.$parent.createOptionsEntityRef('department', 'showModalEntry', 'departments', this.type, 'department_id', 'departmententry')
            }
        },
        setCreate(){
            this.entryMode='Add'
            this.clearFields('department')
            this.forms.department.fields.start_day = 1
            this.forms.department.fields.end_day = 1
            this.forms.department.fields.cut_off_day = 1
            this.forms.department.fields.is_same = 1
            this.showModalEntry = true
        },
        setUpdate(data){
            this.row = data.item
            this.resetFieldStates('department')
            this.fillEntityForm('department', data.item.department_id, 'showModalEntry')
            this.entryMode='Edit'
        }
    },
}
</script>
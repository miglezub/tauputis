<template>
    <div>
        <b-modal ref="showPaymentModal" centered hide-footer v-on:close="closeModal()">
            <template v-slot:modal-title>
                {{ getTitle() }}
            </template>
            <!--
            <template v-slot:modal-header>
                <div v-text="getTitle()" class="font-weight-bold text-break" style="font-size: 24px"></div>
                <b-button size="sm" variant="outline-danger" v-on:click="closeModal()">
                    X
                </b-button>
            </template>
            -->
            <b-container fluid class="ml-2">
                <b-row>
                    <b-col md="8" sm="12">
                        <div v-if="selected_payment.caption || edit" class="form-group">
                            <label class="font-weight-bold mb-0" for="caption" style="font-size: 16px">Pavadinimas</label>
                            <b-form-group v-if="edit" id="caption-group" label-for="caption">
                                <b-form-input
                                id="caption"
                                v-model="selected_payment.caption"
                                type="text"
                                placeholder="Įveskite pavadinimą (neprivalomas)"
                                maxlength="100"
                                ></b-form-input>
                            </b-form-group>
                            <div v-else v-text="selected_payment.caption"></div>
                            <!--
                            <input v-if="edit" v-model="selected_payment.caption" class="form-control" type="text" maxlength="100">
                            <p v-if="edit_errors.includes('caption')" class="text-danger">
                                Pavadinimas negali būti ilgesnis nei 100 simbolių.
                            </p>
                            -->
                        </div>
                        <div v-if="selected_payment.description || edit" class="form-group">
                            <label class="font-weight-bold mb-0" for="description" style="font-size: 16px">Aprašymas</label>
                            <b-form-group v-if="edit" id="description-group" label-for="description">
                                <b-form-textarea
                                id="description"
                                v-model="selected_payment.description"
                                type="text"
                                placeholder="Įveskite aprašymą (neprivalomas)"
                                maxlength="255"
                                ></b-form-textarea>
                            </b-form-group>
                            <div v-else v-text="selected_payment.description"></div>
                            <!--
                            <input v-if="edit" v-model="selected_payment.description" class="form-control" type="text" maxlength="255">
                            <p v-if="edit_errors.includes('description')" class="text-danger">
                                Aprašymas negali būti ilgesnis nei 255 simboliai.
                            </p>
                            -->
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold mb-0" for="value" style="font-size: 16px">Suma</label>
                            <b-form-group v-if="edit" id="value-group" label-for="value"
                                :invalid-feedback="invalidFeedbackValue()"
                                :state="stateValue()">
                                <b-form-input
                                    id="value"
                                    required
                                    v-model="selected_payment.value"
                                    type="number"
                                    placeholder="Įveskite sumą"
                                    :min="getMin()"
                                    :max="getMax()"
                                    step="0.01"
                                    :state="stateValue()"
                                    trim
                                ></b-form-input>
                            </b-form-group>
                            <div v-else v-text="selected_payment.value"></div>
                            <!--
                            <input v-if="edit" v-model="selected_payment.value" class="form-control" type="number"
                                step="0.01" :min="getMin()" :max="getMax()">
                            <p v-if="edit_errors.includes('value') && getMin() == 0.01" class="text-danger">
                                Suma turi būti teigiamas skaičius, kadangi mokėjimo tipas įplaukos.
                            </p>
                            <p v-if="edit_errors.includes('value') && getMin() != 0.01" class="text-danger">
                                Suma turi būti neigiamas skaičius, kadangi mokėjimo tipas išlaidos.
                            </p>
                            -->
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold mb-0" for="date" style="font-size: 16px">Data</label>
                            <b-form-group v-if="edit" id="date-group" label-for="date">
                                <b-form-input
                                id="date"
                                v-model="selected_payment.date"
                                type="date"
                                placeholder="Pasirinkite datą"
                                :max="date_to"
                                ></b-form-input>
                            </b-form-group>
                            <div v-else v-text="selected_payment.date"></div>
                            <!--
                            <input v-if="edit" v-model="selected_payment.date" class="form-control" type="date"
                                :max="date_to">
                                -->
                        </div>
                        <div v-if="selected_payment.fk_payment_type_id != 1" class="form-group">
                            <label class="font-weight-bold mb-0" for="type" style="font-size: 16px">Kategorija</label>
                            <b-form-group v-if="edit" id="type-group" label-for="type">
                            <b-form-select
                                id="type"
                                v-model="selected_payment.fk_payment_type_id"
                                required
                                >
                                    <b-form-select-option v-for="pm_type in payment_types" v-if="pm_type.id != 1" 
                                        :key="pm_type.id" :value="pm_type.id">{{ pm_type.name }}
                                    </b-form-select-option>
                            </b-form-select>
                        </b-form-group>
                            <!--
                            <select name="type" v-if="edit" v-model="selected_payment.fk_payment_type_id" class="form-control">
                                <option v-for="pm_type in payment_types"  :key="pm_type.id" v-if="pm_type.id !=1" :value="pm_type.id">{{ pm_type.name }}</option>
                            </select>
                            -->
                            <div v-else v-text="getTypeName()"></div>
                        </div>
                    </b-col>
                    <b-col  md="4" sm="12" class="text-center">
                        <button class="btn bg-main-teal w-75 mb-1 mb-md-0" v-on:click="editPayment()" v-text="edit ? 'Išsaugoti' : 'Redaguoti'"></button>
                        <button class="btn bg-p1-red w-75 mt-md-1" v-on:click="$refs['confirmDeleteModal'].show()">Ištrinti</button>
                    </b-col>
                </b-row>
            </b-container>
        </b-modal>


        <b-modal ref="confirmDeleteModal" centered hide-header ok-title="Taip" ok-variant="danger" cancel-title="Ne, atšaukti"
            v-on:ok="deleteSelected()">
            Ar tikrai norite ištrinti mokėjimą?
        </b-modal>
    </div>
</template>

<script>
export default {
    props: {
        payment_types: { type: Array, required: true },
        selected_payment: { required: true },
        selected_type: { type: Number, required: true }
    },

    data() {
        return {
            show_payment: false,
            selected_id: 0,
            edit: false,
            date_to: new Date().toISOString().slice(0,10),
            confirm_delete: '',
            edit_errors: [],
        }
    },
    
    methods: {
        getTitle() {
            return this.selected_payment.caption || this.payment_types[this.selected_payment.fk_payment_type_id-1].name || "";
        },

        getMin() {
            return this.selected_payment.is_income == 0 ? -999999.99 : 0.01;
        },

        getMax() {
            return this.selected_payment.is_income == 0 ? -0.01 : 999999.99;
        },

        getTypeName() {
            if(this.selected_payment.fk_payment_type_id)
                return this.payment_types[this.selected_payment.fk_payment_type_id-1].name;
            else
                return this.payment_types[this.selected_type-1].name;
        },

        closeModal() {
            this.$refs['showPaymentModal'].hide();
            this.edit = false;
            this.edit_errors = [];
        },

        deleteSelected() {
            axios.delete(`/payment/delete/${this.selected_payment.id}`)
                .then(response => {
                    this.$emit('reload');
                    this.closeModal();
                });
        },

        editPayment() {
            if(this.edit == false) {
                this.edit = true;
            }
            else {
                //delete
                if(this.validate()) {
                    axios
                        .post(`/payments/update/${this.selected_payment.id}`, this.selected_payment)
                        .then(response => {
                            this.edit = false;
                            this.edit_errors = [];
                            this.$emit('reload');
                        });
                }                
            }
        },

        validate() {
            if(this.selected_payment.caption != null && this.selected_payment.caption.length > 100) {
                this.edit_errors.push('caption');
                return false;
            }
            if(this.selected_payment.description != null && this.selected_payment.description.length > 255) {
                this.edit_errors.push('description');
                return false;
            }
            if(this.selected_payment.value < this.getMin() || this.selected_payment.value > this.getMax()) {
                this.edit_errors.push('value');
                return false;
            }
            return true;
        },

        stateValue() {
            return this.selected_payment.value >= this.getMin() &&  this.selected_payment.value <= this.getMax() ? true : false
        },
        invalidFeedbackValue() {
            if (this.selected_payment.value < 0 && this.getMin() > 0) {
                return 'Suma turi būti teigiamas skaičius';
            }
            else if (this.selected_payment.value > 0 && this.getMin() < 0) {
                return 'Suma turi būti neigiamas skaičius';
            }
            else if (this.selected_payment.value.length == 0) {
                return 'Įveskite sumą';
            } else {
            return 'Suma turi įeiti į intervalą nuo ' + this.getMin() + ' iki ' + this.getMax();
            }
        },
    }
}
</script>
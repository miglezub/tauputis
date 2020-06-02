<template>
    <div>
        <b-modal ref="showPeriodicPaymentModal" centered hide-footer v-on:close="closeModal()">
            <template v-slot:modal-title>
                {{ getTitle() }}
            </template>
            <b-container fluid class="ml-2">
                <b-row>
                    <b-col cols="8">
                        <label class="font-weight-bold mb-0" for="caption" style="font-size: 16px">Periodiškumas</label>
                        <b-form-group
                            :state="statePeriodicType()"
                            :invalid-feedback="invalidFeedbackPeriodicType()"
                            v-if="edit"
                            class="mb-1">
                            <b-form-select
                                id="period_type"
                                v-model="selected_payment.periodic_type"
                                required
                                :state="statePeriodicType()"
                                v-on:change="resetDay()"
                                >
                                    <b-form-select-option value="null" disabled>Pasirinkite...</b-form-select-option>
                                    <b-form-select-option value="1">Kas mėnesį</b-form-select-option>
                                    <b-form-select-option value="2">Kas savaitę</b-form-select-option>
                            </b-form-select>
                        </b-form-group>
                        <div v-else v-text="selected_payment.periodic_type == 1 ? 'Kas mėnesį' : 'Kas savaitę'" class="mb-1"></div>
                        <label class="font-weight-bold mb-0" for="caption" style="font-size: 16px">Diena</label>
                        <b-form-group 
                            v-if="selected_payment.periodic_type == 1 && edit"
                            :state="statePeriodicDay()"
                            :invalid-feedback="invalidFeedbackPeriodicDay()"
                            class="mb-1">
                            <b-form-input
                                id="period_type"
                                v-model="selected_payment.periodic_day"
                                required
                                type="number"
                                min="1"
                                max="28"
                                step="1"
                                :state="statePeriodicDay()"
                                placeholder="Pasirinkite mėnesio dieną">
                            </b-form-input>
                        </b-form-group>

                        <b-form-group
                            v-if="selected_payment.periodic_type == 2 && edit">
                            <b-form-select
                                id="period_type"
                                v-model="selected_payment.periodic_day"
                                required
                                >
                                    <b-form-select-option value="1">Pirmadienį</b-form-select-option>
                                    <b-form-select-option value="2">Antradienį</b-form-select-option>
                                    <b-form-select-option value="3">Trečiadienį</b-form-select-option>
                                    <b-form-select-option value="4">Ketvirtadienį</b-form-select-option>
                                    <b-form-select-option value="5">Penktadienį</b-form-select-option>
                                    <b-form-select-option value="6">Šeštadienį</b-form-select-option>
                                    <b-form-select-option value="0">Sekmadienį</b-form-select-option>
                            </b-form-select>
                        </b-form-group>
                        <div v-if="!edit" v-text="periodic_day()" class="mb-1"></div>

                        <p v-if="edit && !selected_payment.caption && !selected_payment.description">
                            <a v-b-toggle.collapse-1 class="text-main-teal font-weight-bold mb-3"
                            style="text-decoration: underline">
                            Papildoma informacija
                            </a>
                        </p>

                        <b-collapse id="collapse-1" class="mt-2" 
                            :visible="selected_payment.caption || selected_payment.description ? true : false">
                            <div v-if="selected_payment.caption || edit">
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
                            </div>
                            <div v-if="selected_payment.description || edit">
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
                            </div>
                        </b-collapse>

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
                        <div v-else v-text="selected_payment.value.toFixed(2)"></div>

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
                            <div v-else v-text="getTypeName()"></div>
                        </div>
                    </b-col>
                    <b-col cols="4">
                        <button class="btn bg-main-teal w-75" v-on:click="editPayment()" v-text="edit ? 'Išsaugoti' : 'Redaguoti'"></button>
                        <button class="btn bg-p1-red w-75 mt-1" v-on:click="$refs['confirmDeleteModal'].show()">Ištrinti</button>
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
            input_period_type: 0,
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

        periodic_day() {
            if(this.selected_payment.periodic_type == 1)
                return this.selected_payment.periodic_day;
            else return this.weekDay();
        },

        resetDay() {
            this.input_period_type++;
            this.selected_payment.periodic_day = Math.round(this.selected_payment.periodic_day);
            if(this.selected_payment.periodic_type == 2 && (this.selected_payment.periodic_day > 6 || this.selected_payment.periodic_day < 0))
                this.selected_payment.periodic_day = 1;
            if(this.selected_payment.periodic_type == 1 && (this.selected_payment.periodic_day < 1 || this.selected_payment.periodic_day > 28))
                this.selected_payment.periodic_day = 1;
        },

        weekDay() {
            switch (this.selected_payment.periodic_day) {
                case 0:
                    return "Sekmadienis";
                case 1:
                    return "Pirmadienis";
                case 2:
                    return "Antradienis";
                case 3:
                    return "Trečiadienis";
                case 4:
                    return "Ketvirtadienis";
                case 5:
                    return "Penktadienis";
                case 6:
                    return "Šeštadienis";
            }
        },

        closeModal() {
            this.$refs['showPeriodicPaymentModal'].hide();
            this.edit = false;
            this.edit_errors = [];
        },

        resetSelected() {
            this.selected= {
                caption: '',
                description: '',
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
                periodic_type: null,
                periodic_day: null,
            }
        },

        deleteSelected() {
            axios.delete(`/periodic/delete/${this.selected_payment.id}`)
                .then(response => {
                    this.$emit('reload');
                    this.resetSelected();
                    this.closeModal();
                });
        },

        editPayment() {
            if(this.edit == false) {
                this.edit = true;
            }
            else {
                if(this.validate()) {
                    axios
                        .post(`/periodic/update/${this.selected_payment.id}`, this.selected_payment)
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
            if(this.selected_payment.periodic_type == null)
                return false;
            this.selected_payment.periodic_day = Math.round(this.selected_payment.periodic_day);
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

        statePeriodicType() {
            if(this.input_period_type == 0)
                return null;
            return this.selected_payment.periodic_type == 1 || this.selected_payment.periodic_type == 2 ? true : false;
        },
        invalidFeedbackPeriodicType() {
            return "Pasirinkite periodiškumą";
        },

        statePeriodicDay() {
            if(this.selected_payment.periodic_day < 1 || this.selected_payment.periodic_day > 28)
                return false;
            return true;
        },
        invalidFeedbackPeriodicDay() {
            return "Galite pasirinkti dienas nuo 1 iki 28";
        },
    }
}
</script>
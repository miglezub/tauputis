<template>
    <div class="mt-3">
        <b-button class="btn bg-p1-dark float-right" v-on:click="$refs['addPeriodicPaymentModal'].show()">
            Pridėti periodinį mokėjimą
        </b-button>
        <b-modal ref="addPeriodicPaymentModal" centered hide-footer title="Mokėjimo pridėjimas">

            <b-form>
                <b-form-group label="Mokėjimo tipas:">
                    <b-form-radio-group
                        v-model="payment.is_income"
                        :options="payment_options"
                        name="payment_type"
                        buttons
                        button-variant="outline-success"
                        v-on:input="validate()"
                    ></b-form-radio-group>
                </b-form-group>

                <b-form-group label="Periodiškumas"
                    :state="statePeriodicType()"
                    :invalid-feedback="invalidFeedbackPeriodicType()">
                    <b-form-select
                        id="period_type"
                        v-model="payment.period_type"
                        required
                        :state="statePeriodicType()"
                        v-on:change="resetDay()"
                        >
                            <b-form-select-option value="null" disabled>Pasirinkite...</b-form-select-option>
                            <b-form-select-option value="1">Kas mėnesį</b-form-select-option>
                            <b-form-select-option value="2">Kas savaitę</b-form-select-option>
                    </b-form-select>
                </b-form-group>

                <b-form-group 
                    v-if="payment.period_type == 1" label="Pasirinkite dieną"
                    :state="statePeriodicDay()"
                    :invalid-feedback="invalidFeedbackPeriodicDay()">
                    <b-form-input
                        id="period_type"
                        v-model="payment.period_day"
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
                    v-if="payment.period_type == 2"
                    v-on:>
                    <b-form-select
                        id="period_type"
                        v-model="payment.period_day"
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
                
                <a v-b-toggle.collapse-1 class="text-main-teal font-weight-bold mb-3"
                    style="text-decoration: underline">Papildoma informacija</a>
                <b-collapse id="collapse-1" class="mt-2">
                    <b-form-group id="caption-group" label="Pavadinimas:" label-for="caption">
                        <b-form-input
                        id="caption"
                        v-model="payment.caption"
                        type="text"
                        placeholder="Įveskite pavadinimą (neprivalomas)"
                        maxlength="100"
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group id="description-group" label="Aprašymas:" label-for="description">
                        <b-form-textarea
                        id="description"
                        v-model="payment.description"
                        type="text"
                        placeholder="Įveskite aprašymą (neprivalomas)"
                        maxlength="255"
                        ></b-form-textarea>
                    </b-form-group>
                </b-collapse>


                <b-form-group id="value-group" label="Suma:" label-for="value"
                    :invalid-feedback="invalidFeedbackValue()"
                    :state="stateValue()">
                    <b-form-input
                        id="value"
                        required
                        v-model="payment.value"
                        type="number"
                        placeholder="Įveskite sumą"
                        :min="getMin()"
                        :max="getMax()"
                        step="0.01"
                        :state="stateValue()"
                        number
                        v-on:focus="input_value++"
                    ></b-form-input>
                </b-form-group>

                <b-form-group v-if="payment.is_income == 0" id="type-group" label="Kategorija:" label-for="type"
                    :invalid-feedback="invalidFeedbackType()"
                    :state="stateType()">
                    <b-form-select
                        id="type"
                        v-model="payment.payment_type"
                        required
                        :state="stateType()"
                        v-on:change="input_type++"
                        >
                            <b-form-select-option value="null" disabled>Pasirinkite...</b-form-select-option>
                            <b-form-select-option v-for="pm_type in payment_types" v-if="pm_type.id != 1" 
                                :key="pm_type.id" :value="pm_type.id">{{ pm_type.name }}
                            </b-form-select-option>
                    </b-form-select>
                </b-form-group>
                
                <b-button class="bg-p1-red" v-on:click="resetPayment()">Anuliuoti</b-button>
                <b-button class="btn-success" v-on:click="addPayment()">Pridėti</b-button>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import { bus } from '../../app.js'
export default {
    props: {
        payment_types: { type: Array, nullable: false }
    },

    data() {
        return {
            payment: {},
            payment_options: [
                { text: 'Išlaidos', value: 0 },
                { text: 'Įplaukos', value: 1 },
            ],
            input_value: 0,
            input_type: 0,
            input_period_type: 0,
        }
    },

    created() {
        this.resetPayment();
    },

    methods: {
        getMin() {
            return 0.01;
        },

        getMax() {
            return 999999.99;
        },

        validate() {
            this.input_type++;
            this.input_period_type++;
            this.input_value++;
            if(this.payment.caption != null && this.payment.caption.length > 100)
                return false;
            if(this.payment.description != null && this.payment.description.length > 255)
                return false;
            if(this.payment.value == null || this.payment.value < this.getMin() || this.payment.value > this.getMax())
                return false;
            if(this.payment.is_income == 0 && 
                (this.payment.payment_type > 12 || this.payment.payment_type < 2))
                    return false;
            if(this.payment.period_type == null)
                return false;
            this.payment.period_day = Math.round(this.payment.period_day);
            return true;
        },

        resetPayment() {
            this.payment = {
                caption: '',
                description: '',
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
                period_type: null,
                period_day: 1,
            };
            this.input_type = 0;
            this.input_value = 0;
            this.input_period_type = 0;
        },

        resetDay() {
            this.input_period_type++;
            this.payment.period_day = Math.round(this.payment.period_day);
            if(this.payment.period_type == 2 && (this.payment.period_day > 6 || this.payment.period_day < 0))
                this.payment.period_day = 1;
        },

        addPayment() {
            if (this.validate()) {
                this.payment.type = (this.payment.is_income == 0 ? this.payment.payment_type : 1);
                axios
                    .post(`http://127.0.0.1:8000/periodic/store`, this.payment)
                    .then(response => {
                        this.$refs['addPeriodicPaymentModal'].hide();
                        bus.$emit('reloadNotifications', 'added/deleted payment');
                        this.$emit('reload');
                        this.resetPayment();
                    });
            }
        },

        stateValue() {
            if(this.input_value == 0) {
                return null;
            }
            return this.payment.value > 0 && this.payment.value <= 999999.99 ? true : false
        },
        invalidFeedbackValue() {
            if (this.payment.value < 0) {
            return 'Suma turi būti teigiamas skaičius';
            } else if (this.payment.value.length == 0) {
            return 'Įveskite sumą';
            } else {
            return 'Suma turi įeiti į intervalą nuo ' + this.getMin() + ' iki ' + this.getMax();
            }
        },

        stateType() {
            if(this.input_type == 0) {
                return null;
            }
            if(this.payment.payment_type == null) {
                return false;
            }
            return this.payment.is_income == 0 && (
                this.payment.payment_type > 12 || this.payment.payment_type < 2) ? false : true;
        },
        invalidFeedbackType() {
            return "Pasirinkite kategoriją";
        },

        statePeriodicType() {
            if(this.input_period_type == 0)
                return null;
            return this.payment.period_type == 1 || this.payment.period_type == 2 ? true : false;
        },
        invalidFeedbackPeriodicType() {
            return "Pasirinkite periodiškumą";
        },

        statePeriodicDay() {
            if(this.payment.period_day < 1 || this.payment.period_day > 28)
                return false;
            return true;
        },
        invalidFeedbackPeriodicDay() {
            return "Galite pasirinkti dienas nuo 1 iki 28";
        },
    }
}
</script>
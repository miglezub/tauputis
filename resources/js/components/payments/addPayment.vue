<template>
    <div class="float-right mt-3">
        <button class="btn bg-main-teal" v-on:click="$refs['addPaymentModal'].show()">Pridėti mokėjimą</button>
        <b-modal ref="addPaymentModal" centered hide-footer title="Mokėjimo pridėjimas">

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

                <b-form-group id="date-group" label="Data:" label-for="date"
                    :invalid-feedback="invalidFeedbackDate()"
                    :state="stateDate()">
                    <b-form-input
                    id="date"
                    v-model="payment.date"
                    type="date"
                    placeholder="Pasirinkite datą"
                    :max="today"
                    :state="stateDate()"
                    ></b-form-input>
                </b-form-group>

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
                            <b-form-select-option v-for="pm_type in payment_type_options" v-if="pm_type.id != 1" 
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
export default {
    props: {
        payment_type_options: { type: Array, nullable: false }
    },

    data() {
        return {
            payment: {},
            payment_options: [
                { text: 'Išlaidos', value: 0 },
                { text: 'Įplaukos', value: 1 },
            ],
            today: new Date().toISOString().slice(0,10),
            input_value: 0,
            input_type: 0,
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
            if(this.payment.caption != null && this.payment.caption.length > 100)
                return false;
            if(this.payment.description != null && this.payment.description.length > 255)
                return false;
            if(this.payment.value == null || this.payment.value < this.getMin() || this.payment.value > this.getMax())
                return false;
            if(this.payment.is_income == 0 && 
                (this.payment.payment_type > 12 || this.payment.payment_type < 2)) {
                    this.input_type++;
                    return false;
                }
            if(this.payment.date > this.today)
                return false;
            return true;
        },

        resetPayment() {
            this.payment = {
                caption: '',
                description: '',
                date: this.today,
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
            };
            this.input_type = 0;
            this.input_value = 0;
        },

        addPayment() {
            if (this.validate()) {
                this.payment.payment_type = (this.payment.is_income == 0 ? this.payment.payment_type : 1);
                //this.payment.value = (this.payment.is_income == 0 ? -this.payment.value : this.payment.value);
                axios
                    .post(`http://127.0.0.1:8000/payments/store`, this.payment)
                    .then(response => {
                        this.$emit('reload');
                        this.$refs['addPaymentModal'].hide();
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

        stateDate() {
            return this.payment.date <= this.today ? null : false;
        },
        invalidFeedbackDate() {
            return "Pasirinkta data negali būti vėliau negu šiandien";
        },
    }
}
</script>
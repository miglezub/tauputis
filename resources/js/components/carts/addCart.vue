<template>
    <div class="float-right mt-3">
        <button class="btn bg-main-teal" v-on:click="$refs['addCartModal'].show()">Pridėti krepšelį</button>
        <b-modal ref="addCartModal" centered hide-footer title="Krepšelio pridėjimas">
            <b-form>
                <b-form-group label="Kategorija" id="type-group" label-for="type"
                    :invalid-feedback="invalidFeedbackType()"
                    :state="stateType()">
                    <b-form-select
                        id="type"
                        v-model="cart.fk_type"
                        required
                        v-on:change="input_type++"
                        :state="stateType()"
                        >
                            <b-form-select-option value="null" disabled>Pasirinkite...</b-form-select-option>
                            <b-form-select-option v-for="type in available_types" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </b-form-select-option>
                    </b-form-select>
                </b-form-group>

                <b-form-group label="Numatyta mėnesio suma:" id="value-group" label-for="value"
                    :invalid-feedback="invalidFeedbackValue()"
                    :state="stateValue()">
                    <b-form-input
                        id="value"
                        required
                        v-model="cart.value"
                        type="number"
                        placeholder="Įveskite sumą"
                        :min="0.01"
                        :max="999999.99"
                        step="0.01"
                        number
                        :state="stateValue()"
                        v-on:focus="input_value++">
                    </b-form-input>
                </b-form-group>

                <b-form-group label="Išlaidų perkėlimas į kitą mėnesį:" id="transfer-group" label-for="transfer"
                    :invalid-feedback="invalidFeedbackTransfer()"
                    :state="stateTransfer()">
                    <b-form-select
                        id="transfer"
                        v-model="cart.transfer_balance"
                        required
                        :state="stateTransfer()"
                        >
                            <b-form-select-option value="null" disabled>Pasirinkite...</b-form-select-option>
                            <b-form-select-option value="0">Išjungtas</b-form-select-option>
                            <b-form-select-option value="1">Įjungtas</b-form-select-option>
                    </b-form-select>
                </b-form-group>

                <b-button class="bg-main-teal" v-on:click="addCart()">Pridėti</b-button>
            </b-form>
        </b-modal>
    </div>
    <!--
    <div class="modal fade" id="addCartModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h2 class="font-weight-bold">Krepšelio pridėjimas</h2>
                    <label for="fk_type" class="col-form-label">Kategorija:</label>
                    <select name="fk_type" id="fk_type" class="form-control w-75" v-model="cart.fk_type">
                        <option value="null" default>Pasirinkite...</option>
                        <option v-for="type in available_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                    <p v-if="errors.fk_type" v-text="errors.fk_type" class="font-weight-bold text-danger mb-0"></p>

                    <label for="monthly_value" class="col-form-label">Numatyta mėnesio suma:</label>
                    <input id="value" type="number" step="0.01" min="0" max="99999999.99" class="form-control w-75"
                        v-model="cart.value">
                    <p v-if="errors.value" v-text="errors.value" class="font-weight-bold text-danger mb-0"></p>

                    <label for="transfer" class="col-form-label">Išlaidų perkėlimas į kitą mėnesį:</label>
                    <select name="transfer" id="transfer" class="form-control w-75" v-model="cart.transfer_balance">
                        <option value="null" default>Pasirinkite...</option>
                        <option value="0">Išjungtas</option>
                        <option value="1">Įjungtas</option>
                    </select>
                    <p v-if="errors.transfer_balance" v-text="errors.transfer_balance" class="font-weight-bold text-danger mb-0"></p>

                    <button class="btn bg-main-teal my-3" type="button" v-on:click="addCart()">Pridėti</button>
                </div>
            </div>
        </div>
    </div>
    -->
</template>

<script>
export default {
    props: {
        available_types: { type: Array, required: true },
    },

    data() {
        return {
            cart: {
                fk_type: 'null',
                value: 0.01,
                transfer_balance: 'null',
            },
            errors: {
                fk_type: '',
                value: '',
                transfer_balance: '',
            },
            input_value: 0,
            input_type: 0,
            input_transfer: 0,
        }
    },

    methods: {
        addCart() {
            if(this.validateCart()) {
                axios
                    .post(`http://127.0.0.1:8000/carts/add`, this.cart)
                    .then(response => {
                        this.$emit('added');
                        this.$refs['addCartModal'].hide()
                    });
            }
        },

        validateCart() {
            this.input_value++;
            this.input_type++;
            this.input_transfer++;
            if(this.cart.fk_type == "null" || !this.available_types.some(item => item.id === this.cart.fk_type)) {
                //this.errors.fk_type = "Pasirinkite krepšelio kategoriją.";
                return false;
            }
            if(this.cart.value <= 0 || this.cart.value > 99999999.99) {
                return false;
            }
            if(this.cart.transfer_balance == "null") {
                return false;
            }
            if(this.cart.transfer_balance != 0 && this.cart.transfer_balance != 1)
                return false;
            return true;
        },

        stateType() {
            if(this.input_type == 0) {
                return null;
            }
            if(this.available_types.some(item => item.id === this.cart.fk_type)) {
                return true;
            }
            return false;
        },
        invalidFeedbackType() {
            return "Pasirinkite kategoriją";
        },

        stateValue() {
            if(this.input_value == 0) {
                return null;
            }
            return this.cart.value > 0 && this.cart.value <= 999999.99 ? true : false
        },
        invalidFeedbackValue() {
            if (this.cart.value < 0) {
                return 'Suma turi būti teigiamas skaičius';
            } else if (this.cart.value.length == 0) {
                return 'Įveskite sumą';
            } else {
                return 'Suma turi įeiti į intervalą nuo 0.01 iki 999999.99';
            }
        },

        stateTransfer() {
            if(this.input_transfer == 0) {
                return null;
            }
            if(this.cart.transfer_balance == 0 || this.cart.transfer_balance == 1) {
                return true;
            }
            return false;
        },
        invalidFeedbackTransfer() {
            return "Pasirinkite išlaidų perkėlimo tipą";
        },
    },
}
</script>

<style scoped>
    label {
        font-size: 20px;
        font-weight: 500;
    }
</style>
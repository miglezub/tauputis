<template>
    <div class="mb-3">
        <div class="card h-100">
            <p class="card-header font-weight-bold">
                <span class="float-right">{{ getWeekBalance() }}</span>
            </p>
            <div class="card-body">
                <div v-for="day in group" :key="day.id">
                    <p class="font-weight-bold border-bottom mb-0" v-text="day[0].date + ', ' + getDayName(day[0].date)"></p>
                    <span class="float-right text-secondary">{{ getBalance(day) }}</span>
                    <br>
                    <div v-for="payment in day" :key="payment.id" class="pl-2 ml-lg-4 mb-4" id="paymentDiv"
                        v-on:click="showPayment(payment.id)">
                        <div v-if="payment.caption" v-text="payment.caption" class="text-secondary"></div>
                        <div v-text="payment_types[payment.fk_payment_type_id-1].name" class="font-weight-bold h5"></div>
                        <div v-text="round(payment.value)" class="h5"></div>
                    </div>
                </div>
            </div>
        </div>
        <show-payment ref="modal" :payment_types="payment_types" :selected_payment="selected_payment" :selected_type="selected_type"
            @reload="$emit('reload')"/>
    </div>
</template>

<script>
export default {
    props: {
        group: { required: true },
        payment_types: { type: Array, required: true }
    },

    data() {
        return {
            show_payment: false,
            selected_type: 1,
            selected_payment: {
                caption: '',
                description: '',
                date: this.today,
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
            },
            edit: false,
            date_to: new Date().toISOString().slice(0,10),
            confirm_delete: '',
            edit_errors: [],
        }
    },

    methods: {
        getDayName(date) {
            var d = new Date(date).getDay();
            switch (d){
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
                case 0:
                    return "Sekmadienis";
                default:
                    return "";
            };
        },

        showPayment(payment_id) {            
            axios.get(`/payments/${payment_id}`)
                .then(response => {
                    this.selected_payment = response.data;
                    this.show_payment = true;
                    this.selected_type = this.selected_payment.fk_payment_type_id;
                    this.$refs['modal'].$refs['showPaymentModal'].show();
                })
                .catch(error => {
                    console.log(error);
                });
                
        },

        round(value) {
            return parseFloat(value).toFixed(2);
        },

        getBalance(day) {
            return parseFloat(day.reduce(function(sum, payment) {
                return sum + payment.value;
            }, 0)).toFixed(2);
        },

        getWeekBalance() {
            
            var balance = 0;
            $.each(this.group, function () {
                $.each(this, function() {
                    balance += this.value;
                });
                return balance;
            });
            return parseFloat(balance).toFixed(2);
        },
    }
}
</script>

<style>
    #paymentDiv:hover {
        background-color: #f7f7f7;
        border-radius: 7px;
        cursor: pointer; 
    }
</style>
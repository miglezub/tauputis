<template>
    <div class="container row border-bottom ml-1 pb-2 px-1">
        <div class="col-md-6 px-lg-4 px-0">
            <p class="h5">Šio mėnesio išlaidų/įplaukų statusas</p>
            <b-progress :max="max_this_month" height="2rem" v-if="max_this_month > 0">
                <b-progress-bar :value="data.thisMonthExpenses" :class="variant_this_month">
                    <strong style="font-size: 14px;">
                        <div v-if="data.thisMonthExpenses/data.thisMonthIncome*100 >= 30">{{ data.thisMonthExpenses }} / {{ data.thisMonthIncome }}</div>
                    </strong>
                </b-progress-bar>
            </b-progress>
            <b-progress :max="100" variant="secondary" height="2rem" v-else>
                <b-progress-bar :value="100">
                    <a href="/payments" class="text-white" style="font-size: 16px;">Šį mėnesį nėra pridėta mokėjimų</a>
                </b-progress-bar>
            </b-progress>
            <div>
                Išlaidos: <span class="font-weight-bold">{{ data.thisMonthExpenses }}</span>
                <span class="float-right">Įplaukos: <span class="font-weight-bold">{{ data.thisMonthIncome }}</span></span>
                <br>
                <p v-if="data.thisMonthExpenses > data.thisMonthIncome">
                    Išlaidos viršija pajamas 
                    <span v-if="data.thisMonthIncome > 0" class="font-weight-bold">
                        {{ (data.thisMonthExpenses/data.thisMonthIncome*100 - 100).toFixed(2) }}%
                    </span>
                </p>
                <p v-else-if="data.thisMonthIncome > 0 && data.thisMonthExpenses >0">
                    Išlaidos sudaro <span class="font-weight-bold">{{ (data.thisMonthExpenses/data.thisMonthIncome*100).toFixed(2) }}%</span> įplaukų.
                </p>
            </div>
        </div>
        <div class="col-md-6 mt-3 mt-md-0 px-lg-4 px-0">
            <p class="h5">Praeito mėnesio išlaidų/įplaukų statusas</p>
            <b-progress :max="max_last_month" height="2rem" v-if="max_last_month > 0">
                <b-progress-bar :value="data.lastMonthExpenses" :class="variant_last_month">
                    <strong style="font-size: 14px;">
                        <div v-if="data.lastMonthExpenses/data.lastMonthIncome*100 >= 30">{{ data.lastMonthExpenses }} / {{ data.lastMonthIncome }}</div>
                    </strong>
                </b-progress-bar>
            </b-progress>
            <b-progress :max="100" variant="secondary" height="2rem" v-else>
                <b-progress-bar :value="100">
                    <a href="/payments" class="text-white" style="font-size: 16px;">Šį mėnesį nėra pridėta mokėjimų</a>
                </b-progress-bar>
            </b-progress>
            <div>
                Išlaidos: <span class="font-weight-bold">{{ data.lastMonthExpenses }}</span>
                <span class="float-right">Įplaukos: <span class="font-weight-bold">{{ data.lastMonthIncome }}</span></span>
                <br>
                <p v-if="data.lastMonthExpenses > data.lastMonthIncome">
                    Išlaidos viršija pajamas 
                    <span v-if="data.lastMonthIncome > 0" class="font-weight-bold">
                        {{ (data.lastMonthExpenses/data.lastMonthIncome*100 - 100).toFixed(2) }}%
                    </span>
                </p>
                <p v-else-if="data.lastMonthIncome > 0 && data.lastMonthExpenses > 0">
                    Išlaidos sudaro <span class="font-weight-bold">{{ (data.lastMonthExpenses/data.lastMonthIncome*100).toFixed(2) }}%</span> įplaukų.
                </p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            data: [],
            variant_this_month: "primary",
            variant_last_month: "primary",
            max_this_month: 0,
            max_last_month: 0,
            max: 100,
            value: 20.546,
        }
    },

    created() {
        this.fetchData();
    },

    methods: {
        fetchData() {
            axios.get('http://127.0.0.1:8000//apipayments/stats')
                .then(response => {
                    this.data = response.data;
                    this.setVariables();
                })
                .catch(error => {
                    console.log(error);
                });
        },

        setVariables() {
            if(this.data.thisMonthExpenses > this.data.thisMonthIncome) {
                this.variant_this_month = "bg-p1-red";
                this.max_this_month = this.data.thisMonthExpenses;
            }
            else if(this.data.thisMonthExpenses/this.data.thisMonthIncome > 0.8) {
                this.variant_this_month = "bg-p1-dark hover-dark";
                this.max_this_month = this.data.thisMonthIncome;
            }
            else {
                this.variant_this_month = "bg-main-teal";
                this.max_this_month = this.data.thisMonthIncome;
            }

            if(this.data.lastMonthExpenses > this.data.lastMonthIncome) {
                this.variant_last_month = "bg-p1-red";
                this.max_last_month = this.data.lastMonthExpenses;
            }
            else if(this.data.lastMonthExpenses/this.data.lastMonthIncome > 0.8) {
                this.variant_last_month = "bg-p1-dark hover-dark";
                this.max_last_month = this.data.lastMonthIncome;
            }
            else {
                this.variant_last_month = "bg-main-teal";
                this.max_last_month = this.data.lastMonthIncome;
            }
        },
    },
}
</script>
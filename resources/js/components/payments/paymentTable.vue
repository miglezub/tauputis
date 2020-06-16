<template>
  <div class="container">
        <div v-if="showBalance" class="ml-2">
            Filtruotas balansas: <span class="font-weight-bold">{{ getBalance() }}</span>
        </div>
        <div class="container" style="height: 70px">
            <add-payment class="float-right mr-1 mb-2" :payment_type_options="payment_types" @reload="update()"/>
        </div>
        <div v-if="loaded">
            <div class="row justify-content-center ml-2 mr-2">
                <div class="col-lg-4 col-md-6">
                    <select name="filter_type" v-model="filterType" class="custom-select mb-2 mr-1 w-100">
                        <option value="0">Visi</option>
                        <option v-for="pm_type in payment_types" :key="pm_type.id" :value="pm_type.id">{{ pm_type.name }}</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <select name="filter_date" v-model="filterDate" class="custom-select mb-2 mr-1 w-100">
                        <option value="0">Visas laikotarpis</option>
                        <option value="1">Ši savaitė</option>
                        <option value="2">Praeita savaitė</option>
                        <option value="3">Šis mėnuo</option>
                        <option value="4">Praeitas mėnuo</option>
                        <option value="5">Šie metai</option>
                        <option value="6">Praeiti metai</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-12">
                    <button  class="btn bg-p1-dark mb-2 w-100" v-on:click="update()">Filtruoti</button>
                </div>
            </div>
            <div v-if="payments.length == 0 && filterDate == 0 && filterType == 0" class="text-center mb-5">
                <p class="h4 mt-5">Nėra pridėtų mokėjimų</p>
            </div>
            <div v-else-if="payments.length > 0">
                <b-table
                id="payment-table"
                striped hover fixed
                :items="payments"
                :fields="useMobileFields ? fieldsMobile : fields"
                :per-page="perPage"
                :current-page="currentPage"
                @row-clicked="click"
                >
                    <template v-slot:cell(value)="data">
                        {{ round(data.item.value) }}
                    </template>
                    <template v-slot:cell(payment_type)="data">
                        {{ payment_types[data.item.fk_payment_type_id-1].name }}
                    </template>
                </b-table>

                <b-pagination
                v-model="currentPage"
                :total-rows="rows"
                :per-page="perPage"
                aria-controls="payment-table"
                class="justify-content-center"
                ></b-pagination>
            </div>
            <p v-else class="h4 text-center mt-5">Nerasta mokėjimų pagal nurodytus filtro kriterijus</p>
            <show-payment ref="modal" @reload="update()" 
                :payment_types="payment_types" :selected_payment="selected" :selected_type="selected_type" />
        </div>
        <div v-else class="text-center mb-5">
            <div class="spinner-grow text-main-teal" role="status" style="width: 50px; height: 50px;">
            <span class="sr-only">Loading...</span>
            </div>
        </div>
  </div>
</template>

<script>
import { bus } from '../../app.js'
export default {
    props: {
        payment_types: { type: Array, required: true},
    },

    data() {
        return {
            payments: [],
            selected: {
                caption: '',
                description: '',
                date: this.today,
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
            },
            selected_type: 1,
            width: 700,
            loaded: false,
            showBalance: false,
            perPage: 5,
            currentPage: 1,
            filterType: 0,
            filterDate: 0,
            fields: [
            {
                key: 'date',
                label: 'Data',
                sortable: true
            },
            {
                key: 'caption',
                label: 'Pavadinimas',
                sortable: false
            },
            {
                key: 'payment_type',
                label: 'Kategorija',
                sortable: false
            },
            {
                key: 'value',
                label: 'Suma',
                sortable: true,
            }
            ],
            fieldsMobile: [
            {
                key: 'date',
                label: 'Data',
                sortable: true
            },
            {
                key: 'payment_type',
                label: 'Kategorija',
                sortable: false
            },
            {
                key: 'value',
                label: 'Suma',
                sortable: true,
            }
            ],
            useMobileFields: false,
        }
    },

    created() {
        window.addEventListener("resize", this.getScreenWidth);
        this.width = window.innerWidth;
        if (this.width > 700)
            this.useMobileFields = false;
        else
            this.useMobileFields = true;
        this.fetchData();
        bus.$on('reloadTable', (data) => {
            this.fetchData();
        });
    },

    destroyed() {
        window.removeEventListener("resize", this.getScreenWidth);
    },

    computed: {
        rows() {
            return this.payments.length
        }
    },

    methods: {
        fetchData() {
            axios.get(`/apipayments?filter_type=${this.filterType}&filter_date=${this.filterDate}`)
                .then(response => {
                this.payments = response.data;
                this.loaded = true;
                if(this.filterType != 0 || this.filterDate != 0)
                    this.showBalance = true;
                else
                    this.showBalance = false;
                })
                .catch(error => {
                    console.log(error);
                });
        },

        update() {
            this.fetchData();
            bus.$emit('reloadList', 'added/deleted payment');
        },

        click(item) {
            this.selected = item;
            this.selected_type = item.fk_payment_type_id;
            this.$refs['modal'].$refs['showPaymentModal'].show();
        },

        getBalance() {
            var balance = 0;
            $.each(this.payments, function () {
                balance += parseFloat(this.value);
            });
            return parseFloat(balance).toFixed(2);
        },

        getScreenWidth(event) {
            this.width = window.innerWidth;
            if (this.width > 700)
                this.useMobileFields = false;
            else
                this.useMobileFields = true;
        },

        round(value) {
            return parseFloat(value).toFixed(2);
        }
    }
}
</script>
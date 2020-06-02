<template>
  <div class="container">
      <div class="container float-right mb-3">
        <add-periodic-payment :payment_types="payment_types" class="mt-4" @reload="fetchData()"/>
      </div>
        <div v-if="loaded">
            <div v-if="payments.length == 0" class="text-center mb-5">
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
                    <template v-slot:cell(periodic_type)="data">
                        {{ getType(data.item.periodic_type) }}
                    </template>
                    <template v-slot:cell(periodic_day)="data">
                        {{ getDay(data.item.periodic_day, data.item.periodic_type) }}
                    </template>
                    <template v-slot:cell(value)="data">
                        {{ data.item.value.toFixed(2) }}
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
        </div>
        <div v-else class="text-center mb-5">
            <div class="spinner-grow text-main-teal" role="status" style="width: 50px; height: 50px;">
            <span class="sr-only">Loading...</span>
            </div>
        </div>
        <show-periodic ref="modal" :payment_types="payment_types" :selected_payment="selected" 
            :selected_type="selected_type" @reload="fetchData(); show_id = -1" />
  </div>
</template>

<script>
import { bus } from '../../app.js'
export default {
    props: {
        payment_types: { type: Array, required: true},
        show_id: { type: Number, required: true},
    },

    data() {
        return {
            payments: [],
            selected: {
                caption: '',
                description: '',
                is_income: 0,
                value: 0.01,
                payment_type: null,
                type: null,
                periodic_type: null,
                periodic_day: null,
            },
            selected_type: 1,
            width: 700,
            loaded: false,
            perPage: 5,
            currentPage: 1,
            fields: [
            {
                key: 'periodic_type',
                label: 'Periodiškumas',
                sortable: false
            },
            {
                key: 'periodic_day',
                label: 'Diena',
                sortable: false
            },
            {
                key: 'payment_type',
                label: 'Kategorija',
                sortable: true
            },
            {
                key: 'value',
                label: 'Suma',
                sortable: true,
            }
            ],
            fieldsMobile: [
            {
                key: 'periodic_type',
                label: 'Periodiškumas',
                sortable: false
            },
            {
                key: 'payment_type',
                label: 'Kategorija',
                sortable: true
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
        if (this.width > 750)
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
            axios.get(`/periodic`)
                .then(response => {
                this.payments = response.data;
                this.loaded = true;
                bus.$emit('reloadNotifications', 'added/deleted payment');
                if(this.show_id != -1) {
                    this.selected = this.payments.find(x => x.id == this.show_id);
                    this.selected_type = this.selected.fk_payment_type_id;
                    this.$refs['modal'].$refs['showPeriodicPaymentModal'].show();
                }
                else
                    this.resetSelected()
                })
                .catch(error => {
                    console.log(error);
                });
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

        getType(type) {
            if(type == 1)
                return "Kas mėnesį";
            else
                return "Kas savaitę";
        },

        getDay(day, type) {
            if(type == 1)
                return day;
            else
                return this.weekDay(day);
        },

        weekDay(day) {
            switch (day) {
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

        click(item) {
            this.selected = item;
            this.selected_type = item.fk_payment_type_id;
            this.$refs['modal'].$refs['showPeriodicPaymentModal'].show();
        },

        getScreenWidth(event) {
            this.width = window.innerWidth;
            if (this.width > 700)
                this.useMobileFields = false;
            else
                this.useMobileFields = true;
        }
    }
}
</script>
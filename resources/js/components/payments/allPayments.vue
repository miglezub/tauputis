<template>
    <div class="container mx-3">
        <div class="container" style="height: 70px">
            <add-payment class="float-right mb-3" :payment_type_options="payment_types" @reload="update()"/>
        </div>
        <div v-if="loaded && grouped_payments.length != []">
            <div v-if="width > 800" class="row">
                <button class="btn btn-light col-2 col-md-1 mb-md-3" v-if="page > 1" v-on:click="page--; loadPage()">
                    <span class="material-icons">chevron_left</span>
                </button>
                <div v-else class="col-1"></div>
                <payment-group v-for="group in page_payments" :key="group.id" :group="group" :payment_types="payment_types" class="col-md-5"
                    @reload="update()"/>
                <button class="btn btn-light m-3 mx-md-0 mb-md-3 mt-md-0 col-2 col-md-1" 
                    v-on:click="page++; loadPage()" v-if="page < last_page">
                    <span class="material-icons">chevron_right</span>
                </button>
            </div>
            <div v-else>
                <payment-group :group="grouped_payments[keys[current_id]]" :payment_types="payment_types"
                    @reload="update()"/>
                <div class="text-center mb-2">
                    <button class="btn btn-light mr-1" v-if="current_id > 0" v-on:click="current_id--">
                        <span class="material-icons">chevron_left</span>
                    </button>
                    <button class="btn btn-light" v-if="current_id < max_id" v-on:click="current_id++">
                        <span class="material-icons">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
        <div v-else-if="!loaded" class="text-center mb-5">
            <div class="spinner-grow text-main-teal" role="status" style="width: 50px; height: 50px;">
            <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div v-else class="text-center mb-5">
            <p class="h4">Nėra pridėtų mokėjimų</p>
        </div>
    </div>
</template>

<script>
import { bus } from '../../app.js'
export default {
    props: {
        payment_types: { type: Array, required: true },
    },

    data() {
        return {
            grouped_payments: [],
            page_payments: [],
            last_page: 1,
            max_id: 1,
            keys: [],
            current_id: 0,
            page: 1,
            loaded: false,
            width: 993,
        }
    },

    created() {
        window.addEventListener("resize", this.getScreenWidth);
        this.width = window.innerWidth;
        this.fetchData();
        bus.$on('reloadList', (data) => {
            this.fetchData();
        });
    },

    destroyed() {
        window.removeEventListener("resize", this.getScreenWidth);
    },

    methods: {
        fetchData() {
            axios.get(`http://127.0.0.1:8000/apigroupedpayments`)
                .then(response => {
                this.grouped_payments = response.data;
                this.loaded = true;
                this.keys = Object.keys(this.grouped_payments);
                this.max_id = this.keys.length -1;
                this.last_page = Math.ceil(this.keys.length / 2);
                this.loadPage();
                })
                .catch(error => {
                    console.log(error);
                });
        },

        update() {
            this.fetchData();
            bus.$emit('reloadTable', 'added/deleted payment');
        },

        getScreenWidth(event) {
            this.width = window.innerWidth;
        },

        loadPage() {
            this.page_payments = [];
            this.page_payments.push(this.grouped_payments[this.keys[this.page * 2 - 2]]);
            if(this.keys[this.page * 2 - 1] != null)
                this.page_payments.push(this.grouped_payments[this.keys[this.page * 2 - 1]]);
        },
    }
}
</script>
<template>
    <div class="container">
        <div class="container mb-2" style="height: 50px;" v-if="available_types.length > 0">
            <!--
            <button type="button" class="btn bg-main-teal float-right" style="height: 40px; font-size: 18px;"
                v-on:click="toggleAdd()">
                <i class="material-icons align-text-top">add_circle_outline</i> Pridėti
            </button>
            -->
            <add-cart :available_types="available_types" @added="fetchData()"></add-cart>
        </div>
        <!--div v-if="add">
            <add-cart :available_types="available_types" @added="addedCart()"></add-cart>
        </div -->
        <div v-if="loaded && carts.length > 0" class="container px-0 px-md-2" id="cartDisplay" ref="cartDisplay">
            <div v-for="cart in carts" :key="cart.id">
                <cart :cart="cart" :paymentType="payment_types[cart.fk_type-1]" :balance="balances[cart.fk_type] || 0"></cart>
            </div>
        </div>
        <h4 v-else-if="loaded" class="text-center">Nėra pridėtų krepšelių</h4>
        <div v-else class="text-center mb-5">
            <div class="spinner-grow text-main-teal" role="status" style="width: 50px; height: 50px;">
            <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        payment_types: { type: Array, required: true },
        balances: { required: true },
    },
    data() {
        return {
            carts: [],
            add: false,
            loaded: false,
            available_types: [],
        }
    },

    created() {
        this.fetchData();
        //console.log(this.carts);
    },

    methods: {
        fetchData() {
            axios.get("/apicarts")
                .then(response => {
                this.carts = response.data;
                this.available_types = this.payment_types;
                this.loaded = true;
                this.getAvailableTypes();
                })
                .catch(error => {
                    console.log(error);
                });
        },

        toggleAdd() {
            this.add = true;
            $('#addCartModal').modal({
                show: true
            });
        },

        getAvailableTypes() {
            this.carts.forEach(cart => {
                this.removeType(cart.fk_type);
            });
            this.removeType(1);
            //console.log(this.available_types);
        },

        removeType(cart) {
            this.available_types = this.available_types.filter(type => {
                return type.id != cart;
            });
        },
    },
    name: 'allCarts'
}
</script>

<style scoped>
    #progress{
        height: 14px;
    }
</style>
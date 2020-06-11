<template>
    <div class="px-0 px-md-2 col-lg-6 float-left mb-3">
        <div class="col-12" id="main-div">

            <div class="row mt-3">
                <h3 class="w-75 ml-4 border-bottom">{{ paymentType.name }}</h3>
                <button v-if="edit" v-on:click="cancelEdit()" 
                    class="btn bg-p1-red btn-sm ml-4" style="line-height: 1">
                    <i class="material-icons text-center">cancel</i>
                </button>
                <button id="edit_btn" class="btn bg-main-teal btn-sm ml-4" :disabled="edit"
                    v-on:click="startEdit()" v-else>
                    <i class="material-icons">edit</i>
                </button>
            </div>
            

            <p class="pt-1">Numatytos išlaidos: 
                <input id="value" type="number" step="0.01" min="0" max="99999999.99" v-model="cart.monthly_goal"
                    class="form-control w-50" v-if="edit" v-on:input="validate()">
                <span v-else v-text="cart.monthly_goal" class="font-weight-bold"></span>
                <span v-if="edit_error" v-text="edit_error" class="text-danger"></span>
                <br>
                Išlaidų perkėlimas į kitą mėnesį: 
                <select v-if="edit" name="transfer" id="transfer" class="form-control w-50" v-model="cart.transfer_balance">
                    <option value="false">Išjungtas</option>
                    <option value="true">Įjungtas</option>
                </select>
                <span v-else class="font-weight-bold" v-text="transferBalanceStatus()"></span>
            </p>
            <button v-if="edit" @click="saveEdit()" class="d-block mb-2 btn bg-main-teal">Išsaugoti</button>

            <label for="balance">Mėnesio balanso likutis:</label>
            <!-- monthly balance does not exceed monthly goal -->
            <div v-if="!exceeds()">
                <div class="row mb-0">
                    <div class="progress w-75 ml-3" id="progress"> 
                        <div class="progress-bar bg-main-teal" role="progressbar" 
                        :aria-valuenow="getBalance()" aria-valuemin="0" :aria-valuemax="cart.monthly_goal"
                        :style="{ width: getBalance()*100/cart.monthly_goal + '%'}">{{ getBalance() }}</div>
                    </div>
                    <p v-text="'/' + cart.monthly_goal" class="pl-1 align-top mb-1" style="font-size: 12px;"></p>
                </div>
                <p>
                    Šio mėnesio išleista suma: <span class="font-weight-bold">{{ balance.toFixed(2) }}</span> neviršija numatytos.
                    <br>
                    <span v-if="cart.transfer_balance">
                        Praeito mėnesio suma, perskaičiuota šiam mėnesiui: 
                        <span class="font-weight-bold">{{ (getBalance()-balance).toFixed(2) }}</span>
                    </span>
                    <span v-else>Išlaidų perskaičiavimas iš praeito mėnesio išjungtas.</span>
                    <br class="mb-2">
                    Dar galima išleisti: 
                    <span class="font-weight-bold">{{ (cart.monthly_goal-getBalance()).toFixed(2) }}</span>
                </p>
            </div>
            <!-- monthly balance exceeds monthly goal -->
            <div v-else>
                <div class="row">
                    <div class="progress w-75 ml-3" id="progress"> 
                        <div class="progress-bar bg-p1-red-no-hover" role="progressbar" 
                        :aria-valuenow="getBalance()" aria-valuemin="0" :aria-valuemax="cart.monthly_goal"
                        style="width: 100%">{{ getBalance() }}</div>
                    </div>
                    <p v-text="'/' + cart.monthly_goal" class="pl-1 align-top mb-1" style="font-size: 12px;"></p>
                </div>
                <p>Šio mėnesio išleista suma: 
                    <span class="font-weight-bold">{{ balance.toFixed(2) }}</span> 
                    <span v-if="balance > cart.monthly_goal">viršija numatytą!</span>
                    <span v-else>neviršija numatytos.</span>
                    <br>
                    <span v-if="cart.transfer_balance">
                        Praeito mėnesio suma, perskaičiuota šiam mėnesiui: 
                        <span class="font-weight-bold">{{ (getBalance()-balance).toFixed(2) }}</span>
                    </span>
                    <span v-else>Išlaidų perskaičiavimas iš praeito mėnesio išjungtas.</span>
                    <br class="mb-2">
                    Likutis: <span class="font-weight-bold">{{(getBalance()-cart.monthly_goal).toFixed(2)}}</span>
                        <span v-if="cart.transfer_balance"> bus </span>
                        <span v-else> nebus </span>
                        perkeltas į kitą mėnesį.
                </p>
            </div>
            <p class="border-top">Praėjusio mėnesio išleista suma: <span class="font-weight-bold" v-text="cart.last_month_value || 0"></span></p>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        cart: { required: true },
        paymentType: { required: true },
        balance: { required: true },
    },
    data() {
        return {
            edit: false,
            hover: false,
            edit_error: '',
            cart_old: {},
        }
    },

    methods: {
        getBalance() {
            if(this.cart.transfer_balance == true
                && parseFloat(this.cart.last_month_value) > parseFloat(this.cart.monthly_goal))
                return (parseFloat(this.balance) + parseFloat(this.cart.last_month_value) - parseFloat(this.cart.monthly_goal)).toFixed(2);
            return parseFloat(this.balance).toFixed(2) || 0;
        },
        exceeds() {
            console.log(this.getBalance());
            console.log(this.cart.transfer_balance);
            if(parseFloat(this.getBalance()) < parseFloat(this.cart.monthly_goal)) {
                console.log(false);
                return false;
            }
            console.log(true);
            return true;
        },
        transferBalanceStatus() {
            if(this.cart.transfer_balance == true)
                return "Įjungtas";
            else return "Išjungtas";
        },
        /* Checks if edit input has errors */
        validate() {
            if(this.cart.monthly_goal >= 0 && this.cart.monthly_goal < 99999999.99 && this.cart.monthly_goal) {
                this.edit_error='';
            }
            else
                this.edit_error="Įveskite sumą didesnę, arba lygią 0.";
        },
        /* Closes edit panel */
        cancelEdit() {
            this.cart = Object.assign({}, this.cart_old);
            this.edit_error='';
            this.edit=false;
        },
        /* Writes changes to database */
        saveEdit() {
            this.validate();
            if(!this.edit_error) {
                axios
                    .post(`/carts/update/${this.cart.id}`, this.cart)
                    .then(response => {
                        this.edit_error='';
                        this.edit=false;
                    });
            }
        },

        startEdit() {
            this.cart_old = Object.assign({}, this.cart);
            this.edit = true;
        }
    },
}
</script>

<style scoped>
    #progress{
        height: 14px;
    }

    #main-div {
        border: 1px solid;
        border-color: lightgray;
        border-radius: 7px;

    }

    #main-div:hover {
        border-color: teal;
    }
</style>
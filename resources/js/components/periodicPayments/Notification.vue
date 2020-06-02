<template>
  <span>
    <span v-if="loaded" class="text-white pb-0" style="font-size: 16px;"> | {{ balance }}</span>
    <span v-if="(loaded && showBadge) || (loaded && success_alert)">
      <b-badge id="popover-reactive-1" class="bg-p1-pink ml-2" style="font-size: 15px">{{ payments.length }}</b-badge>
      <b-popover target="popover-reactive-1"
        triggers="hover"
        placement="auto">
          <div class="text-center">
            <span class="font-weight-bold">Periodiniai mokėjimai:</span>
            <b-alert variant="success" :show="success_alert">Mokėjimas <strong>{{ success_text }}</strong> sėkmingai pridėtas</b-alert>
            <div class="container" v-for="payment in payments" :key="payment.id">
              {{ getPaymentType(payment.fk_payment_type_id) + ' ' + payment.value.toFixed(2) }}
              <br class="my-1">
              <b-button class="btn btn-sm bg-main-teal" v-on:click="addPayment(payment)">Pridėti</b-button>
              <b-button class="btn btn-sm bg-p1-dark" v-on:click="showPeriodic(payment.id)">Peržiūrėti</b-button>
              <hr>
            </div>
          </div>
      </b-popover>
    </span>
  </span>
</template>

<script>
import { bus } from '../../app.js'
export default {
  data() {
    return {
      showBadge: false,
      payments: [],
      payment_types: [],
      success_alert: false,
      success_text: '',
      balance: null,
      loaded: false,
    }
  },

  created() {
    this.fetchData();
    bus.$on('reloadNotifications', (data) => {
      this.fetchData();
    });
  },

  methods: {
    fetchData() {
      axios.get(`/apitodaypayments`)
        .then(response => {
          this.payments = response.data;
          this.fetchBalance();
          if(this.payments.length > 0) {
            this.getPaymentTypes();
            this.showBadge = true;
          }
          else
            this.showBadge = false;
          })
          .catch(error => {
              console.log(error);
          });
    },

    fetchBalance() {
      axios.get(`/apibalance`)
        .then(response => {
          this.balance = response.data;
          this.loaded = true;
        })
    },

    getPaymentTypes() {
      axios.get(`/apipaymenttypes`)
        .then(response => {
          this.payment_types = response.data;
          })
          .catch(error => {
              console.log(error);
          });
    },

    getPaymentType(id) {
    return this.payment_types[id-1] ? this.payment_types[id-1].name : '';
    },

    addPayment(payment) {
      console.log(payment);
      axios.post(`/periodic/storePayment`, payment)
        .then(response => {
          this.fetchData();
          bus.$emit('reloadTable', 'added/deleted payment');
          bus.$emit('reloadList', 'added/deleted payment');
          this.success_alert = true;
          this.success_text = this.payment_types[payment.fk_payment_type_id-1].name + ' ' + payment.value;
          })
          .catch(error => {
              console.log(error);
          });
    },

    showPeriodic(id) {
      window.location.replace(`/payments?showPeriodic&showId=${id}`);
    }
  }
}
</script>
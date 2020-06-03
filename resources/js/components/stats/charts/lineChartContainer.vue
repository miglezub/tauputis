<template>
  <div class="container">
      <div class="row">
        <label for="date_from" class="form-label h5 d-inline pt-1 col-sm-6 col-lg-3">Filtruoti nuo datos:</label>
        <input type="date" name="date_from" v-model="date_from" class="form-control d-inline col-sm-6 col-lg-7"
          :max="today">
        <label for="date_to" class="form-label h5 d-inline pt-3 col-sm-6 col-lg-3">Iki datos:</label>
        <input type="date" name="date_to" v-model="date_to" class="form-control d-inline col-sm-6 col-lg-7 mt-1"
          :max="today">
        <button class="btn bg-main-teal ml-2 mt-2 mt-md-1 h-75" v-on:click="filter()">Filtruoti</button>
      </div>
      <div v-if="loaded && labels.length > 1">
        <div class="row mt-5">
          <h3 class="col-11 text-center">Balansas</h3>
          <span data-toggle="popover" title="Balanso grafikas" data-placement="left"
            data-trigger="hover" class="float-right mt-1" style="cursor: help"
            data-content="Atvaizduojami visi mokėjimai filtruotam laikotarpiui, skaičiuojamas balanso likutis.
                Kiekviename taške pateikiama kokios kategorijos ir sumos mokėjimas atliktas, mokėjimo data.">
            <span class="material-icons text-main-teal">info</span>
          </span>
        </div>
        <line-chart
        :height="800"
        :width="1800"
        :chartData="values"
        :chartLabels="labels"
        v-if="width > 500"/>
        <div v-else class="text-center">
          Atsiprašome, ekranas per mažas, kad būtų rodomas balanso grafikas.
          <br>Padidinkite naršyklės langą, arba paverskite telefoną gulsčiai.
        </div>
        <div class="row mt-5">
          <div class="w-100 mx-auto">
            <h3 class="text-center">Išlaidų sumos pagal kategorijas (filtruotam laikotarpiui)</h3>
            <span data-toggle="popover" title="Išlaidų pagal kategorijas grafikas" data-placement="left"
                data-trigger="hover" class="float-right mt-1" style="cursor: help"
                data-content="Atvaizduojamos mokėjimų sumos pagal kategorijas filtruotam laikotarpiui.">
                <span class="material-icons text-main-teal">info</span>
            </span>
          </div>
          <pie-chart-container
          :payment_types="payment_types"
          :date_from="date_from"
          :date_to="date_to"
          class="w-100 mx-auto"
          />
          <!--div class="col-md-5 content-align-top">
            <h3>Periodiniai mokėjimai</h3>
            <ul>
              <li>Mokėjimas 1</li>
              <li>Mokėjimas 2</li>
            </ul>
          </div-->
        </div>
      </div>
      <div v-else-if="!loaded" class="text-center mt-5">
        <div class="spinner-grow text-main-teal" role="status" style="width: 50px; height: 50px;">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
      <div v-else>
        <p class="h3 text-center mt-5">Pasirinktu laikotarpiu mokėjimų nėra arba neužtenka suformuoti grafikams.</p>
        <a href="/payments" class="btn bg-main-teal d-block mx-auto w-50">Peržiūrėti/pridėti mokėjimus</a>
      </div>
  </div>
</template>

<script>
import LineChart from './lineChart.js'

export default {
  props: {
      payment_types: { type: Array, required: true },
  },
  
  name: 'lineChartContainer',

  components: { LineChart },
  data: () => ({
    loaded: false,
    date_from: new Date().getFullYear().toString() + "-01-01",
    date_to: new Date().toISOString().slice(0,10),
    today: new Date().toISOString().slice(0,10),
    yesterday: new Date().toISOString().slice(0,10),
    labels: [],
    values: [],
    width: 1000,
  }),

  created() {
      this.filter();
      window.addEventListener("resize", this.getScreenWidth);
      this.width = window.innerWidth;
      console.log(this.width);
  },

  destroyed() {
      window.removeEventListener("resize", this.getScreenWidth);
  },

  methods: {
      filter() {
          this.loaded = false;
          axios.get(`/apilinechartpayments?date_from=${this.date_from}&date_to=${this.date_to}`)
            .then(response => {
            this.values = response.data.map(item => item.balance)
            this.values.types = response.data.map(item => this.payment_types[item.fk_payment_type_id-1].name)
            this.values.values = response.data.map(item => item.value)
            this.labels = response.data.map(item => item.date)
            this.loaded = true
            })
            .catch(err => {
                console.log(response.data.error)
            });
      },

      getScreenWidth(event) {
          this.width = window.innerWidth;
          console.log(this.width);
      },
  }
}
</script>

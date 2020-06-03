<template>
  <div>
      <div class="container" v-if="loaded">
        <pie-chart
        :height="350"
        :width="400"
        :chartData="values"
        :chartLabels="labels"/>
      </div>
      <div v-else class="text-center">
          <img src="https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif" alt="Loading" class="w-25">
        </div>
  </div>
</template>

<script>
import PieChart from './pieChart.js'

export default {
  props: {
      payment_types: { type: Array, required: true },
      date_from: { required: true },
      date_to: { required: true },
  },
  
  name: 'pieChartContainer',

  components: { PieChart },
  data: () => ({
    loaded: false,
    labels: [],
    values: [],
  }),

  mounted() {
      this.filter();
      $('[data-toggle="popover"]').popover()
  },

  methods: {
      filter() {
          this.loaded = false;
          axios.get(`/apipiechartpayments?date_from=${this.date_from}&date_to=${this.date_to}`)
            .then(response => {
            this.values = response.data.map(item => item.value)
            //this.values.types = response.data.map(item => this.payment_types[item.fk_payment_type_id-1].name)
            //this.values.values = response.data.map(item => item.value)
            this.labels = response.data.map(item => item.label)
            this.loaded = true
            })
            .catch(err => {
                console.log(response.data.error)
            });
      }
  }
}
</script>

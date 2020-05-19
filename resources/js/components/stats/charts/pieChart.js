import { Pie } from 'vue-chartjs'

export default {
    extends: Pie,
    props: {
        chartData: {
        type: Array,
        required: false
        },
        chartLabels: {
        type: Array,
        required: true
        },
    },

    data() {
      return {
        options: {
          responsive: true,
          maintainAspectRatio: false,
        }
      }
    },

    mounted () {
        this.renderChart({
        labels: this.chartLabels,
        datasets: [
          {
            label: 'Mokėjimai',
            data: this.chartData,
            backgroundColor: [
              "#B35D64", "#FFB8BE", "#FF9EA7", '#6BB87D', '#9EFFB5', 
              '#B3465B', '#FFF98F', '#FF9EB1', '#9EE4FF', '#3E91B3', 
              "#FFB8BE", "#FF9EA7"
            ],
          }
        ]
      }, this.options)
    }
}
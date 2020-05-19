import { Line } from 'vue-chartjs'

export default {
    extends: Line,
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
                showScale: true,
                legend: {
                    display: false,
                },
                scales: {
                    yAxes: [{
                    ticks: {
                        beginAtZero: false,
                    },
                    gridLines: {
                        display: true,
                        color: '#EEF0F4',
                        drawTicks: false,
                    }
                    }],
                    xAxes: [ {
                    gridLines: {
                        display: true,
                        color: '#EEF0F4',
                        drawTicks: false,
                    }
                    }]
                },
                tooltips: {
                    enabled: true,
                    
                    callbacks: {
                        title: function(tooltipItem, data) {
                          return data['labels'][tooltipItem[0]['index']];
                        },
                        label: function(tooltipItem, data) {
                          return data['datasets'][0]['data'].types[tooltipItem['index']] + ': '
                           + data['datasets'][0]['data'].values[tooltipItem['index']];
                        },
                        afterLabel: function(tooltipItem, data) {
                            return 'Balansas: '
                             + data['datasets'][0]['data'][tooltipItem['index']];
                          }
                    },
                }
            }
        }
    },

    mounted () {
        this.renderChart({
        labels: this.chartLabels,
        datasets: [
          {
            label: 'Mokėjimai',
            //backgroundColor: "rgba(207, 253, 226, 0.3)",
            //borderColor: "#85CCA7",
            backgroundColor: "rgba(252, 23, 92, 0.04)",
            borderColor: "#FC175A",
            data: this.chartData,
            fill: true,
          }
        ]
      }, this.options)
    }
}
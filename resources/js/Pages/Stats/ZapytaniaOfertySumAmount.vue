<template>
  <h1 class="text-2xl font-bold p-1 mt-3">Wartość Zapytań/Ofert w mln PLN</h1>
  <div class="w-3/4 h-100">
    <Line :data="data" :options="options" />
  </div>
</template>

<script>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
// import * as chartConfig from './chartConfig.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

export default {
  name: 'ZapytaniaOfertSumAmount',
  components: {
    Line
  },
  props: {
    zapytaniaOfertySumAmount: Array,
  },
  data() {
    return {
      data: {
        labels: this.zapytaniaOfertySumAmount[0],
        datasets: [
          {
            label: 'Zapytania kwota PLN',
            fill: false,
            borderColor: "#7f95cd",
            backgroundColor: "#7f95cd",
            data: this.zapytaniaOfertySumAmount[1],
            tension: 0.4,
          },
          {
            label: 'Oferty kwota PLN',
            fill: false,
            borderColor: "#e755ba",
            backgroundColor: "#e755ba",
            data: this.zapytaniaOfertySumAmount[2],
          },
          {
            label: 'Ofert wygrane kwota PLN',
            fill: false,
            borderColor: "#3cba9f",
            backgroundColor: "#3cba9f",
            data: this.zapytaniaOfertySumAmount[3],
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
      }
    }
  }
}
</script>

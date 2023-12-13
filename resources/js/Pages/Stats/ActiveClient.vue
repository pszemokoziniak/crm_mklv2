<template>
  <h1 class="text-2xl font-bold p-1 mt-3">Aktywni klienci</h1>
  <Pie :data="data" :options="options" />
</template>

<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

export default {
  name: 'App',
  components: {
    Pie
  },
  props: {
    clientActive: Array,
  },
  data() {
    return {
      data: {
        labels: ['Aktywni', 'Nie aktywni'],
        datasets: [
          {
            backgroundColor: ['#41B883', '#E46651', '#00D8FF', '#DD1B16'],
            data: this.clientActive,
          }
        ]
      },
      options: {
        responsive: false,
        maintainAspectRatio: true,
      }
    }
  }
}
</script>


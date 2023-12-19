<template>
  <h1 class="text-2xl font-bold p-1 mt-3">Dodali zapytania</h1>
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
    zapytaniaUsers: Array,
  },
  data() {
    return {
      data: {
        labels: this.zapytaniaUsers[0],
        datasets: [
          {
            backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#7f95cd", "#2e5ea4","#c738b9","#ccdb6b","#7a3e0a","#3d7835","#cdcce3","#941b4b","#aebcbd","#cda9e8"],
            data: this.zapytaniaUsers[1],
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


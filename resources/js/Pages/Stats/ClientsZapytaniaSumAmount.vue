<template>
  <h1 class="text-2xl font-bold p-1 mt-3">Najlepsi klienci / Zapytania</h1>
  <div class="w-3/4 h-100">
    <Bar :data="data" :options="options" />
  </div>
</template>

<script>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

export default {
  name: 'App',
  components: {
    Bar
  },
  props: {
    clientZapytaniaSumAmount: Array,
  },
  data() {
    return {
      data: {
        labels: this.clientZapytaniaSumAmount[0],
        datasets: [
          {
            backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#7f95cd", "#2e5ea4","#c738b9","#ccdb6b","#7a3e0a","#3d7835","#cdcce3","#941b4b","#aebcbd","#cda9e8"],
            data: this.clientZapytaniaSumAmount[1],
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true
      }
    }
  }
}
</script>

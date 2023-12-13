<template>
  <h1 class="text-2xl font-bold p-1 mt-3">Przyrost klientów</h1>
  <Line :data="data" :options="options" />
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
  name: 'IncreaseClient',
  components: {
    Line
  },
  props: {
    increaseClients: Array,
  },
  data() {
    return {
      data: {
        labels: this.increaseClients[0],
        datasets: [
          {
            label: 'Ilość klientów',
            backgroundColor: '#f87979',
            data: this.increaseClients[1]
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


<template>
  <div>
    <h1 class="text-xl font-bold text-center text-green-700 py-3 mt-8 border-b border-green-100">Top 15 klientów / Oferty wygrane (PLN)</h1>
    <div class="w-full" :style="{ height: chartHeight + 'px' }">
      <Bar :data="data" :options="options" />
    </div>
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
  LinearScale,
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

export default {
  name: 'ClientsOfertaWygraneSumAmount',
  components: {
    Bar,
  },
  props: {
    clientOfertaWygraneSumAmount: Array,
  },
  data() {
    return {
      data: {
        labels: this.clientOfertaWygraneSumAmount[0],
        datasets: [
          {
            label: 'Wartość ofert wygranych (PLN)',
            backgroundColor: '#16a34a',
            borderRadius: 4,
            data: this.clientOfertaWygraneSumAmount[1],
          },
        ],
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN', maximumFractionDigits: 0 }).format(ctx.raw),
            },
          },
        },
        scales: {
          x: {
            ticks: {
              callback: (v) => new Intl.NumberFormat('pl-PL', { notation: 'compact', compactDisplay: 'short' }).format(v),
            },
            grid: { color: '#f3f4f6' },
          },
          y: {
            ticks: { font: { size: 11 } },
            grid: { display: false },
          },
        },
      },
    }
  },
  computed: {
    chartHeight() {
      const count = this.clientOfertaWygraneSumAmount[0] ? this.clientOfertaWygraneSumAmount[0].length : 0
      return Math.max(300, count * 40)
    },
  },
}
</script>

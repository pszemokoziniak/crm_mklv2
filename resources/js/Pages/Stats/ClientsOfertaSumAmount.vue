<template>
  <div>
    <h1 class="text-xl font-bold text-center text-indigo-700 py-3 mt-8 border-b border-indigo-100">Top 15 klientów / Oferty (PLN)</h1>
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
  name: 'ClientsOfertaSumAmount',
  components: {
    Bar,
  },
  props: {
    clientOfertaSumAmount: Array,
  },
  data() {
    return {
      data: {
        labels: this.clientOfertaSumAmount[0],
        datasets: [
          {
            label: 'Wartość ofert (PLN)',
            backgroundColor: '#4f46e5',
            borderRadius: 4,
            data: this.clientOfertaSumAmount[1],
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
      const count = this.clientOfertaSumAmount[0] ? this.clientOfertaSumAmount[0].length : 0
      return Math.max(300, count * 40)
    },
  },
}
</script>

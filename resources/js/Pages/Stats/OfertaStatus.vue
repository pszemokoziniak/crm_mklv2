<template>
  <div>
    <h1 class="text-xl font-bold text-center text-indigo-700 py-3 mt-8 border-b border-indigo-100">Oferty status</h1>
    <div class="flex flex-col md:flex-row md:items-center gap-6 p-2">
      <div class="w-full md:w-1/2 lg:w-2/5" style="height: 400px;">
        <Pie :data="data" :options="options" />
      </div>
      <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-1.5 text-xs flex-1">
        <div v-for="(label, i) in chartLabels" :key="i" class="flex items-center gap-1.5 min-w-0">
          <span class="w-3 h-3 rounded-full inline-block flex-shrink-0" :style="{ backgroundColor: colors[i % colors.length] }" />
          <span class="truncate" :title="label">{{ label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

const COLORS = ['#3e95cd', '#8e5ea2','#3cba9f','#e8c3b9','#7f95cd', '#2e5ea4','#c738b9','#ccdb6b','#7a3e0a','#3d7835','#cdcce3','#941b4b','#aebcbd','#cda9e8']

export default {
  name: 'OfertaStatus',
  components: { Pie },
  props: {
    ofertaStatus: Array,
  },
  data() {
    return {
      colors: COLORS,
      chartLabels: this.ofertaStatus[0] || [],
      data: {
        labels: this.ofertaStatus[0],
        datasets: [{
          backgroundColor: COLORS,
          data: this.ofertaStatus[1],
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
      },
    }
  },
}
</script>

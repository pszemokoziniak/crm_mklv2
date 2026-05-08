<template>
  <div>
    <h1 class="text-2xl font-bold p-1 mt-3">Dodali zapytania</h1>
    <div class="max-w-sm mx-auto">
      <div style="height: 280px;">
        <Pie :data="data" :options="options" />
      </div>
      <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 mt-2 text-xs">
        <div v-for="(label, i) in chartLabels" :key="i" class="flex items-center gap-1">
          <span class="w-3 h-3 rounded-full inline-block" :style="{ backgroundColor: colors[i % colors.length] }" />
          {{ label }}
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
  name: 'ZapytaniaUsers',
  components: { Pie },
  props: {
    zapytaniaUsers: Array,
  },
  data() {
    return {
      colors: COLORS,
      chartLabels: this.zapytaniaUsers[0] || [],
      data: {
        labels: this.zapytaniaUsers[0],
        datasets: [{
          backgroundColor: COLORS,
          data: this.zapytaniaUsers[1],
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

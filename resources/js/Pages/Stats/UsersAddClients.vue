<template>
  <div>
    <h1 class="text-xl font-bold text-center text-indigo-700 py-3 mt-8 border-b border-indigo-100">Pracownicy którzy dodali klientów</h1>
    <div class="max-w-2xl mt-2 space-y-2 px-1">
      <div v-for="item in clientNumberByUser" :key="item.id" class="flex items-center gap-3">
        <div class="w-40 text-sm font-medium text-gray-700 truncate text-right">
          {{ displayName(item) }}
        </div>
        <div class="flex-1 flex items-center bg-gray-100 rounded-full h-5 overflow-hidden">
          <div
            class="h-full bg-indigo-500 flex items-center justify-end transition-all"
            :style="{ width: percent(item.count) + '%' }"
            :class="{ 'pr-2': isInside(item.count) }"
          >
            <span v-if="isInside(item.count)" class="text-[10px] font-bold text-white whitespace-nowrap">{{ item.count }}</span>
          </div>
          <span v-if="!isInside(item.count)" class="ml-2 text-[10px] font-bold text-gray-700 whitespace-nowrap">{{ item.count }}</span>
        </div>
        <div class="w-14 text-xs text-gray-500 text-right">{{ percent(item.count) }}%</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UsersAddClients',
  props: {
    clientNumber: Number,
    clientNumberByUser: Object,
  },
  methods: {
    displayName(item) {
      const first = (item.first_name || '').trim()
      const last = (item.last_name || '').trim()
      if (first && last) return first + ' ' + last
      return first || last || '—'
    },
    percent(count) {
      if (!this.clientNumber) return 0
      return (count / this.clientNumber * 100).toFixed(1)
    },
    isInside(count) {
      // Cyfry renderowane wewnatrz paska gdy jest dosc szeroki, w przeciwnym razie na zewnatrz (po prawej)
      return parseFloat(this.percent(count)) >= 10
    },
  },
}
</script>

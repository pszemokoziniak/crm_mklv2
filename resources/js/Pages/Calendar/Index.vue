<template>
  <div>
    <Head title="Kalendarz" />
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Kalendarz Projektów</h1>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex flex-wrap items-end -mx-3">
        <div class="w-full lg:w-1/3 px-3 mb-4 lg:mb-0">
          <search-filter-simple v-model="form.search" class="w-full" placeholder="Szukaj projektu..." @reset="reset" />
        </div>
        <div class="w-full lg:w-1/4 px-3">
          <text-input v-model="form.start" type="date" label="Data od" />
        </div>
        <div class="w-full lg:w-1/4 px-3">
          <text-input v-model="form.end" type="date" label="Data do" />
        </div>
      </div>
    </div>

    <!-- Legenda kolorów pasków (status oferty przypisanej do zapytania) -->
    <div class="bg-white rounded-lg shadow-sm px-6 py-3 mb-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-700">
      <span class="font-semibold text-gray-800">Legenda:</span>
      <span v-for="entry in legend" :key="entry.key" class="flex items-center">
        <span class="inline-block w-4 h-4 rounded mr-2 flex-shrink-0" :style="{ backgroundColor: palette[entry.key].bg }" />
        {{ entry.label }}
      </span>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <!-- Kontener z przewijaniem pionowym i poziomym -->
      <div class="overflow-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100" style="max-height: 70vh;">
        <table class="border-collapse table-fixed" :style="{ width: tableWidth + 'px' }">
          <thead class="sticky top-0 z-30">
            <!-- Wiersz Miesięcy -->
            <tr class="bg-gray-100 border-b border-gray-200">
              <th
                v-for="(count, month) in months"
                :key="month"
                :colspan="count"
                class="border-r border-gray-200 py-2 text-center text-xs font-bold text-gray-700 uppercase whitespace-nowrap"
                :class="count > 1 ? 'px-2 tracking-wider' : 'px-1'"
                :title="month"
              >
                {{ monthLabel(month, count) }}
              </th>
            </tr>
            <!-- Wiersz Tygodni (kolumna = 1 tydzień, etykieta = poniedziałek) -->
            <tr class="bg-white border-b border-gray-300 shadow-sm">
              <th
                v-for="(week, index) in weeks"
                :key="index"
                class="border-r border-gray-100 p-1 text-center text-[11px] font-bold text-gray-600"
                style="width: 56px; min-width: 56px;"
                :title="`Tydzień ${week.week} (od ${week.label})`"
              >
                {{ week.label }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in zapytanias" :key="item.id" class="hover:bg-blue-50/30 transition-colors">
              <td
                v-for="(col, index) in item.colSpan"
                :key="index"
                :colspan="col[0]"
                class="p-0 h-12 relative border-r border-gray-50"
                :class="{'bg-transparent': col[1] === 0}"
              >
                <Link
                  v-if="col[1] === 1"
                  :href="`/zapytania/${item.id}/edit`"
                  class="absolute inset-y-2 left-1 right-1 flex items-center px-3 rounded-md text-xs font-semibold shadow-sm hover:brightness-90 hover:shadow-md transition-all overflow-hidden whitespace-nowrap z-10"
                  :style="barStyle(item.status_color)"
                  :title="`${item.id_zapyt} - ${item.nazwa_projektu}${item.client ? ' (' + item.client.nazwa + ')' : ''} — ${legendLabel(item.status_color)}`"
                >
                  <span class="truncate">
                    <span class="bg-black/10 px-1.5 py-0.5 rounded mr-2 text-[10px]">{{ item.id_zapyt }}</span>
                    {{ item.nazwa_projektu }}<template v-if="item.client"> | {{ item.client.nazwa }}</template>
                  </span>
                </Link>
              </td>
            </tr>
            <tr v-if="Object.keys(months).length === 0">
              <td class="px-6 py-10 text-center text-gray-500 italic" :colspan="weeks.length">
                Brak zapytań w wybranym terminie
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4 text-sm text-gray-500 flex items-center justify-between px-2">
      <div class="flex items-center space-x-4">
        <div class="flex items-center">
          <div class="w-3 h-3 rounded-full bg-indigo-500 mr-2" />
          <span>Zakres: <strong>{{ start }}</strong> do <strong>{{ end }}</strong></span>
        </div>
        <div class="text-gray-400">|</div>
        <div class="text-gray-400 italic">
          Przewijaj w poziomie i pionie, aby zobaczyć wszystkie dane
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import TextInput from '@/Shared/TextInput.vue'
import SearchFilterSimple from '@/Shared/SearchFilterSimple.vue'

export default {
  components: {
    SearchFilterSimple,
    TextInput,
    Head,
    Link,
  },
  layout: Layout,
  props: {
    months: Object,
    weeks: Array,
    zapytanias: Array,
    start: String,
    end: String,
    filters: Object,
  },
  data() {
    return {
      weekWidth: 56,
      // Kolor paska wg statusu oferty zapytania. 'brak' = brak oferty / inny status.
      palette: {
        wygrana: { bg: '#16a34a', text: '#ffffff' },
        zrewidowana: { bg: '#facc15', text: '#422006' },
        toczy: { bg: '#f97316', text: '#ffffff' },
        zawieszona: { bg: '#2563eb', text: '#ffffff' },
        brak: { bg: '#6b7280', text: '#ffffff' },
      },
      legend: [
        { key: 'wygrana', label: 'Oferta WYGRANA' },
        { key: 'zrewidowana', label: 'Oferta ZREWIDOWANA' },
        { key: 'toczy', label: 'Oferta TOCZY SIĘ' },
        { key: 'zawieszona', label: 'Oferta ZAWIESZONA PRZEZ INWESTORA' },
        { key: 'brak', label: 'Brak oferty / inny status' },
      ],
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        start: this.filters.start || this.start,
        end: this.filters.end || this.end,
      },
    }
  },
  computed: {
    tableWidth() {
      return this.weeks.length * this.weekWidth
    },
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/calendar', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    /** Miesiąc z jedną kolumną tygodniową nie mieści "09-2026" — skracamy do "09.26". */
    monthLabel(month, count) {
      if (count > 1) {
        return month
      }
      const [mm, yyyy] = month.split('-')
      return `${mm}.${yyyy.slice(-2)}`
    },
    barStyle(key) {
      const color = this.palette[key] || this.palette.brak
      return { backgroundColor: color.bg, color: color.text }
    },
    legendLabel(key) {
      return (this.legend.find((entry) => entry.key === key) || this.legend[this.legend.length - 1]).label
    },
    reset() {
      this.form.search = null
      this.form.start = this.start
      this.form.end = this.end
    },
  },
}
</script>

<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  width: 8px;
  height: 10px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: #f8fafc;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 5px;
  border: 2px solid #f8fafc;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Zapewnienie, że sticky header działa poprawnie */
thead th {
  position: sticky;
  top: 0;
  z-index: 20;
}

/* Drugi wiersz nagłówka musi być przesunięty o wysokość pierwszego */
thead tr:nth-child(2) th {
  top: 33px; /* Przybliżona wysokość pierwszego wiersza */
  z-index: 19;
}
</style>

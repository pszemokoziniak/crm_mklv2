<template>
  <div>
    <Head title="Zapytania" />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Zapytania ofertowe</h1>
      <Link class="btn-indigo flex items-center justify-center px-5 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 text-sm" href="/zapytania/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Nowe zapytanie</span>
      </Link>
    </div>

    <!-- Statystyki -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Wszystkie</p>
        <p class="text-xl font-bold text-gray-900">{{ stats.total }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Ten miesiąc</p>
        <div class="flex items-baseline gap-2">
          <p class="text-xl font-bold text-gray-900">{{ stats.this_month }}</p>
          <span v-if="stats.prev_month > 0" class="text-[10px] font-semibold" :class="monthChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ monthChange >= 0 ? '↑' : '↓' }} {{ Math.abs(monthChange) }}%
          </span>
        </div>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Z ofertą</p>
        <p class="text-xl font-bold text-green-600">{{ stats.with_oferta }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Bez oferty</p>
        <p class="text-xl font-bold text-amber-600">{{ stats.without_oferta }}</p>
      </div>
    </div>

    <!-- Filtry -->
    <div class="flex items-center justify-between mb-4">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-sm font-medium text-gray-700 mb-1">Status archiwum:</label>
        <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option :value="null">Wszystko (bez usuniętych)</option>
          <option value="with">Wszystko (z usuniętymi)</option>
          <option value="only">Tylko usunięte</option>
        </select>
      </search-filter>
    </div>

    <!-- Mobile: widok kart -->
    <div class="md:hidden space-y-3">
      <Link
        v-for="item in zapytanias.data"
        :key="item.id"
        :href="`/zapytania/${item.id}/edit`"
        class="block bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:border-indigo-200 hover:shadow transition-all active:scale-[0.99]"
      >
        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
              <span class="font-bold text-sm text-gray-900 line-clamp-2">{{ item.nazwa_projektu }}</span>
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-rose-400" />
            </div>
            <span class="text-[10px] text-gray-400 font-medium">{{ item.id_zapyt }}</span>
          </div>
          <icon name="cheveron-right" class="flex-shrink-0 w-5 h-5 text-gray-300" />
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
          <span v-if="item.client" class="truncate max-w-[50%]">
            <span class="text-gray-400">Klient:</span> {{ item.client.nazwa }}
          </span>
          <span v-if="item.zakres">
            <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-semibold rounded uppercase">{{ item.zakres.name }}</span>
          </span>
          <span v-if="item.kraj">{{ countryFlag(item.kraj.name) }} {{ item.kraj.name }}</span>
        </div>
        <div v-if="item.otrzymal" class="mt-2 flex items-center justify-between text-[11px] text-gray-400">
          <span>{{ item.otrzymal.first_name }} {{ item.otrzymal.last_name }}</span>
          <span>{{ item.created_at }}</span>
        </div>
      </Link>
      <div v-if="zapytanias.data.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center text-gray-400">
        <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20 mx-auto" />
        <p class="text-xs">Brak zapytań spełniających kryteria.</p>
      </div>
    </div>

    <!-- Desktop: widok tabeli -->
    <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
            <th class="px-3 py-1.5">
              <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Projekt</span>
            </th>
            <th class="px-3 py-1.5">
              <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Klient</span>
            </th>
            <th class="px-3 py-1.5 hidden lg:table-cell">
              <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Kraj</span>
            </th>
            <th class="px-3 py-1.5">
              <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Zakres</span>
            </th>
            <th class="px-3 py-1.5 hidden lg:table-cell">
              <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Zarejestrował</span>
            </th>
            <th class="py-1.5" />
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="item in zapytanias.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
            <td class="px-3 py-2.5">
              <Link class="block focus:text-indigo-500" :href="`/zapytania/${item.id}/edit`">
                <div class="flex items-center">
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-xs" :title="item.nazwa_projektu">{{ item.nazwa_projektu }}</span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </div>
                <span class="text-[10px] text-gray-400 font-medium mt-0.5 block">{{ item.id_zapyt }}</span>
              </Link>
            </td>
            <td class="px-3 py-2.5">
              <Link class="block text-gray-600" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                <span v-if="item.client" class="text-xs font-medium">{{ item.client.nazwa }}</span>
              </Link>
            </td>
            <td class="px-3 py-2.5 hidden lg:table-cell">
              <Link class="block text-gray-500" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                <span v-if="item.kraj" class="text-xs whitespace-nowrap">{{ countryFlag(item.kraj.name) }} {{ item.kraj.name }}</span>
              </Link>
            </td>
            <td class="px-3 py-2.5">
              <Link class="block" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                <span v-if="item.zakres" class="inline-block px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[8px] font-bold rounded uppercase tracking-tight leading-tight">
                  {{ item.zakres.name }}
                </span>
              </Link>
            </td>
            <td class="px-3 py-2.5 hidden lg:table-cell whitespace-nowrap">
              <Link class="block" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                <template v-if="item.otrzymal">
                  <div class="text-xs font-medium text-gray-700">{{ item.otrzymal.first_name }} {{ item.otrzymal.last_name }}</div>
                  <div class="text-[10px] text-gray-400 mt-0.5">{{ item.created_at }}</div>
                </template>
              </Link>
            </td>
            <td class="px-1 py-2.5 text-center">
              <Link class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="w-3 h-3" />
              </Link>
            </td>
          </tr>
          <tr v-if="zapytanias.data.length === 0">
            <td class="px-4 py-12 text-center text-gray-400" colspan="6">
              <div class="flex flex-col items-center">
                <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20" />
                <p class="text-xs">Brak zapytań spełniających kryteria.</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      <pagination :links="zapytanias.links" />
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    zapytanias: Object,
    stats: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  computed: {
    monthChange() {
      if (!this.stats.prev_month || this.stats.prev_month === 0) return 0
      return Math.round(((this.stats.this_month - this.stats.prev_month) / this.stats.prev_month) * 100)
    },
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/zapytania', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    countryFlag(name) {
      const map = {
        'Polska': 'PL', 'Francja': 'FR', 'Wielka Brytania': 'GB', 'Szwecja': 'SE',
        'Finlandia': 'FI', 'Dania': 'DK', 'Norwegia': 'NO', 'Austria': 'AT',
        'Niemcy': 'DE', 'Czechy': 'CZ', 'Słowacja': 'SK', 'Litwa': 'LT',
        'Łotwa': 'LV', 'Estonia': 'EE', 'Szwajcaria': 'CH', 'Belgia': 'BE',
        'Hiszpania': 'ES', 'Bosnia i Hercegowina': 'BA', 'Chorwacja': 'HR',
        'USA': 'US', 'Portugalia': 'PT', 'Holandia': 'NL', 'Włochy': 'IT',
        'Grecja': 'GR', 'Rosja': 'RU', 'Rumunia': 'RO', 'Urugwaj': 'UY',
        'Brazylia': 'BR', 'Irlandia': 'IE', 'Malta': 'MT', 'Turcja': 'TR',
        'Luxemburg': 'LU', 'Bulgaria': 'BG', 'Wegry': 'HU',
      }
      const code = map[name]
      if (!code) return ''
      return String.fromCodePoint(...[...code].map(c => 0x1F1E6 + c.charCodeAt(0) - 65))
    },
  },
}
</script>

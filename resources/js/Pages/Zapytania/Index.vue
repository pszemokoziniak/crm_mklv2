<template>
  <div>
    <Head title="Zapytania" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Zapytania ofertowe</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/zapytania/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Nowe zapytanie</span>
      </Link>
    </div>

    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-sm font-medium text-gray-700 mb-1">Status archiwum:</label>
        <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option :value="null">Wszystko (bez usuniętych)</option>
          <option value="with">Wszystko (z usuniętymi)</option>
          <option value="only">Tylko usunięte</option>
        </select>
      </search-filter>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full table-fixed md:table-auto">
          <thead>
            <tr
              class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100"
            >
              <th class="px-4 py-1.5 w-1/3 md:w-auto">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Projekt</div>
              </th>
              <th class="px-4 py-1.5 w-1/4 md:w-auto">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Klient</div>
              </th>
              <th class="px-4 py-1.5 hidden lg:table-cell">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Kraj</div>
              </th>
              <th class="px-4 py-1.5 hidden sm:table-cell">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Zakres</div>
              </th>
              <th class="px-4 py-1.5 hidden md:table-cell">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Zarejestrował</div>
              </th>
              <th class="px-4 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in zapytanias.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-4 py-3">
                <Link class="flex flex-col focus:text-indigo-500" :href="`/zapytania/${item.id}/edit`">
                  <div class="flex items-center">
                    <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-xs break-words line-clamp-2">{{ item.nazwa_projektu }}</span>
                    <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                  </div>
                  <span class="text-[10px] text-gray-400 font-medium mt-0.5">{{ item.id_zapyt }}</span>
                </Link>
              </td>
              <td class="px-4 py-3">
                <Link class="flex items-center text-gray-600" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.client" class="text-xs font-medium truncate">
                    {{ item.client.nazwa }}
                  </div>
                </Link>
              </td>
              <td class="px-4 py-3 hidden lg:table-cell">
                <Link class="flex items-center text-gray-500" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.kraj" class="flex items-center text-xs">
                    {{ item.kraj.name }}
                  </div>
                </Link>
              </td>
              <td class="px-4 py-3 hidden sm:table-cell">
                <Link class="flex items-center" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.zakres" class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded uppercase tracking-tighter leading-tight whitespace-nowrap scale-[0.7] origin-left inline-block">
                    {{ item.zakres.name }}
                  </span>
                </Link>
              </td>
              <td class="px-4 py-3 hidden md:table-cell">
                <Link class="flex flex-col" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                  <template v-if="item.otrzymal">
                    <span class="text-xs font-medium text-gray-700">{{ item.otrzymal.first_name }} {{ item.otrzymal.last_name }}</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">{{ item.created_at }}</span>
                  </template>
                </Link>
              </td>
              <td class="px-4 py-3 text-right">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
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
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
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
    formatNumber (num) {
      if (!num) return '0,00'
      return new Intl.NumberFormat('pl-PL',{
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num)
    },
  },
}
</script>

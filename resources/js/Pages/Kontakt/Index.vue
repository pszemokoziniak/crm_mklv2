<template>
  <div>
    <Head title="Wszystkie Kontakty" />
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Wszystkie Kontakty</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/kontakt/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj kontakt</span>
      </Link>
    </div>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset" />
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead>
            <tr class="text-left font-bold text-gray-400 text-[10px] uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-3 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('client')">
                <div class="flex items-center">
                  Klient
                  <icon v-if="form.field === 'client'" :name="form.direction === 'asc' ? 'cheveron-up' : 'cheveron-down'" class="w-3 h-3 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('subject')">
                <div class="flex items-center">
                  Temat
                  <icon v-if="form.field === 'subject'" :name="form.direction === 'asc' ? 'cheveron-up' : 'cheveron-down'" class="w-3 h-3 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3 whitespace-nowrap cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('call_date')">
                <div class="flex items-center">
                  Data
                  <icon v-if="form.field === 'call_date'" :name="form.direction === 'asc' ? 'cheveron-up' : 'cheveron-down'" class="w-3 h-3 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('user')">
                <div class="flex items-center">
                  Dodał
                  <icon v-if="form.field === 'user'" :name="form.direction === 'asc' ? 'cheveron-up' : 'cheveron-down'" class="w-3 h-3 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('opiekun')">
                <div class="flex items-center">
                  Opiekun
                  <icon v-if="form.field === 'opiekun'" :name="form.direction === 'asc' ? 'cheveron-up' : 'cheveron-down'" class="w-3 h-3 ml-1" />
                </div>
              </th>
              <th class="px-6 py-3">Zapytanie / Oferta</th>
              <th class="px-6 py-3" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in kontakty.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-3">
                <Link class="flex items-center font-medium text-gray-900 text-xs" :href="`/kontakt/${item.id}/edit`">
                  {{ item.client }}
                </Link>
              </td>
              <td class="px-6 py-3">
                <Link class="flex items-center text-indigo-600 font-semibold text-xs" :href="`/kontakt/${item.id}/edit`">
                  {{ item.subject }}
                  <span v-if="item.replies_count > 0" class="ml-2 text-[10px] text-gray-500">({{ item.replies_count }})</span>
                </Link>
              </td>
              <td class="px-6 py-3 whitespace-nowrap">
                <Link class="flex items-center text-gray-600 text-xs" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  {{ item.call_date }}
                </Link>
              </td>
              <td class="px-6 py-3">
                <Link class="flex items-center text-gray-600 text-xs" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  {{ item.user }}
                </Link>
              </td>
              <td class="px-6 py-3">
                <Link class="flex items-center text-gray-600 text-xs" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  {{ item.opiekun }}
                </Link>
              </td>
              <td class="px-6 py-3">
                <Link class="flex flex-col text-gray-500 text-[10px]" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  <span class="whitespace-nowrap">Z: {{ truncateText(item.zapytanie, 15) }}</span>
                  <span class="whitespace-nowrap">O: {{ truncateText(item.oferta, 15) }}</span>
                </Link>
              </td>
              <td class="px-6 py-3 text-right">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="kontakty.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="7">
                <div class="flex flex-col items-center">
                  <icon name="contact" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Nie znaleziono żadnych kontaktów.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-6">
      <pagination :links="kontakty.links" />
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import throttle from 'lodash/throttle'
import pickBy from 'lodash/pickBy'
import mapValues from 'lodash/mapValues'

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
    kontakty: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        field: this.filters.field,
        direction: this.filters.direction,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/kontakt', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    sort(field) {
      this.form.field = field
      this.form.direction = this.form.direction === 'asc' ? 'desc' : 'asc'
    },
    truncateText(text, length) {
      if (!text || text === '-') return '-'
      if (text.length <= length) {
        return text
      }
      return text.substring(0, length) + '...'
    },
  },
}
</script>

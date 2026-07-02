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
      <div>
        <table class="w-full table-fixed">
          <colgroup>
            <col style="width:22%" /><!-- Klient -->
            <col style="width:26%" /><!-- Temat -->
            <col style="width:12%" /><!-- Data -->
            <col style="width:17%" /><!-- Opiekun -->
            <col style="width:20%" /><!-- Zapytanie/Oferta -->
            <col style="width:3%" /><!-- Arrow -->
          </colgroup>
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('client')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'client' }">
                  <span>Klient</span>
                  <span v-if="form.field === 'client'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('subject')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'subject' }">
                  <span>Temat</span>
                  <span v-if="form.field === 'subject'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('call_date')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'call_date' }">
                  <span>Data</span>
                  <span v-if="form.field === 'call_date'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('opiekun')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'opiekun' }">
                  <span>Opiekun</span>
                  <span v-if="form.field === 'opiekun'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5">
                <span class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Zapytanie / Oferta</span>
              </th>
              <th class="px-3 py-1.5" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in kontakty.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block font-medium text-gray-900 text-xs truncate" :href="`/kontakt/${item.id}/edit`" :title="item.client">
                  {{ item.client }}
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex flex-col text-indigo-600 font-semibold text-xs" :href="`/kontakt/${item.id}/edit`">
                  <div class="flex items-center truncate" :title="item.subject">
                    <span class="truncate">{{ item.subject }}</span>
                    <span v-if="item.replies_count > 0" class="ml-2 flex-shrink-0 text-[10px] text-gray-500">({{ item.replies_count }})</span>
                  </div>
                  <span v-if="item.contact_type" class="text-[9px] text-indigo-400 uppercase mt-0.5 truncate">{{ item.contact_type }}</span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 text-xs whitespace-nowrap" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  {{ $filters.formatDate(item.call_date) }}
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 text-xs truncate" :href="`/kontakt/${item.id}/edit`" tabindex="-1" :title="item.opiekun">
                  {{ item.opiekun }}
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex flex-col text-gray-500 text-[9px]" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  <span class="truncate" :title="item.zapytanie">Z: {{ item.zapytanie || '-' }}</span>
                  <span class="truncate" :title="item.oferta">O: {{ item.oferta || '-' }}</span>
                </Link>
              </td>
              <td class="px-1 py-3 text-center">
                <Link class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-3 h-3" />
                </Link>
              </td>
            </tr>
            <tr v-if="kontakty.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="6">
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
    toggleSort(field) {
      if (this.form.field !== field) {
        this.form.field = field
        this.form.direction = 'desc'
      } else if (this.form.direction === 'desc') {
        this.form.direction = 'asc'
      } else {
        this.form.field = null
        this.form.direction = null
      }
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

<template>
  <div>
    <Head title="Oferty" />
    <h1 class="mb-8 text-3xl font-bold text-gray-900">Oferty</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option :value="null">Wszystko</option>
          <option value="with">Z usuniętymi</option>
          <option value="only">Tylko usunięte</option>
        </select>
      </search-filter>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95" href="/oferta/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj ofertę</span>
      </Link>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full table-fixed">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5 w-1/4">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Zapytanie</div>
              </th>
              <th class="px-6 py-1.5 w-40">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Typ</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Klient</div>
              </th>
              <th class="px-6 py-1.5 w-32">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Status</div>
              </th>
              <th class="px-6 py-1.5 w-40">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Dodał</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in ofertas.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="">
                <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/oferta/${item.id}/edit`">
                  <div class="truncate">
                    <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate" :title="item.zapytania ? item.zapytania.nazwa_projektu : ''">
                      {{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak zapytania' }}
                    </div>
                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                      {{ item.zapytania ? item.zapytania.id_zapyt : '-' }}
                    </div>
                  </div>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="">
                <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded uppercase tracking-tighter leading-tight whitespace-nowrap scale-[0.7] origin-left inline-block shadow-sm">
                    {{ item.typ || 'Brak' }}
                  </span>
                </Link>
              </td>
              <td class="">
                <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.client" class="text-xs font-medium truncate" :title="item.client.nazwa">
                    {{ item.client.nazwa }}
                  </div>
                </Link>
              </td>
              <td class="">
                <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.status" class="flex items-center">
                    <span class="px-2 py-0.5 text-[10px] font-bold leading-none text-white bg-indigo-500 rounded truncate shadow-sm scale-[0.7] origin-left inline-block uppercase tracking-tighter">
                      {{ item.status.name }}
                    </span>
                  </div>
                </Link>
              </td>
              <td class="">
                <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <div class="text-xs truncate">
                    <div class="font-medium text-gray-700 truncate">{{ item.user.first_name }} {{ item.user.last_name }}</div>
                    <div class="text-[10px] text-gray-400 mt-0.5">{{ item.created_at }}</div>
                  </div>
                </Link>
              </td>
              <td class="">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm ml-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="ofertas.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="6">
                <div class="flex flex-col items-center">
                  <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Brak ofert spełniających kryteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <pagination class="mt-6" :links="ofertas.links" />
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
    ofertas: Object,
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
        this.$inertia.get('/oferta', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    formatCurrency(value) {
      if (!value) return '0'
      return new Intl.NumberFormat('pl-PL').format(value)
    },
  },
}
</script>

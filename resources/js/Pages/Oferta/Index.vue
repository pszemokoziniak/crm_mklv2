<template>
  <div>
    <Head title="Oferty" />
    <h1 class="mb-8 text-gray-900 text-3xl font-bold">Oferty</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block mb-1 text-gray-700 text-sm font-medium">Status:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm focus:ring-indigo-500">
          <option :value="null">Aktualne</option>
          <option value="with">Wszystkie</option>
          <option value="only">Tylko usunięte</option>
        </select>
      </search-filter>
      <!--      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95" href="/oferta/create">-->
      <!--        <icon name="plus" class="w-4 h-4 mr-2" />-->
      <!--        <span>Dodaj ofertę</span>-->
      <!--      </Link>-->
    </div>
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
      <div class="scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 overflow-x-auto">
        <table class="table-fixed w-full">
          <thead>
            <tr class="bg-gray-50/50 text-left text-gray-500 border-b border-gray-100">
              <th class="px-6 py-1.5 w-1/4">
                <div class="text-[10px] scale-[0.8] whitespace-nowrap font-semibold tracking-tight origin-left uppercase">Zapytanie</div>
              </th>
              <th class="px-6 py-1.5 w-40">
                <div class="text-[4px] whitespace-nowrap font-semibold tracking-tight uppercase">Typ</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] scale-[0.8] whitespace-nowrap font-semibold tracking-tight origin-left uppercase">Klient</div>
              </th>
              <th class="px-6 py-1.5 w-32">
                <div class="text-[4px] whitespace-nowrap font-semibold tracking-tight uppercase">Status</div>
              </th>
              <th class="px-6 py-1.5 w-40">
                <div class="text-[10px] scale-[0.8] whitespace-nowrap font-semibold tracking-tight origin-left uppercase">Dodał</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-gray-50 divide-y">
            <tr v-for="item in ofertas.data" :key="item.id" class="hover:bg-indigo-50/30 group transition-colors">
              <td class="">
                <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/oferta/${item.id}/edit`">
                  <div class="truncate">
                    <div class="text-gray-900 group-hover:text-indigo-600 font-bold transition-colors truncate" :title="item.zapytania ? item.zapytania.nazwa_projektu : ''">
                      {{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak zapytania' }}
                    </div>
                    <div class="text-[10px] mt-0.5 text-gray-400 font-medium">
                      {{ item.zapytania ? item.zapytania.id_zapyt : '-' }}
                    </div>
                  </div>
                  <icon v-if="item.deleted_at" name="trash" class="fill-rose-400 flex-shrink-0 ml-2 w-3 h-3" />
                </Link>
              </td>
              <td class="">
                <Link class="" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <span class="">
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
                    <span class="text-[4px] inline-block px-2 py-0.5 text-white font-bold tracking-tighter leading-none bg-indigo-500 rounded shadow-sm uppercase truncate">
                      {{ item.status.name }}
                    </span>
                  </div>
                </Link>
              </td>
              <td class="">
                <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <div class="text-xs truncate">
                    <div class="text-gray-700 font-medium truncate">{{ item.user.first_name }} {{ item.user.last_name }}</div>
                    <div class="text-[10px] mt-0.5 text-gray-400">{{ item.created_at }}</div>
                  </div>
                </Link>
              </td>
              <td class="">
                <Link class="inline-flex items-center justify-center ml-4 w-7 h-7 text-gray-400 group-hover:text-white bg-gray-50 group-hover:bg-indigo-600 rounded-full shadow-sm transition-all" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="ofertas.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="6">
                <div class="flex flex-col items-center">
                  <icon name="zapytania" class="mb-2 w-12 h-12 opacity-20" />
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

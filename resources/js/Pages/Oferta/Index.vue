<template>
  <div>
    <Head title="Oferty" />
    <h1 class="mb-8 text-3xl font-bold">Oferty</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Status:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option :value="null">Wszystko</option>
          <option value="with">Z usuniętymi</option>
          <option value="only">Tylko usunięte</option>
        </select>
      </search-filter>
      <Link class="btn-indigo" href="/oferta/create">
        <span>Dodaj</span>
        <span class="hidden md:inline">&nbsp;ofertę</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-hidden">
      <table class="w-full table-fixed">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6 w-1/4">Zapytanie</th>
            <th class="pb-4 pt-6 px-6 w-40">Typ</th>
            <th class="pb-4 pt-6 px-6">Klient</th>
            <th class="pb-4 pt-6 px-6 w-40">Kwota</th>
            <th class="pb-4 pt-6 px-6 w-32">Status</th>
            <th class="pb-4 pt-6 px-6 w-40">Dodał</th>
            <th class="pb-4 pt-6 px-6 w-12"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in ofertas.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/oferta/${item.id}/edit`">
                <div class="truncate">
                  <div class="font-semibold text-gray-900 truncate" :title="item.zapytania ? item.zapytania.nazwa_projektu : ''">
                    {{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak zapytania' }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ item.zapytania ? item.zapytania.id_zapyt : '-' }}
                  </div>
                </div>
                <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 truncate">
                  {{ item.typ || 'Brak' }}
                </span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <div v-if="item.client" class="text-sm text-gray-900 truncate" :title="item.client.nazwa">
                  {{ item.client.nazwa }}
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 font-medium" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <div v-if="item.kwota" class="text-sm">
                  <div class="whitespace-nowrap">{{ formatCurrency(item.kwota) }} {{ item.waluta ? item.waluta.name : '' }}</div>
                  <div v-if="item.waluta && item.waluta.name !== 'PLN'" class="text-xs text-gray-500 whitespace-nowrap">
                    {{ formatCurrency(item.kwotaPLN) }} PLN
                  </div>
                </div>
                <span v-else>-</span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <div v-if="item.status" class="flex items-center">
                  <span class="px-2 py-1 text-xs font-bold leading-none text-white bg-indigo-500 rounded truncate">
                    {{ item.status.name }}
                  </span>
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <div class="text-sm truncate">
                  <div class="text-gray-900 truncate">{{ item.user.first_name }} {{ item.user.last_name }}</div>
                  <div class="text-gray-500 text-xs">{{ item.created_at }}</div>
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="ofertas.data.length === 0">
            <td class="px-6 py-4 border-t text-center text-gray-500" colspan="7">Brak ofert.</td>
          </tr>
        </tbody>
      </table>
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

<template>
  <div>
    <Head title="Contacts" />
    <h1 class="mb-8 text-3xl font-bold">Zapytania</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Wybierz:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option value="with">Wszystko</option>
          <option value="only">Archiwum</option>
        </select>
      </search-filter>
      <Link class="btn-indigo" href="/zapytania/create">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">ID/Projekt</th>
          <th class="pb-4 pt-6 px-6">Klient</th>
          <th class="pb-4 pt-6 px-6">Kraj</th>
          <th class="pb-4 pt-6 px-6">Kwota szacowana</th>
          <th class="pb-4 pt-6 px-6">Zakres</th>
          <th class="pb-4 pt-6 px-6">Dodał</th>
        </tr>
        <tr v-for="item in zapytanias.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/zapytania/${item.id}/edit`">
              {{ item.nazwa_projektu }} <br /> {{ item.id_zapyt }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              <div v-if="item.client">
               {{ item.client.nazwa }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              <div v-if="item.kraj">
                {{ item.kraj.name }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              {{ formatNumber(item.kwota) }} {{ item.waluta.name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              <div v-if="item.zakres">
                {{ item.zakres.name }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              {{ item.user.last_name }} {{ item.user.first_name }}<br>{{ item.created_at }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/zapytania/${item.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="zapytanias.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak zapytań.</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="zapytanias.links" />-->
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
      return new Intl.NumberFormat('pl-PL',{
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num)
    },
  },
}
</script>

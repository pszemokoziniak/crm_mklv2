<template>
  <div>
    <Head title="Klienci" />
    <h1 class="mb-8 text-3xl font-bold">Klienci</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Status:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option value="with">Wszystko</option>
          <option value="only">Archiwum</option>
        </select>
      </search-filter>
      <Link class="btn-indigo" href="/clients/create">
        <span>Dodaj klienta</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold bg-gray-50">
            <th class="pb-4 pt-6 px-6">Nazwa</th>
            <th class="pb-4 pt-6 px-6">Branża</th>
            <th class="pb-4 pt-6 px-6">Kraj</th>
            <th class="pb-4 pt-6 px-6">Opiekun</th>
            <th class="pb-4 pt-6 px-6">Utworzył</th>
            <th class="pb-4 pt-6 px-6" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in clients.data" :key="item.id" class="hover:bg-gray-50 focus-within:bg-gray-100 transition-colors">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500 font-medium text-indigo-700" :href="`/clients/${item.id}/edit`">
                {{ item.nazwa }}
                <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/clients/${item.id}/edit`" tabindex="-1">
                {{ item.branza }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/clients/${item.id}/edit`" tabindex="-1">
                {{ item.kraj }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/clients/${item.id}/edit`" tabindex="-1">
                {{ item.user }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex flex-col px-6 py-4" :href="`/clients/${item.id}/edit`" tabindex="-1">
                <span class="text-xs font-semibold text-gray-700">{{ item.created_by }}</span>
                <span class="text-xs text-gray-400 mt-0.5">{{ item.created_at }}</span>
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/clients/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="clients.data.length === 0">
            <td class="px-6 py-4 border-t text-center text-gray-500" colspan="6">Brak klientów do wyświetlenia.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="clients.links" />
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
    clients: Object,
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
        this.$inertia.get('/clients', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>

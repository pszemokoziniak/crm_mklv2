<template>
  <div>
    <Head title="Przyszłe projekty" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Przyszłe projekty</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/futureproject/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj</span>
      </Link>
    </div>

    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-sm font-medium text-gray-700 mb-1">Archiwum:</label>
        <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option :value="null" />
          <option value="with">Wszystko</option>
          <option value="only">Archiwum</option>
        </select>
      </search-filter>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Projekt</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Klient</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Kraj / Miasto</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Rodzaj obiektu</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Faza projektu</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in futureprojects.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="flex items-center focus:text-indigo-500" :href="`/futureproject/${item.id}/edit`">
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors truncate" :title="item.nazwa">{{ item.nazwa }}</span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center text-gray-600" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.client" class="text-sm truncate" :title="item.client.nazwa">{{ item.client.nazwa }}</span>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center text-gray-600" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <div class="text-sm truncate">
                    <span v-if="item.kraj" class="font-semibold">{{ item.kraj.name }}</span>
                    <span v-if="item.miasto" class="text-gray-500 ml-1">({{ item.miasto }})</span>
                  </div>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.objekt" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold truncate" :title="item.objekt.name">
                    {{ item.objekt.name }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.faza" class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold truncate" :title="item.faza.name">
                    {{ item.faza.name }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <Link class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-5 h-5" />
                </Link>
              </td>
            </tr>
            <tr v-if="futureprojects.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="6">
                <div class="flex flex-col items-center">
                  <icon name="users" class="w-12 h-12 mb-2 opacity-20" />
                  <p>Brak projektów.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-8">
      <pagination :links="futureprojects.links" />
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
    futureprojects: Object,
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
        this.$inertia.get('/futureproject', pickBy(this.form), { preserveState: true })
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

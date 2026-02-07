<template>
  <div>
    <Head title="Przyszłe projekty" />
    <h1 class="mb-8 text-3xl font-bold">Przyszłe projekty</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Status:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option :value="null" />
          <option value="with">Wszystko</option>
          <option value="only">Archiwum</option>
        </select>
      </search-filter>
      <Link class="btn-indigo" href="/futureproject/create">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">Projekt</th>
            <th class="pb-4 pt-6 px-6">Klient</th>
            <th class="pb-4 pt-6 px-6">Kraj / Miasto</th>
            <th class="pb-4 pt-6 px-6">Start / Koniec</th>
            <th class="pb-4 pt-6 px-6">Rodzaj obiektu</th>
            <th class="pb-4 pt-6 px-6">Faza projektu</th>
            <th class="pb-4 pt-6 px-6">Dodał</th>
            <th class="pb-4 pt-6 px-6" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in futureprojects.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/futureproject/${item.id}/edit`">
                <div class="flex flex-col">
                  <span class="font-medium">{{ item.nazwa }}</span>
                  <span v-if="item.deleted_at" class="text-xs text-red-500">Usunięty</span>
                </div>
                <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <div v-if="item.client" class="text-sm">
                  {{ item.client.nazwa }}
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <div class="text-sm">
                  <span v-if="item.kraj" class="font-semibold">{{ item.kraj.name }}</span>
                  <span v-if="item.miasto" class="text-gray-500 ml-1">({{ item.miasto }})</span>
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <div class="flex flex-col">
                  <span>{{ item.start }}</span>
                  <span class="text-gray-400 text-xs">{{ item.end }}</span>
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <span v-if="item.objekt" class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                  {{ item.objekt.name }}
                </span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <span v-if="item.faza" class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                  {{ item.faza.name }}
                </span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <div class="flex flex-col">
                  <span>{{ item.user.first_name }} {{ item.user.last_name }}</span>
                  <span class="text-gray-400 text-xs">{{ item.created_at }}</span>
                </div>
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="futureprojects.data.length === 0">
            <td class="px-6 py-4 border-t" colspan="8">Brak projektów.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="futureprojects.links" />
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

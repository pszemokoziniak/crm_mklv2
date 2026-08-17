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
        <div class="mb-4">
          <label class="block mb-1 text-gray-700 text-sm font-medium">Faza projektu:</label>
          <select v-model="form.faza_id" class="form-select mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm focus:ring-indigo-500">
            <option :value="null">Wszystkie</option>
            <option v-for="item in faza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select>
        </div>
        <label class="block mb-1 text-gray-700 text-sm font-medium">Wyświetlaj:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm focus:ring-indigo-500">
          <option :value="null">Aktualne</option>
          <option value="only">Archiwum</option>
          <option value="with">Wszystko</option>
        </select>
      </search-filter>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div>
        <table class="w-full table-fixed">
          <colgroup>
            <col style="width:24%" /><!-- Projekt -->
            <col style="width:22%" /><!-- Klient -->
            <col style="width:18%" /><!-- Kraj / Miasto -->
            <col style="width:16%" /><!-- Rodzaj obiektu -->
            <col style="width:17%" /><!-- Faza projektu -->
            <col style="width:3%" /><!-- Arrow -->
          </colgroup>
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('nazwa')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'nazwa' }">
                  <span>Projekt</span>
                  <span v-if="form.field === 'nazwa'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('client')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'client' }">
                  <span>Klient</span>
                  <span v-if="form.field === 'client'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('kraj')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'kraj' }">
                  <span>Kraj / Miasto</span>
                  <span v-if="form.field === 'kraj'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('objekt')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'objekt' }">
                  <span>Rodzaj obiektu</span>
                  <span v-if="form.field === 'objekt'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('faza')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'faza' }">
                  <span>Faza projektu</span>
                  <span v-if="form.field === 'faza'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in futureprojects.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center focus:text-indigo-500" :href="`/futureproject/${item.id}/edit`">
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-sm truncate" :title="item.nazwa">{{ item.nazwa }}</span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.client" class="text-sm" :title="item.client.nazwa">{{ item.client.nazwa }}</span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <div class="text-sm">
                    <span v-if="item.kraj" class="font-semibold">{{ item.kraj.name }}</span>
                    <span v-if="item.miasto" class="text-gray-500 ml-1">({{ item.miasto }})</span>
                  </div>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.objekt" class="inline-block max-w-full truncate px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold" :title="item.objekt.name">
                    {{ item.objekt.name }}
                  </span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.faza" class="inline-block max-w-full truncate px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold" :title="item.faza.name">
                    {{ item.faza.name }}
                  </span>
                </Link>
              </td>
              <td class="px-1 py-3 text-center">
                <Link class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-3 h-3" />
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
    faza: Object,
    futureprojects: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        faza_id: this.filters.faza_id ? Number(this.filters.faza_id) : null,
        trashed: this.filters.trashed,
        field: this.filters.field || null,
        direction: this.filters.direction || null,
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
  },
}
</script>

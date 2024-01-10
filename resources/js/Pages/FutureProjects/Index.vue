<template>
  <div>
    <Head title="Contacts" />
    <h1 class="mb-8 text-3xl font-bold">Przyszłe projekty</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Wybierz:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
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
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Projekt</th>
          <th class="pb-4 pt-6 px-6">Klient</th>
          <th class="pb-4 pt-6 px-6">Kraj / Miasto</th>
          <th class="pb-4 pt-6 px-6">Start</th>
          <th class="pb-4 pt-6 px-6">Koniec</th>
          <th class="pb-4 pt-6 px-6">Rodzaj obiektu</th>
          <th class="pb-4 pt-6 px-6">Faza projektu</th>
          <th class="pb-4 pt-6 px-6">Dodał</th>
        </tr>
        <tr v-for="item in futureprojects.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/futureproject/${item.id}/edit`">
              {{ item.nazwa }} <br />
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              <div v-if="item.client">
               {{ item.client.nazwa }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              <div v-if="item.kraj">
                {{ item.kraj.name }} / {{ item.miasto }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              {{ item.start }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              {{ item.end }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              <div v-if="item.objekt">
                {{ item.objekt.name }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              <div v-if="item.faza">
                {{ item.faza.name }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              {{ item.user.last_name }} {{ item.user.first_name }}<br>{{ item.created_at }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/futureproject/${item.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="futureprojects.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak zapytań.</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="futureprojects.links" />-->
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

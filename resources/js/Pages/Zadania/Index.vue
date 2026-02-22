<template>
  <div>
    <Head title="Zadania" />
    <h1 class="mb-8 text-3xl font-bold">Zadania</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Wybierz:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option value="with">Wszystko</option>
          <option value="only">Archiwum</option>
        </select>
      </search-filter>
      <Link class="btn-indigo" href="/zadania/create">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Temat</th>
          <th class="pb-4 pt-6 px-6">Termin wykonania</th>
          <th class="pb-4 pt-6 px-6">Dodał</th>
          <th class="pb-4 pt-6 px-6">Obecny opiekun / Data</th>
          <th class="pb-4 pt-6 px-6" colspan="2" />
        </tr>
        <tr v-for="item in zadanias.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/zadania/${item.id}/edit`">
              <div v-if="item.subject">
                {{ item.subject }}
              </div>
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zadania/${item.id}/edit`" tabindex="-1">
              <div v-if="item.deadline" :class="getDeadlineClass(item.deadline)" class="px-2 py-1 rounded font-medium">
                {{ item.deadline }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/zadania/${item.id}/edit`" tabindex="-1">
              <span v-if="item.user">
                {{ item.user.last_name }} {{ item.user.first_name }}
              </span>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex flex-col px-6 py-4" :href="`/zadania/${item.id}/edit`" tabindex="-1">
              <span v-if="item.responsible_person_id" class="font-medium">
                {{ item.responsible_person_id.last_name }} {{ item.responsible_person_id.first_name }}
              </span>
              <span class="text-xs text-gray-500 mt-1">{{ item.created_at }}</span>
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/zadania/${item.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="zadanias.data.length === 0">
          <td class="px-6 py-4 border-t" colspan="5">Brak zadań.</td>
        </tr>
      </table>
    </div>
    <pagination class="mt-6" :links="zadanias.links" />
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
    zadanias: Object,
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
        this.$inertia.get('/zadania', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    getDeadlineClass(deadline) {
      if (!deadline) return ''

      const today = new Date()
      today.setHours(0, 0, 0, 0)

      const deadlineDate = new Date(deadline)
      deadlineDate.setHours(0, 0, 0, 0)

      const diffTime = deadlineDate - today
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

      if (diffDays < 0) {
        return 'bg-red-100 text-red-800' // Przeterminowane
      } else if (diffDays <= 7) {
        return 'bg-orange-100 text-orange-800' // Mniej niż 7 dni
      }

      return 'text-gray-700'
    },
  },
}
</script>

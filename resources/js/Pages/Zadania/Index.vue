<template>
  <div>
    <Head title="Zadania" />
    <h1 class="mb-8 text-3xl font-bold text-gray-900">Zadania</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
        <select v-model="form.status" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-2">
          <option :value="null">Wszystkie</option>
          <option value="aktywne">Aktywne</option>
          <option value="do_akceptacji">Do akceptacji</option>
          <option value="zamkniete">Zamknięte</option>
        </select>
        <label class="block text-sm font-medium text-gray-700 mb-1">Wyświetlaj:</label>
        <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <option :value="null">Aktualne</option>
          <option value="only">Archiwum</option>
          <option value="with">Wszystko</option>
        </select>
      </search-filter>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95" href="/zadania/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj zadanie</span>
      </Link>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full whitespace-nowrap">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Temat</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Status</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Termin wykonania</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Dodał</div>
              </th>
              <th class="px-6 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Obecny opiekun / Data</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in zadanias.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="flex items-center focus:text-indigo-500" :href="`/zadania/${item.id}/edit`">
                  <span v-if="item.subject" class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                    {{ item.subject }}
                  </span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span :class="getStatusClass(item.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                    {{ getStatusLabel(item.status) }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.deadline" :class="getDeadlineClass(item.deadline)" class="px-2 py-0.5 rounded text-xs font-bold shadow-sm">
                    {{ item.deadline ? item.deadline.split('T')[0] : '-' }}
                  </div>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center text-gray-600" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.user" class="text-xs font-medium">
                    {{ item.user.last_name }} {{ item.user.first_name }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex flex-col" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.responsible_person_id" class="text-xs font-medium text-gray-700">
                    {{ item.responsible_person_id.last_name }} {{ item.responsible_person_id.first_name }}
                  </span>
                  <span class="text-[10px] text-gray-400 mt-0.5">{{ item.created_at }}</span>
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="zadanias.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="6">
                <div class="flex flex-col items-center">
                  <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Brak zadań spełniających kryteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
        status: this.filters.status,
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
    getStatusClass(status) {
      switch (status) {
      case 'aktywne': return 'bg-green-100 text-green-800'
      case 'do_akceptacji': return 'bg-yellow-100 text-yellow-800'
      case 'zamkniete': return 'bg-gray-100 text-gray-600'
      default: return 'bg-green-100 text-green-800'
      }
    },
    getStatusLabel(status) {
      switch (status) {
      case 'aktywne': return 'Aktywne'
      case 'do_akceptacji': return 'Do akceptacji'
      case 'zamkniete': return 'Zamknięte'
      default: return 'Aktywne'
      }
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
        return 'bg-red-50 text-red-600 border border-red-100' // Przeterminowane
      } else if (diffDays <= 7) {
        return 'bg-orange-50 text-orange-600 border border-orange-100' // Mniej niż 7 dni
      }

      return 'bg-gray-50 text-gray-600 border border-gray-100'
    },
  },
}
</script>

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
      <div>
        <table class="w-full table-fixed">
          <colgroup>
            <col style="width:26%" /><!-- Temat -->
            <col style="width:22%" /><!-- Klient -->
            <col style="width:12%" /><!-- Status -->
            <col style="width:15%" /><!-- Termin wykonania -->
            <col style="width:22%" /><!-- Obecny opiekun / Data -->
            <col style="width:3%" /><!-- Arrow -->
          </colgroup>
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('subject')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'subject' }">
                  <span>Temat</span>
                  <span v-if="form.field === 'subject'" class="ml-1">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="ml-1 text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('client')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'client' }">
                  <span>Klient</span>
                  <span v-if="form.field === 'client'" class="ml-1">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="ml-1 text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('status')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'status' }">
                  <span>Status</span>
                  <span v-if="form.field === 'status'" class="ml-1">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="ml-1 text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('deadline')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'deadline' }">
                  <span>Termin wykonania</span>
                  <span v-if="form.field === 'deadline'" class="ml-1">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="ml-1 text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('created_at')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'created_at' }">
                  <span>Obecny opiekun / Data</span>
                  <span v-if="form.field === 'created_at'" class="ml-1">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="ml-1 text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in zadanias.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center focus:text-indigo-500" :href="`/zadania/${item.id}/edit`">
                  <span v-if="item.subject" class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-sm truncate" :title="item.subject">
                    {{ item.subject }}
                  </span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.client" class="text-xs font-medium" :title="item.client.nazwa">{{ item.client.nazwa }}</span>
                  <span v-else class="text-xs text-gray-300 italic">—</span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span :class="getStatusClass(item.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap">
                    {{ getStatusLabel(item.status) }}
                  </span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <div v-if="item.deadline" :class="getDeadlineClass(item.deadline)" class="px-2 py-0.5 rounded text-xs font-bold shadow-sm whitespace-nowrap">
                    {{ item.deadline ? item.deadline.split('T')[0] : '-' }}
                  </div>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex flex-col" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <span v-if="item.responsible_person_id" class="text-xs font-medium text-gray-700 truncate">
                    {{ item.responsible_person_id.last_name }} {{ item.responsible_person_id.first_name }}
                  </span>
                  <span class="text-[10px] text-gray-400 mt-0.5">{{ item.created_at }}</span>
                </Link>
              </td>
              <td class="px-1 py-3 text-center">
                <Link class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/zadania/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-3 h-3" />
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
        field: this.filters.field || null,
        direction: this.filters.direction || null,
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

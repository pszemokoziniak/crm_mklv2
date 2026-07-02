<template>
  <div>
    <Head title="Klienci" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Klienci</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/clients/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj klienta</span>
      </Link>
    </div>

    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
          <select v-model="form.status" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option :value="null">Wszystko</option>
            <option value="aktywni">Aktywni</option>
            <option value="nieaktywni">Nieaktywni</option>
            <option value="zapytania">Zapytania</option>
          </select>
          <ul class="mt-2 space-y-1 text-xs text-gray-500 leading-snug">
            <li><span class="font-semibold text-gray-700">Wszystko</span> – wszyscy klienci</li>
            <li><span class="font-semibold text-gray-700">Aktywni</span> – zapytanie lub kontakt z Klientem w ostatnich 6 miesiącach</li>
            <li><span class="font-semibold text-gray-700">Nieaktywni</span> – brak zapytania oraz kontaktu z Klientem w ostatnich 6 miesiącach</li>
            <li><span class="font-semibold text-gray-700">Zapytania</span> – otrzymane zapytanie od Klienta w obecnym roku kalendarzowym</li>
          </ul>
        </div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Wyświetlaj:</label>
        <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
            <col style="width:30%" /><!-- Nazwa -->
            <col style="width:20%" /><!-- Branza -->
            <col style="width:14%" /><!-- Kraj -->
            <col style="width:16%" /><!-- Z/O/K -->
            <col style="width:17%" /><!-- Opiekun -->
            <col style="width:3%" /><!-- Arrow -->
          </colgroup>
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('nazwa')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'nazwa' }">
                  <span>Nazwa</span>
                  <span v-if="form.field === 'nazwa'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('branza')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'branza' }">
                  <span>Branża</span>
                  <span v-if="form.field === 'branza'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('kraj')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'kraj' }">
                  <span>Kraj</span>
                  <span v-if="form.field === 'kraj'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5">
                <div class="text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap">Z / O / K</div>
              </th>
              <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="sort('user')">
                <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'user' }">
                  <span>Opiekun</span>
                  <span v-if="form.field === 'user'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                  <span v-else class="text-gray-300">↕</span>
                </div>
              </th>
              <th class="px-3 py-1.5" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in clients.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-3 py-3 overflow-hidden">
                <Link class="flex items-center focus:text-indigo-500" :href="`/clients/${item.id}/edit`">
                  <div :class="item.is_active ? 'bg-green-500' : 'bg-red-500'" class="flex-shrink-0 w-2 h-2 rounded-full mr-2 shadow-sm" :title="item.is_active ? 'Aktywny (ostatnie 6 m-cy)' : 'Nieaktywny'" />
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-sm truncate" :title="item.nazwa">{{ item.nazwa }}</span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/clients/${item.id}/edit`" tabindex="-1">
                  <span class="text-sm" :title="item.branza">{{ item.branza }}</span>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/clients/${item.id}/edit`" tabindex="-1">
                  <span class="text-sm whitespace-nowrap" :title="item.kraj">{{ item.kraj }}</span>
                </Link>
              </td>
              <td class="px-3 py-3">
                <Link class="flex items-center gap-1" :href="`/clients/${item.id}/edit`" tabindex="-1">
                  <div class="flex items-center justify-center w-6 h-6 rounded bg-indigo-50 text-indigo-600 text-xs font-bold" title="Zapytania">
                    {{ item.zapytania_count }}
                  </div>
                  <div class="flex items-center justify-center w-6 h-6 rounded bg-blue-50 text-blue-600 text-xs font-bold" title="Oferty">
                    {{ item.oferty_count }}
                  </div>
                  <div class="flex items-center justify-center w-6 h-6 rounded bg-emerald-50 text-emerald-600 text-xs font-bold" title="Kontakty">
                    {{ item.kontakty_count }}
                  </div>
                </Link>
              </td>
              <td class="px-3 py-3 overflow-hidden">
                <Link class="block text-gray-600 truncate" :href="`/clients/${item.id}/edit`" tabindex="-1">
                  <span class="text-sm font-medium" :title="item.user">{{ item.user }}</span>
                </Link>
              </td>
              <td class="px-1 py-3 text-center">
                <Link class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/clients/${item.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-3 h-3" />
                </Link>
              </td>
            </tr>
            <tr v-if="clients.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="7">
                <div class="flex flex-col items-center">
                  <icon name="users" class="w-12 h-12 mb-2 opacity-20" />
                  <p>Brak klientów spełniających kryteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-8">
      <pagination :links="clients.links" />
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
    clients: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        status: this.filters.status,
        field: this.filters.field,
        direction: this.filters.direction,
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
    sort(field) {
      this.form.field = field
      this.form.direction = this.form.direction === 'asc' ? 'desc' : 'asc'
    },
  },
}
</script>

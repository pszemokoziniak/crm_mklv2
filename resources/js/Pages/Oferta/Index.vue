<template>
  <div>
    <Head title="Oferty" />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-3">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Oferty</h1>
    </div>

    <!-- Statystyki -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Wszystkie</p>
        <p class="text-xl font-bold text-gray-900">{{ stats.total }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1">Ten miesiąc</p>
        <div class="flex items-baseline gap-2">
          <p class="text-xl font-bold text-gray-900">{{ stats.this_month }}</p>
          <span v-if="stats.prev_month > 0" class="text-[10px] font-semibold" :class="monthChange >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ monthChange >= 0 ? '↑' : '↓' }} {{ Math.abs(monthChange) }}%
          </span>
        </div>
      </div>
      <div v-for="s in topStatuses" :key="s.name" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-500 mb-1 truncate">{{ s.name }}</p>
        <p class="text-xl font-bold text-indigo-600">{{ s.count }}</p>
      </div>
    </div>

    <!-- Filtry -->
    <div class="flex items-center justify-between mb-4">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <div class="mb-4">
          <label class="block mb-1 text-gray-700 text-sm font-medium">Status:</label>
          <select v-model="form.status" class="form-select mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm focus:ring-indigo-500">
            <option :value="null">Wszystkie</option>
            <option value="wygrana">Wygrana</option>
            <option value="toczy">Toczy</option>
            <option value="zawieszona przez inwestora">Zawieszona przez Inwestora</option>
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

    <!-- Mobile: widok kart -->
    <div class="md:hidden space-y-3">
      <Link
        v-for="item in ofertas.data"
        :key="item.id"
        :href="`/oferta/${item.id}/edit`"
        class="block bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:border-indigo-200 hover:shadow transition-all active:scale-[0.99]"
      >
        <div class="flex items-start justify-between gap-2 mb-2">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
              <span class="font-bold text-sm text-gray-900 line-clamp-2">{{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak zapytania' }}</span>
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-rose-400" />
            </div>
            <span class="text-[10px] text-gray-400 font-medium">{{ item.zapytania ? item.zapytania.id_zapyt : '-' }}</span>
          </div>
          <icon name="cheveron-right" class="flex-shrink-0 w-5 h-5 text-gray-300" />
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-gray-500 mb-2">
          <span v-if="item.client" class="truncate max-w-[60%]">
            <span class="text-gray-400">Klient:</span> {{ item.client.nazwa }}
          </span>
          <span v-if="item.typ" class="text-gray-400">{{ item.typ }}</span>
        </div>
        <div class="flex items-center justify-between">
          <select
            v-if="item.status"
            :value="item.status.id"
            @click.stop.prevent
            @change.stop="changeStatus(item, $event.target.value)"
            class="px-2 py-0.5 text-[10px] font-bold text-white bg-indigo-500 rounded uppercase border-0 cursor-pointer focus:ring-2 focus:ring-indigo-300 appearance-none"
          >
            <option v-for="opt in statusOptions" :key="opt.id" :value="opt.id" class="text-gray-900 bg-white normal-case font-normal">{{ opt.name }}</option>
          </select>
          <span v-if="item.kwota" class="text-xs font-semibold text-gray-700">{{ formatCurrency(item.kwota) }} {{ item.waluta ? item.waluta.name : '' }}</span>
        </div>
        <div v-if="item.user" class="mt-2 flex items-center justify-between text-[11px] text-gray-400">
          <span>{{ item.user.first_name }} {{ item.user.last_name }}</span>
          <span>{{ item.created_at }}</span>
        </div>
      </Link>
      <div v-if="ofertas.data.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-100 p-12 text-center text-gray-400">
        <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20 mx-auto" />
        <p class="text-xs">Brak ofert spełniających kryteria.</p>
      </div>
    </div>

    <!-- Desktop: widok tabeli -->
    <div class="hidden md:block bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
      <table class="w-full table-fixed">
        <colgroup>
          <col style="width:22%" /><!-- Zapytanie -->
          <col style="width:22%" /><!-- Klient -->
          <col style="width:11%" class="hidden lg:table-column" /><!-- Typ -->
          <col style="width:14%" /><!-- Status -->
          <col style="width:14%" class="hidden lg:table-column" /><!-- Kwota -->
          <col style="width:14%" class="hidden lg:table-column" /><!-- Dodał -->
          <col style="width:3%" /><!-- Arrow -->
        </colgroup>
        <thead>
          <tr class="bg-gray-50/50 text-left text-gray-500 border-b border-gray-100">
            <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('zapytanie')">
              <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'zapytanie' }">
                <span>Zapytanie</span>
                <span v-if="form.field === 'zapytanie'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
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
            <th class="px-3 py-1.5 hidden lg:table-cell cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('typ')">
              <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'typ' }">
                <span>Typ</span>
                <span v-if="form.field === 'typ'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                <span v-else class="text-gray-300">↕</span>
              </div>
            </th>
            <th class="px-3 py-1.5 cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('status')">
              <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'status' }">
                <span>Status</span>
                <span v-if="form.field === 'status'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                <span v-else class="text-gray-300">↕</span>
              </div>
            </th>
            <th class="px-3 py-1.5 hidden lg:table-cell cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('kwota')">
              <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'kwota' }">
                <span>Kwota</span>
                <span v-if="form.field === 'kwota'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                <span v-else class="text-gray-300">↕</span>
              </div>
            </th>
            <th class="px-3 py-1.5 hidden lg:table-cell cursor-pointer hover:text-indigo-600 transition-colors" @click="toggleSort('dodal')">
              <div class="flex items-center gap-1 text-[10px] font-semibold uppercase tracking-tight whitespace-nowrap" :class="{ 'text-indigo-600': form.field === 'dodal' }">
                <span>Dodał</span>
                <span v-if="form.field === 'dodal'">{{ form.direction === 'asc' ? '↑' : '↓' }}</span>
                <span v-else class="text-gray-300">↕</span>
              </div>
            </th>
            <th class="py-1.5" />
          </tr>
        </thead>
        <tbody class="divide-gray-50 divide-y">
          <tr v-for="item in ofertas.data" :key="item.id" class="hover:bg-indigo-50/30 group transition-colors">
            <td class="px-3 py-2.5 overflow-hidden">
              <Link class="block truncate focus:text-indigo-500" :href="`/oferta/${item.id}/edit`">
                <div class="flex items-center">
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors text-xs truncate" :title="item.zapytania ? item.zapytania.nazwa_projektu : ''">
                    {{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak zapytania' }}
                  </span>
                  <icon v-if="item.deleted_at" name="trash" class="fill-rose-400 flex-shrink-0 ml-2 w-3 h-3" />
                </div>
                <div v-if="item.zapytania && item.zapytania.id_zapyt" class="text-[10px] mt-0.5 text-gray-400 font-medium truncate">
                  {{ item.zapytania.id_zapyt }}
                </div>
              </Link>
            </td>
            <td class="px-3 py-2.5 overflow-hidden">
              <Link class="block text-gray-600 truncate" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <span v-if="item.client" class="text-xs font-medium" :title="item.client.nazwa">{{ item.client.nazwa }}</span>
              </Link>
            </td>
            <td class="px-3 py-2.5 hidden lg:table-cell overflow-hidden">
              <Link class="text-xs text-gray-600 truncate block" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                {{ item.typ || '-' }}
              </Link>
            </td>
            <td class="px-3 py-2.5 overflow-hidden">
              <select
                v-if="item.status"
                :value="item.status.id"
                @change="changeStatus(item, $event.target.value)"
                class="px-1.5 py-0.5 text-[8px] text-white font-bold tracking-tight leading-tight bg-indigo-500 rounded shadow-sm uppercase border-0 max-w-full cursor-pointer focus:ring-2 focus:ring-indigo-300 appearance-none"
                :title="item.status.name"
              >
                <option v-for="opt in statusOptions" :key="opt.id" :value="opt.id" class="text-gray-900 bg-white normal-case font-normal text-xs">{{ opt.name }}</option>
              </select>
            </td>
            <td class="px-3 py-2.5 hidden lg:table-cell overflow-hidden">
              <Link class="block text-xs text-gray-700 font-medium truncate" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <template v-if="item.kwota">{{ formatCurrency(item.kwota) }} {{ item.waluta ? item.waluta.name : '' }}</template>
              </Link>
            </td>
            <td class="px-3 py-2.5 hidden lg:table-cell overflow-hidden">
              <Link class="block" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <div class="text-xs font-medium text-gray-700 truncate">{{ item.user.first_name }} {{ item.user.last_name }}</div>
                <div class="text-[10px] mt-0.5 text-gray-400">{{ item.created_at }}</div>
              </Link>
            </td>
            <td class="px-1 py-2.5 text-center">
              <Link class="inline-flex items-center justify-center w-6 h-6 text-gray-400 group-hover:text-white bg-gray-50 group-hover:bg-indigo-600 rounded-full shadow-sm transition-all" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="w-3 h-3" />
              </Link>
            </td>
          </tr>
          <tr v-if="ofertas.data.length === 0">
            <td class="px-3 py-12 text-center text-gray-400" colspan="7">
              <div class="flex flex-col items-center">
                <icon name="zapytania" class="mb-2 w-12 h-12 opacity-20" />
                <p class="text-xs">Brak ofert spełniających kryteria.</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-6">
      <pagination :links="ofertas.links" />
    </div>

    <oferta-utrata-modal
      :show="showUtrataModal"
      :oferta-id="utrataOfertaId"
      :powody-utraty="powodyUtraty"
      :waluta="waluta"
      :initial-data="utrataInitialData"
      @close="showUtrataModal = false"
      @saved="showUtrataModal = false"
    />
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
import OfertaUtrataModal from '@/Shared/OfertaUtrataModal.vue'

export default {
  components: {
    OfertaUtrataModal,
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    ofertas: Object,
    stats: Object,
    statusOptions: Array,
    powodyUtraty: { type: Array, default: () => [] },
    waluta: { type: Array, default: () => [] },
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        status: this.filters.status || null,
        trashed: this.filters.trashed,
        field: this.filters.field || null,
        direction: this.filters.direction || null,
      },
      showUtrataModal: false,
      utrataOfertaId: null,
      utrataInitialData: null,
    }
  },
  computed: {
    monthChange() {
      if (!this.stats.prev_month || this.stats.prev_month === 0) return 0
      return Math.round(((this.stats.this_month - this.stats.prev_month) / this.stats.prev_month) * 100)
    },
    topStatuses() {
      if (!this.stats.statuses) return []
      return this.stats.statuses.filter(s => s.count > 0).slice(0, 2)
    },
    utrataFlash() {
      return this.$page.props.flash ? this.$page.props.flash.openUtrataForOferta : null
    },
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/oferta', pickBy(this.form), { preserveState: true })
      }, 150),
    },
    // Po zmianie statusu na przegrana/rezygnacja (tu lub z ekranu edycji)
    // kontroler flashuje id oferty -> otwieramy modal powodu utraty.
    utrataFlash: {
      immediate: true,
      handler(id) {
        if (id) this.openUtrataModal(id)
      },
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
    formatCurrency(value) {
      if (!value) return '0'
      return new Intl.NumberFormat('pl-PL').format(value)
    },
    changeStatus(item, newStatusId) {
      const id = parseInt(newStatusId, 10)
      if (!item.status || item.status.id === id) return
      // Modal powodu utraty otwiera watcher `utrataFlash` (kontroler flashuje id).
      this.$inertia.put(`/oferta/${item.id}/status`, { oferta_status_id: id }, {
        preserveScroll: true,
        preserveState: true,
      })
    },
    openUtrataModal(id) {
      const row = this.ofertas.data.find(o => o.id === id)
      this.utrataOfertaId = id
      this.utrataInitialData = row ? row.utrataDetail : null
      this.showUtrataModal = true
    },
  },
}
</script>

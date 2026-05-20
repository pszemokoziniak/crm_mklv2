<template>
  <div>
    <Head title="Panel główny" />
    <div class="mb-8">
      <div class="sm:hidden">
        <label for="tabs" class="sr-only">Wybierz zakładkę</label>
        <select id="tabs" v-model="activeTab" name="tabs" class="block w-full focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md" @change="updateTabUrl">
          <option value="todo">Do zrobienia</option>
          <option value="activity">Aktywność systemowa</option>
        </select>
      </div>
      <div class="hidden sm:block">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button
              type="button"
              :class="[activeTab === 'todo' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']"
              @click="setTab('todo')"
            >
              Do zrobienia
            </button>
            <button
              type="button"
              :class="[activeTab === 'activity' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200']"
              @click="setTab('activity')"
            >
              Aktywność systemowa
            </button>
          </nav>
        </div>
      </div>
    </div>

    <div v-show="activeTab === 'todo'">
      <!-- Karty statystyk -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Nowe zapytania -->
        <div class="bg-white rounded-lg shadow p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-500">Nowe zapytania</p>
              <div class="flex items-baseline">
                <p class="text-2xl font-bold text-gray-900">{{ stats.zapytania.current }}</p>
                <span v-if="stats.zapytania.previous > 0" class="ml-2 text-xs font-semibold" :class="percentChange(stats.zapytania.current, stats.zapytania.previous) >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ percentChange(stats.zapytania.current, stats.zapytania.previous) >= 0 ? '↑' : '↓' }} {{ Math.abs(percentChange(stats.zapytania.current, stats.zapytania.previous)) }}%
                </span>
                <span v-else class="ml-2 text-xs text-gray-400">—</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Nowe oferty -->
        <div class="bg-white rounded-lg shadow p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-500">Nowe oferty</p>
              <div class="flex items-baseline">
                <p class="text-2xl font-bold text-gray-900">{{ stats.oferty.current }}</p>
                <span v-if="stats.oferty.previous > 0" class="ml-2 text-xs font-semibold" :class="percentChange(stats.oferty.current, stats.oferty.previous) >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ percentChange(stats.oferty.current, stats.oferty.previous) >= 0 ? '↑' : '↓' }} {{ Math.abs(percentChange(stats.oferty.current, stats.oferty.previous)) }}%
                </span>
                <span v-else class="ml-2 text-xs text-gray-400">—</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Nowi klienci -->
        <div class="bg-white rounded-lg shadow p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-500">Nowi klienci</p>
              <div class="flex items-baseline">
                <p class="text-2xl font-bold text-gray-900">{{ stats.klienci.current }}</p>
                <span v-if="stats.klienci.previous > 0" class="ml-2 text-xs font-semibold" :class="percentChange(stats.klienci.current, stats.klienci.previous) >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ percentChange(stats.klienci.current, stats.klienci.previous) >= 0 ? '↑' : '↓' }} {{ Math.abs(percentChange(stats.klienci.current, stats.klienci.previous)) }}%
                </span>
                <span v-else class="ml-2 text-xs text-gray-400">—</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Wartość ofert -->
        <div class="bg-white rounded-lg shadow p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="ml-4 flex-1">
              <p class="text-sm font-medium text-gray-500">Wartość ofert</p>
              <div class="flex items-baseline">
                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(stats.wartoscOfert.current) }}</p>
                <span v-if="stats.wartoscOfert.previous > 0" class="ml-2 text-xs font-semibold" :class="percentChange(stats.wartoscOfert.current, stats.wartoscOfert.previous) >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ percentChange(stats.wartoscOfert.current, stats.wartoscOfert.previous) >= 0 ? '↑' : '↓' }} {{ Math.abs(percentChange(stats.wartoscOfert.current, stats.wartoscOfert.previous)) }}%
                </span>
                <span v-else class="ml-2 text-xs text-gray-400">—</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 space-y-4 md:space-y-0">
        <h1 class="text-3xl font-extrabold text-gray-900">Do zrobienia</h1>
        <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4 w-full max-w-2xl">
          <div v-if="users && users.length > 0" class="flex w-full sm:w-64 bg-white rounded shadow">
            <select v-model="form.user_id" class="relative w-full px-4 py-3 rounded focus:shadow-outline border-none text-sm">
              <option :value="null">Wszyscy użytkownicy</option>
              <option v-for="user in users" :key="user.id" :value="user.id">
                {{ user.first_name }} {{ user.last_name }}
              </option>
            </select>
          </div>
          <search-filter-simple v-model="form.search" class="w-full" @reset="reset" />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Zapytania -->
        <div class="flex flex-col">
          <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-lg font-bold text-indigo-700 uppercase tracking-wider">Zapytania</h2>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ zapytanias.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="item in zapytanias" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
              <Link :href="item.link || `/zapytania/${item.id}/edit`" class="block p-4">
                <div v-if="item.wznowienie===2" class="mb-2 flex items-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Wznowienie</span>
                  <Link :href="`/oferta/create?zapytania_id=${item.original_zapytanie_id || item.id}&wznowienie_id=${item.wznowienie_id || ''}&wznowienie=true`" class="ml-2 inline-flex items-center px-2.5 py-0.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Stwórz ofertę
                  </Link>
                </div>
                <div class="text-xs font-mono text-gray-500 mb-1">{{ item.id_zapyt }}</div>
                <div class="font-bold text-gray-900 mb-1 truncate pb-0.5">{{ item.nazwa_projektu }}</div>
                <div class="text-sm text-gray-600 mb-3">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>

                <div class="flex flex-col space-y-2 mt-auto pt-3 border-t border-gray-50">
                  <span class="uppercase text-gray-400 font-bold leading-none mb-1" style="font-size: 9px;">Data złożenia</span>
                  <div class="text-xs flex items-center" :class="{'text-red-500': isOverdue(item.data_zlozenia), 'text-gray-500': !isOverdue(item.data_zlozenia)}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    {{ item.data_zlozenia || 'Brak daty złożenia' }}
                  </div>
                  <div v-if="item.opracowuje" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                    {{ item.opracowuje.first_name }} {{ item.opracowuje.last_name }}
                  </div>
                  <div v-else class="text-xs text-gray-400 italic">Nieprzypisane</div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Oferty -->
        <div class="flex flex-col">
          <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-lg font-bold text-green-700 uppercase tracking-wider">Oferty</h2>
            <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ ofertas.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="item in ofertas" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
              <Link :href="`/oferta/${item.id}/edit`" class="block p-4">
                <div class="flex items-center justify-between mb-1">
                  <div class="font-bold text-gray-900 truncate pb-0.5">{{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak projektu' }}</div>
                  <div v-if="item.kontakt_blisko || item.kontakt_przeterminowany" class="ml-2 flex-shrink-0" :title="item.kontakt_przeterminowany ? 'Kontakt przeterminowany!' : 'Kontakt w ciągu 10 dni'">
                    <svg class="w-5 h-5" :class="item.kontakt_przeterminowany ? 'text-yellow-500 animate-bell-ring' : 'text-yellow-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                  </div>
                </div>
                <div class="text-sm text-gray-600 mb-1">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>
                <div v-if="item.status" class="mb-3">
                  <span class="inline-flex">
                    {{ item.status }}
                  </span>
                </div>

                <div class="flex flex-col space-y-2 mt-auto pt-3 border-t border-gray-50">
                  <div v-if="item.data_kontakt" class="flex flex-col">
                    <span class="text-[9px] uppercase text-indigo-500 font-bold leading-none mb-1">Data kontaktu</span>
                    <div class="text-xs font-bold flex items-center" :class="{'text-red-500': isOverdue(item.data_kontakt), 'text-indigo-700': !isOverdue(item.data_kontakt)}">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                      {{ item.data_kontakt }}
                    </div>
                  </div>
                  <div class="flex flex-col">
                    <span class="uppercase text-gray-400 font-bold leading-none mb-1" style="font-size: 9px;">Wysłano</span>
                    <div class="text-xs flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                      {{ item.data_wyslania || 'Brak daty' }}
                    </div>
                  </div>
                  <div v-if="item.user" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                    {{ item.user.first_name }} {{ item.user.last_name }}
                  </div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Klienci kontakty -->
        <div class="flex flex-col">
          <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-lg font-bold text-blue-700 uppercase tracking-wider">Kontakty</h2>
            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ kontakts.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="item in kontakts" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
              <Link :href="`/kontakt/${item.thread_root_id || item.id}/edit`" class="block p-4">
                <div class="font-bold text-gray-900 mb-1 truncate">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>
                <div v-if="item.kontaktperson" class="text-sm text-gray-700 font-medium mb-1">
                  {{ item.kontaktperson.first_name }} {{ item.kontaktperson.last_name }}
                </div>
                <div class="text-sm text-gray-600 mb-3 line-clamp-2 italic">"{{ item.subject }}"</div>

                <div class="flex flex-col space-y-3 mt-auto pt-3 border-t border-gray-50">
                  <!-- Następny kontakt -->
                  <div v-if="item.next_call_date">
                    <span class="uppercase text-gray-400 font-bold leading-none mb-1" style="font-size: 9px;">Termin
                      kontaktu</span>
                    <div class="text-xs flex items-center" :class="{'text-red-500': isOverdue(item.next_call_date), 'text-indigo-700': !isOverdue(item.next_call_date)}">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                      <span class="whitespace-nowrap">{{ item.next_call_date }}</span>
                    </div>
                    <div v-if="item.next_call_time" class="flex items-center">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                      <span class="bg-indigo-200 text-indigo-800 px-1 py-0.5 rounded text-[9px]">{{ item.next_call_time }}</span>
                    </div>
                  </div>

                  <!-- Ostatni i Opiekun -->
                  <div class="space-y-2">
                    <div class="flex flex-col">
                      <span class="uppercase text-gray-400 font-bold leading-none mb-1" style="font-size: 9px;">Ostatni kontakt</span>
                      <span class="text-xs whitespace-nowrap text-gray-500">{{ item.call_date }}</span>
                    </div>
                    <div v-if="item.user" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                      {{ item.user.first_name }} {{ item.user.last_name }}
                    </div>
                    <div v-else class="text-xs text-gray-400 italic">Nieprzypisane</div>
                  </div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Zadania -->
        <div class="flex flex-col">
          <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-lg font-bold text-orange-700 uppercase tracking-wider">Zadania</h2>
            <span class="bg-orange-100 text-orange-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ zadania.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="item in zadania" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
              <Link :href="`/zadania/${item.id}/edit`" class="block p-4">
                <div class="flex items-center justify-between mb-1">
                  <div class="font-bold text-gray-900 truncate">{{ item.subject }}</div>
                  <span v-if="item.status === 'do_akceptacji'" class="ml-2 flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                    Do akceptacji
                  </span>
                </div>

                <div class="flex flex-col space-y-2 mt-auto pt-3 border-t border-gray-50">
                  <div class="text-xs flex items-center" :class="{'text-red-500': isOverdue(item.deadline), 'text-gray-500': !isOverdue(item.deadline)}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ item.deadline || 'Brak terminu' }}
                  </div>
                  <div v-if="item.users" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                    {{ item.users.first_name }} {{ item.users.last_name }}
                  </div>
                  <div v-else class="text-xs text-gray-400 italic">Nieprzypisane</div>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-show="activeTab === 'activity'">
      <Historia v-if="historia" :historia="historia" />
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import Historia from '@/Pages/ActivityLog/Index.vue'
import SearchFilterSimple from '@/Shared/SearchFilterSimple.vue'
import throttle from 'lodash/throttle'
// Removed pickBy as we'll construct params manually
// import pickBy from 'lodash/pickBy'
// Removed mapValues as we'll reset fields explicitly
// import mapValues from 'lodash/mapValues'

export default {
  components: {
    SearchFilterSimple,
    Head,
    Link,
    Historia,
  },
  layout: Layout,
  props: {
    stats: Object,
    kontakts: Array,
    zapytanias: Array,
    ofertas: Array,
    futureProjects: Array,
    zadania: Array,
    historia: Object,
    filters: Object,
    users: Array,
  },
  data() {
    return {
      activeTab: this.filters.tab || 'todo',
      form: {
        search: this.filters.search || '', // Ensure search is an empty string if null
        user_id: this.filters.user_id,
        tab: this.filters.tab || 'todo',
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        const params = {}
        // Always include search, even if empty, so backend can filter
        if (this.form.search !== null && this.form.search !== undefined) {
          params.search = this.form.search
        }
        // Include user_id only if it has a value
        if (this.form.user_id !== null && this.form.user_id !== undefined) {
          params.user_id = this.form.user_id
        }
        // Always include tab
        params.tab = this.form.tab

        this.$inertia.get('/', params, { preserveState: true })
      }, 150),
    },
  },
  methods: {
    setTab(tab) {
      this.activeTab = tab
      this.form.tab = tab
    },
    updateTabUrl() {
      this.form.tab = this.activeTab
    },
    reset() {
      this.form.search = '' // Explicitly set search to empty string
      this.form.user_id = null // Explicitly set user_id to null
      this.form.tab = this.activeTab // Keep the current active tab
    },
    statusClasses(status) {
      const s = status.toLowerCase()
      if (s.includes('wygrana') || s.includes('przyjęta') || s.includes('realizacja')) return 'bg-green-100 text-green-800'
      if (s.includes('przegrana') || s.includes('odrzucona') || s.includes('rezygnacja')) return 'bg-red-100 text-red-800'
      if (s.includes('toczy') || s.includes('wysłana') || s.includes('oczekuje')) return 'bg-blue-100 text-blue-800'
      return 'bg-gray-100 text-gray-800'
    },
    percentChange(current, previous) {
      if (previous === 0) return 0
      return Math.round(((current - previous) / previous) * 100)
    },
    formatCurrency(value) {
      if (value === null || value === undefined) return '0 PLN'
      return new Intl.NumberFormat('pl-PL', { maximumFractionDigits: 0 }).format(value) + ' PLN'
    },
    isOverdue(dateString) {
      if (!dateString) return false
      const today = new Date()
      today.setHours(0, 0, 0, 0) // Ignore time for comparison

      const itemDate = new Date(dateString)
      itemDate.setHours(0, 0, 0, 0) // Ignore time for comparison

      return itemDate < today
    },
  },
}
</script>

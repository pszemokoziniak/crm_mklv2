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

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <!-- Zapytania -->
        <div class="flex flex-col">
          <div class="flex items-center justify-between mb-4 px-2">
            <h2 class="text-lg font-bold text-indigo-700 uppercase tracking-wider">Zapytania</h2>
            <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ zapytanias.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="item in zapytanias" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
              <Link :href="`/zapytania/${item.id}/edit`" class="block p-4">
                <div v-if="item.wznowienie===2" class="mb-2">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Wznowienie</span>
                </div>
                <div class="text-xs font-mono text-gray-500 mb-1">{{ item.id_zapyt }}</div>
                <div class="font-bold text-gray-900 mb-1 truncate">{{ item.nazwa_projektu }}</div>
                <div class="text-sm text-gray-600 mb-3">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>

                <div class="flex flex-col space-y-2 mt-auto pt-3 border-t border-gray-50">
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
                <div class="font-bold text-gray-900 mb-1 truncate">{{ item.zapytania ? item.zapytania.nazwa_projektu : 'Brak projektu' }}</div>
                <div class="text-sm text-gray-600 mb-1">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>
                <div v-if="item.status" class="mb-3">
                  <span :class="statusClasses(item.status)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider">
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
                    <div class="text-xs flex items-center" :class="{'text-red-500': isOverdue(item.data_wyslania), 'text-gray-500': !isOverdue(item.data_wyslania)}">
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
              <Link :href="`/kontakt/${item.id}/edit`" class="block p-4">
                <div class="font-bold text-gray-900 mb-1 truncate">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>
                <div v-if="item.kontaktperson" class="text-sm text-gray-700 font-medium mb-1">
                  {{ item.kontaktperson.first_name }} {{ item.kontaktperson.last_name }}
                </div>
                <div class="text-sm text-gray-600 mb-3 line-clamp-2 italic">"{{ item.subject }}"</div>

                <div class="flex flex-col space-y-3 mt-auto pt-3 border-t border-gray-50">
                  <!-- Następny kontakt -->
                  <div v-if="item.next_call_date">
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
                <div class="font-bold text-gray-900 mb-1 truncate">{{ item.subject }}</div>

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
import pickBy from 'lodash/pickBy'
import mapValues from 'lodash/mapValues'

export default {
  components: {
    SearchFilterSimple,
    Head,
    Link,
    Historia,
  },
  layout: Layout,
  props: {
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
        search: this.filters.search,
        user_id: this.filters.user_id,
        tab: this.filters.tab || 'todo',
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/', pickBy(this.form), { preserveState: true })
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
      this.form = mapValues(this.form, () => null)
      this.form.tab = this.activeTab
    },
    statusClasses(status) {
      const s = status.toLowerCase()
      if (s.includes('wygrana') || s.includes('przyjęta') || s.includes('realizacja')) return 'bg-green-100 text-green-800'
      if (s.includes('przegrana') || s.includes('odrzucona') || s.includes('rezygnacja')) return 'bg-red-100 text-red-800'
      if (s.includes('toczy') || s.includes('wysłana') || s.includes('oczekuje')) return 'bg-blue-100 text-blue-800'
      return 'bg-gray-100 text-gray-800'
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

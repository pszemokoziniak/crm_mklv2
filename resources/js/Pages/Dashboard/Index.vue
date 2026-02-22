<template>
  <div>
    <Head title="Panel główny" />
    <Historia v-if="historia" :historia="historia" />
    <hr class="my-8 border-gray-200" />

    <div class="flex items-center justify-between mb-8">
      <h1 class="text-3xl font-extrabold text-gray-900">Do zrobienia</h1>
      <search-filter-simple v-model="form.search" class="w-full max-w-md" @reset="reset" />
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
                <div class="text-xs text-gray-500 flex items-center">
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
                <div class="flex flex-col">
                  <span class="text-[9px] uppercase text-gray-400 font-bold leading-none mb-1">Wysłano</span>
                  <div class="text-xs text-gray-500 flex items-center">
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
                <!-- Następny kontakt - Wyróżniony -->
                <div v-if="item.next_call_date" class="bg-indigo-50 p-2 rounded-md border border-indigo-100">
                  <div class="text-[9px] uppercase font-bold text-indigo-400 mb-0.5">Następny:</div>
                  <div class="flex items-center text-indigo-700 font-bold text-xs">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="whitespace-nowrap">{{ item.next_call_date }}</span>
                    <span v-if="item.next_call_time" class="ml-2 bg-indigo-200 text-indigo-800 px-1 py-0.5 rounded text-[9px]">{{ item.next_call_time }}</span>
                  </div>
                </div>

                <!-- Ostatni i Opiekun - Każdy w nowej linii z małym fontem -->
                <div class="space-y-2">
                  <div class="flex flex-col">
                    <span class="text-[9px] uppercase text-gray-400 font-bold leading-none mb-1">Ostatni kontakt</span>
                    <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ item.call_date }}</span>
                  </div>
                  <div v-if="item.user" class="flex flex-col">
                    <span class="text-[9px] uppercase text-gray-400 font-bold leading-none mb-1">Opiekun</span>
                    <span class="text-[10px] text-gray-500 truncate">{{ item.user.first_name }} {{ item.user.last_name }}</span>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>
      </div>

      <!-- Przyszłe projekty -->
      <div class="flex flex-col">
        <div class="flex items-center justify-between mb-4 px-2">
          <h2 class="text-lg font-bold text-purple-700 uppercase tracking-wider">Projekty</h2>
          <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ futureProjects.length }}</span>
        </div>
        <div class="space-y-3">
          <div v-for="item in futureProjects" :key="item.id" class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
            <Link :href="`/futureproject/${item.id}/edit`" class="block p-4">
              <div class="font-bold text-gray-900 mb-1 truncate">{{ item.nazwa }}</div>
              <div class="text-sm text-gray-600 mb-1">{{ item.client ? item.client.nazwa : 'Brak klienta' }}</div>
              <div v-if="item.faza" class="mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs uppercase tracking-wider bg-purple-100 text-purple-800">
                  {{ item.faza }}
                </span>
              </div>

              <div class="flex flex-col space-y-2 mt-auto pt-3 border-t border-gray-50">
                <div class="text-xs text-gray-500 flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                  {{ item.data_kontakt || 'Brak daty' }}
                </div>
                <div v-if="item.user" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                  {{ item.user.first_name }} {{ item.user.last_name }}
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
                <div class="text-xs text-gray-500 flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  {{ item.deadline || 'Brak terminu' }}
                </div>
                <div v-if="item.users" class="text-xs font-medium text-gray-600 bg-gray-100 px-2 py-1 rounded self-start">
                  {{ item.users.first_name }} {{ item.users.last_name }}
                </div>
              </div>
            </Link>
          </div>
        </div>
      </div>
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
  },
  data() {
    return {
      form: {
        search: this.filters.search,
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
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    statusClasses(status) {
      const s = status.toLowerCase()
      if (s.includes('wygrana') || s.includes('przyjęta') || s.includes('realizacja')) return 'bg-green-100 text-green-800'
      if (s.includes('przegrana') || s.includes('odrzucona') || s.includes('rezygnacja')) return 'bg-red-100 text-red-800'
      if (s.includes('toczy') || s.includes('wysłana') || s.includes('oczekuje')) return 'bg-blue-100 text-blue-800'
      return 'bg-gray-100 text-gray-800'
    },
  },
}
</script>

<template>
  <div>
    <Head title="Terminy" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
      <div class="flex items-center gap-3">
        <h1 class="text-3xl font-bold text-gray-900">Terminy</h1>
        <span class="text-sm text-gray-400 hidden md:inline">panoramiczny widok wszystkich deadlinów</span>
      </div>

      <!-- Nawigacja miesiacami -->
      <div class="flex items-center gap-2">
        <Link :href="`/terminy?month=${prevMonth}${userQuery}`" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">← Poprzedni</Link>
        <div class="px-4 py-1.5 bg-indigo-600 text-white rounded-lg font-bold text-sm capitalize min-w-[180px] text-center">
          {{ viewMonthLabel }}
        </div>
        <Link :href="`/terminy?month=${nextMonth}${userQuery}`" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">Następny →</Link>
        <Link v-if="viewMonth !== today" :href="`/terminy?month=${today}${userQuery}`" class="px-3 py-1.5 border border-indigo-200 rounded-lg text-sm font-medium text-indigo-600 hover:bg-indigo-50">Dziś</Link>
      </div>
    </div>

    <!-- Filtr per user + statystyki -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
      <div v-if="users && users.length > 0" class="flex items-center gap-2">
        <label class="text-xs font-semibold text-gray-500 uppercase">Osoba:</label>
        <select v-model="selectedUser" class="form-select text-sm border-gray-200 rounded-md" @change="onUserChange">
          <option :value="null">Wszyscy użytkownicy</option>
          <option v-for="u in users" :key="u.id" :value="u.id">
            {{ u.last_name }} {{ u.first_name }}
          </option>
        </select>
      </div>

      <div class="grid grid-cols-4 gap-2 flex-1 max-w-2xl lg:ml-auto">
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
          <span class="w-3 h-3 rounded-full bg-indigo-500 flex-shrink-0" />
          <div class="min-w-0">
            <div class="text-[10px] uppercase font-bold text-gray-500 truncate">Zapytania</div>
            <div class="text-lg font-bold text-indigo-600">{{ stats.zapytania }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
          <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0" />
          <div class="min-w-0">
            <div class="text-[10px] uppercase font-bold text-gray-500 truncate">Oferty</div>
            <div class="text-lg font-bold text-green-600">{{ stats.oferty }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
          <span class="w-3 h-3 rounded-full bg-blue-500 flex-shrink-0" />
          <div class="min-w-0">
            <div class="text-[10px] uppercase font-bold text-gray-500 truncate">Kontakty</div>
            <div class="text-lg font-bold text-blue-600">{{ stats.kontakty }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
          <span class="w-3 h-3 rounded-full bg-orange-500 flex-shrink-0" />
          <div class="min-w-0">
            <div class="text-[10px] uppercase font-bold text-gray-500 truncate">Zadania</div>
            <div class="text-lg font-bold text-orange-600">{{ stats.zadania }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Kalendarz -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <!-- Naglowki dni tygodnia -->
      <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50/50">
        <div v-for="d in ['Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'Sb', 'Nd']" :key="d" class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-500 text-center">{{ d }}</div>
      </div>

      <!-- Dni -->
      <div class="grid grid-cols-7">
        <div
          v-for="(day, idx) in days"
          :key="idx"
          class="min-h-[110px] p-1.5 border-r border-b border-gray-100 relative"
          :class="[
            !day.inCurrentMonth ? 'bg-gray-50/40' : 'bg-white',
            day.isWeekend && day.inCurrentMonth ? 'bg-gray-50/60' : '',
            day.isToday ? 'ring-2 ring-inset ring-indigo-400' : '',
          ]"
        >
          <div class="flex items-center justify-between mb-1">
            <span
              class="text-xs font-semibold"
              :class="[
                !day.inCurrentMonth ? 'text-gray-300' : 'text-gray-700',
                day.isToday ? 'bg-indigo-600 text-white px-1.5 py-0.5 rounded' : '',
              ]"
            >{{ day.day }}</span>
            <span v-if="day.events.length" class="text-[9px] text-gray-400">{{ day.events.length }}</span>
          </div>
          <div class="space-y-1">
            <Link
              v-for="(ev, i) in day.events.slice(0, 3)"
              :key="i"
              :href="ev.link"
              class="block px-1.5 py-1 rounded text-[10px] leading-tight truncate transition-colors border-l-2"
              :class="colorClasses(ev.color)"
              :title="`${ev.label}: ${ev.title}\n${ev.subtitle || ''}\n${ev.assignee ? 'Opiekun: ' + ev.assignee : ''}`"
            >
              <span class="font-bold">{{ eventTypeShort(ev.type) }}</span>
              <span v-if="ev.time" class="ml-1 text-gray-500">{{ ev.time.slice(0, 5) }}</span>
              <span class="ml-1">{{ ev.title }}</span>
            </Link>
            <button
              v-if="day.events.length > 3"
              type="button"
              class="w-full text-left text-[9px] text-indigo-600 hover:underline px-1.5"
              @click="showDay = day"
            >
              +{{ day.events.length - 3 }} więcej
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: wszystkie zdarzenia z dnia -->
    <div v-if="showDay" class="fixed inset-0 flex items-start justify-center p-4 md:pt-24 bg-black bg-opacity-50" style="z-index:100000" @click="showDay = null">
      <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col" @click.stop>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
          <div>
            <h3 class="text-lg font-bold text-gray-800">{{ formatDayLabel(showDay.date) }}</h3>
            <p class="text-xs text-gray-500">{{ showDay.events.length }} zdarzeń</p>
          </div>
          <button type="button" class="text-gray-400 hover:text-gray-600 text-2xl leading-none" @click="showDay = null">×</button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
          <Link
            v-for="(ev, i) in showDay.events"
            :key="i"
            :href="ev.link"
            class="block p-3 rounded-lg border-l-4 border transition-colors"
            :class="colorClassesFull(ev.color)"
            @click="showDay = null"
          >
            <div class="flex items-center justify-between mb-1">
              <span class="text-[9px] font-bold uppercase tracking-wider">{{ ev.label }}</span>
              <span v-if="ev.time" class="text-xs text-gray-500">{{ ev.time.slice(0, 5) }}</span>
            </div>
            <div class="text-sm font-bold text-gray-800 truncate">{{ ev.title }}</div>
            <div v-if="ev.subtitle" class="text-xs text-gray-600 truncate">{{ ev.subtitle }}</div>
            <div v-if="ev.assignee" class="text-[10px] text-gray-500 mt-1">Opiekun: {{ ev.assignee }}</div>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'

export default {
  components: { Head, Link },
  layout: Layout,
  props: {
    viewMonth: String,
    viewMonthLabel: String,
    prevMonth: String,
    nextMonth: String,
    today: String,
    days: Array,
    stats: Object,
    users: Array,
    selectedUserId: Number,
  },
  data() {
    return {
      showDay: null,
      selectedUser: this.selectedUserId,
    }
  },
  computed: {
    userQuery() {
      return this.selectedUser ? `&user_id=${this.selectedUser}` : ''
    },
  },
  methods: {
    onUserChange() {
      this.$inertia.get('/terminy', { month: this.viewMonth, user_id: this.selectedUser || undefined }, { preserveState: true })
    },
    colorClasses(color) {
      return ({
        indigo: 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border-indigo-400',
        green: 'bg-green-50 text-green-800 hover:bg-green-100 border-green-400',
        blue: 'bg-blue-50 text-blue-800 hover:bg-blue-100 border-blue-400',
        orange: 'bg-orange-50 text-orange-800 hover:bg-orange-100 border-orange-400',
      })[color] || 'bg-gray-50 text-gray-800 hover:bg-gray-100 border-gray-300'
    },
    colorClassesFull(color) {
      return ({
        indigo: 'bg-indigo-50 border-indigo-400 hover:bg-indigo-100',
        green: 'bg-green-50 border-green-400 hover:bg-green-100',
        blue: 'bg-blue-50 border-blue-400 hover:bg-blue-100',
        orange: 'bg-orange-50 border-orange-400 hover:bg-orange-100',
      })[color] || 'bg-gray-50 border-gray-300 hover:bg-gray-100'
    },
    eventTypeShort(type) {
      return ({
        zapytanie: 'ZAP',
        oferta: 'OF',
        kontakt: 'KON',
        zadanie: 'ZAD',
      })[type] || '?'
    },
    formatDayLabel(iso) {
      const d = new Date(iso + 'T00:00:00')
      return d.toLocaleDateString('pl-PL', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
    },
  },
}
</script>

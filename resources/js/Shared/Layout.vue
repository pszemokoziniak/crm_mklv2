<template>
  <div>
    <div id="dropdown" />
    <div class="md:flex md:flex-col">
      <div class="md:flex md:flex-col md:h-screen">
        <div class="md:flex md:flex-shrink-0">
          <div class="flex items-center justify-between px-6 py-4 bg-white md:flex-shrink-0 md:justify-center md:w-64 transition-all duration-300">
            <Link class="mt-1" href="/">
              <logo class="fill-white" />
            </Link>
            <dropdown class="md:hidden" placement="bottom-end">
              <template #default>
                <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              </template>
              <template #dropdown>
                <div class="mt-2 px-8 py-4 bg-indigo-800 rounded shadow-lg">
                  <main-menu :main-menus="mainMenus" />
                </div>
              </template>
            </dropdown>
          </div>
          <div class="md:text-md flex items-center justify-between p-4 w-full text-sm bg-white border-b md:px-12 md:py-0 shadow-sm">
            <div class="mr-4 mt-1 flex items-center gap-4">
              <div class="font-bold text-indigo-600 uppercase tracking-wider">
                {{ auth.user.roles[0] || 'Użytkownik' }}
              </div>
              <div v-if="imieninyToday.length > 0" class="hidden md:flex items-center gap-1.5 text-xs" :title="imieninyTitle">
                <span class="text-base leading-none">🎂</span>
                <span class="text-gray-500 font-medium">Imieniny:</span>
                <span class="flex items-center gap-1 flex-wrap">
                  <template v-for="(name, idx) in imieninyToday" :key="idx">
                    <span
                      :class="matchesEmployee(name)
                        ? 'text-pink-600 font-bold underline decoration-pink-300 decoration-2 underline-offset-2'
                        : 'text-gray-700 font-semibold'"
                    >{{ name }}</span><span v-if="idx < imieninyToday.length - 1" class="text-gray-400">,</span>
                  </template>
                </span>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <dropdown v-if="myTodo && myTodo.total > 0" class="mt-1" placement="bottom-end">
                <template #default>
                  <div class="group flex items-center gap-1.5 cursor-pointer select-none px-2.5 py-1.5 rounded-lg hover:bg-gray-50 transition-colors" :title="todoTitle">
                    <span class="text-base leading-none">{{ todoIcon }}</span>
                    <span class="text-xs font-semibold" :class="todoColorClass">{{ myTodo.total }}</span>
                  </div>
                </template>
                <template #dropdown>
                  <div class="mt-2 py-2 text-sm bg-white rounded-lg shadow-xl border border-gray-100 min-w-[280px]">
                    <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 flex items-center gap-2">
                      <span class="text-sm">{{ todoIcon }}</span>
                      <span>{{ todoHeadline }}</span>
                    </div>
                    <button type="button" :disabled="myTodo.zapytania === 0" class="w-full flex items-center justify-between px-4 py-2 hover:bg-indigo-50 transition-colors disabled:cursor-default disabled:hover:bg-transparent" @click="openTodoModal('zapytania')">
                      <span class="flex items-center gap-2 text-xs text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500" />
                        Zapytania (termin złożenia)
                      </span>
                      <span class="text-xs font-bold" :class="myTodo.zapytania > 0 ? 'text-indigo-600' : 'text-gray-300'">{{ myTodo.zapytania }}</span>
                    </button>
                    <button type="button" :disabled="myTodo.oferty === 0" class="w-full flex items-center justify-between px-4 py-2 hover:bg-indigo-50 transition-colors disabled:cursor-default disabled:hover:bg-transparent" @click="openTodoModal('oferty')">
                      <span class="flex items-center gap-2 text-xs text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500" />
                        Oferty (data kontaktu)
                      </span>
                      <span class="text-xs font-bold" :class="myTodo.oferty > 0 ? 'text-green-600' : 'text-gray-300'">{{ myTodo.oferty }}</span>
                    </button>
                    <button type="button" :disabled="myTodo.kontakty === 0" class="w-full flex items-center justify-between px-4 py-2 hover:bg-indigo-50 transition-colors disabled:cursor-default disabled:hover:bg-transparent" @click="openTodoModal('kontakty')">
                      <span class="flex items-center gap-2 text-xs text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500" />
                        Kontakty (termin)
                      </span>
                      <span class="text-xs font-bold" :class="myTodo.kontakty > 0 ? 'text-blue-600' : 'text-gray-300'">{{ myTodo.kontakty }}</span>
                    </button>
                    <button type="button" :disabled="myTodo.zadania === 0" class="w-full flex items-center justify-between px-4 py-2 hover:bg-indigo-50 transition-colors disabled:cursor-default disabled:hover:bg-transparent" @click="openTodoModal('zadania')">
                      <span class="flex items-center gap-2 text-xs text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500" />
                        Zadania (deadline)
                      </span>
                      <span class="text-xs font-bold" :class="myTodo.zadania > 0 ? 'text-orange-600' : 'text-gray-300'">{{ myTodo.zadania }}</span>
                    </button>
                    <div class="border-t border-gray-100 my-1" />
                    <Link href="/" class="block px-4 py-2 text-[10px] text-center text-indigo-600 hover:bg-indigo-50 font-semibold transition-colors">
                      Zobacz zestawienie na dashboardzie →
                    </Link>
                  </div>
                </template>
              </dropdown>
              <div v-else-if="myTodo && myTodo.total === 0" class="flex items-center gap-1 px-2.5 py-1.5 select-none" title="Nic pilnego na horyzoncie — wolne! 🍹">
                <span class="text-base leading-none">🍹</span>
              </div>
              <dropdown v-if="isAdmin && onlineUsers && onlineUsers.length > 0" class="mt-1" placement="bottom-end">
                <template #default>
                  <div class="group flex items-center gap-1.5 cursor-pointer select-none px-2.5 py-1.5 rounded-lg hover:bg-gray-50 transition-colors" :title="onlineCount > 0 ? `${onlineCount} zalogowanych` : 'Nikt nie jest teraz zalogowany'">
                    <span v-if="onlineCount > 0" class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75" />
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500" />
                    </span>
                    <span v-else class="flex-shrink-0 w-2 h-2 rounded-full bg-gray-300" />
                    <span class="text-xs font-semibold" :class="onlineCount > 0 ? 'text-gray-700 group-hover:text-indigo-600' : 'text-gray-500'">
                      {{ onlineCount }} online
                    </span>
                  </div>
                </template>
                <template #dropdown>
                  <div class="mt-2 py-2 text-sm bg-white rounded-lg shadow-xl border border-gray-100 min-w-[260px] max-h-96 overflow-y-auto">
                    <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100">Aktywność (ostatnie 7 dni)</div>
                    <div v-for="u in onlineUsers" :key="u.id" class="flex items-center px-4 py-2 hover:bg-indigo-50 transition-colors">
                      <span class="flex-shrink-0 w-2 h-2 rounded-full mr-3" :class="u.is_logged_in ? 'bg-green-500' : 'bg-gray-300'" />
                      <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-gray-800 truncate flex items-center gap-1.5">
                          <span class="truncate">{{ u.first_name }} {{ u.last_name }}</span>
                          <span v-if="u.is_logged_in" class="flex-shrink-0 text-[8px] font-bold uppercase tracking-wide text-green-700 bg-green-50 px-1.5 py-0.5 rounded">online</span>
                        </div>
                        <div class="text-[10px] text-gray-400 truncate">{{ u.email }}</div>
                      </div>
                      <div class="text-[9px] text-gray-400 ml-2 whitespace-nowrap text-right">
                        <div v-if="u.is_logged_in">zalogowany</div>
                        <div v-else>wylogowany</div>
                        <div>{{ formatRelative(u.is_logged_in ? u.last_login_at : u.last_logout_at) }}</div>
                      </div>
                    </div>
                  </div>
                </template>
              </dropdown>
              <notification-bell :count="unreadNotificationsCount" />
              <dropdown class="mt-1" placement="bottom-end">
                <template #default>
                  <div class="group flex items-center cursor-pointer select-none p-2 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="mr-2 text-gray-700 group-hover:text-indigo-600 focus:text-indigo-600 whitespace-nowrap font-medium">
                      <span>{{ auth.user.first_name }}</span>
                      <span class="hidden md:inline">&nbsp;{{ auth.user.last_name }}</span>
                    </div>
                    <icon class="w-5 h-5 fill-gray-400 group-hover:fill-indigo-600 transition-colors" name="cheveron-down" />
                  </div>
                </template>
                <template #dropdown>
                  <div class="mt-2 py-2 text-sm bg-white rounded-lg shadow-xl border border-gray-100 min-w-[160px]">
                    <Link class="block px-6 py-2 text-gray-700 hover:text-white hover:bg-indigo-600 transition-colors" :href="`/users/${auth.user.id}/edit`">Profil</Link>
                    <Link class="block px-6 py-2 text-gray-700 hover:text-white hover:bg-indigo-600 transition-colors" href="/users">Użytkownicy</Link>
                    <div class="border-t border-gray-100 my-1" />
                    <Link class="block px-6 py-2 w-full text-left text-red-600 hover:text-white hover:bg-red-500 transition-colors" href="/logout" method="delete" as="button">Wyloguj</Link>
                  </div>
                </template>
              </dropdown>
            </div>
          </div>
        </div>
        <div class="md:flex md:flex-grow md:overflow-hidden">
          <main-menu :main-menus="mainMenus" class="hidden flex-shrink-0 px-4 py-8 w-64 bg-indigo-800 overflow-y-auto md:block border-r border-indigo-900" />
          <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto bg-gray-50">
            <flash-messages />
            <slot />
          </div>
        </div>
      </div>
    </div>

    <!-- Modal: podglad "do zrobienia" per kategoria -->
    <div v-if="todoModal && myTodo && myTodo.items" class="fixed inset-0 z-50 flex items-start justify-center p-4 md:p-8 bg-black bg-opacity-50" @click="todoModal = null">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[85vh] flex flex-col overflow-hidden" @click.stop>
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50">
          <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full" :class="todoModalColorClass" />
            <h3 class="text-lg font-bold text-gray-800">{{ todoModalTitle }}</h3>
            <span class="text-sm text-gray-400">({{ myTodo.items[todoModal].length }})</span>
          </div>
          <button type="button" class="text-gray-400 hover:text-gray-600 text-2xl leading-none" @click="todoModal = null">×</button>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="myTodo.items[todoModal].length === 0" class="text-center text-gray-400 py-8">
            Brak pozycji w tej kategorii.
          </div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <!-- ZAPYTANIA -->
            <template v-if="todoModal === 'zapytania'">
              <Link v-for="it in myTodo.items.zapytania" :key="it.id" :href="it.link" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-indigo-300 transition-all p-4" @click="todoModal = null">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[10px] font-mono text-gray-400">{{ it.id_zapyt || '—' }}</span>
                  <span v-if="it.overdue" class="text-[9px] font-bold uppercase text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Zaległe</span>
                </div>
                <div class="font-bold text-sm text-gray-900 mb-1 truncate" :title="it.nazwa_projektu">{{ it.nazwa_projektu || 'Brak nazwy' }}</div>
                <div class="text-xs text-gray-600 mb-3 truncate" :title="it.client">{{ it.client || 'Brak klienta' }}</div>
                <div class="flex flex-col gap-1 pt-2 border-t border-gray-50">
                  <div class="text-[10px] uppercase text-gray-400 font-bold">Data złożenia</div>
                  <div class="text-xs" :class="it.overdue ? 'text-red-600 font-bold' : 'text-gray-700'">{{ it.data_zlozenia || 'Brak daty' }}</div>
                  <div v-if="it.opracowuje" class="text-[10px] text-gray-500 mt-1">Opracowuje: {{ it.opracowuje }}</div>
                </div>
              </Link>
            </template>

            <!-- OFERTY -->
            <template v-else-if="todoModal === 'oferty'">
              <Link v-for="it in myTodo.items.oferty" :key="it.id" :href="it.link" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-green-300 transition-all p-4" @click="todoModal = null">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[10px] font-mono text-gray-400">#{{ it.id }}</span>
                  <span v-if="it.overdue" class="text-[9px] font-bold uppercase text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Zaległe</span>
                </div>
                <div class="font-bold text-sm text-gray-900 mb-1 truncate" :title="it.nazwa_projektu">{{ it.nazwa_projektu || 'Brak projektu' }}</div>
                <div class="text-xs text-gray-600 mb-2 truncate" :title="it.client">{{ it.client || 'Brak klienta' }}</div>
                <div v-if="it.kwota" class="text-sm font-bold text-green-700 mb-2">{{ formatKwota(it.kwota) }} <span class="text-xs">{{ it.waluta || '' }}</span></div>
                <div class="flex flex-col gap-1 pt-2 border-t border-gray-50">
                  <div class="text-[10px] uppercase text-gray-400 font-bold">Data kontaktu</div>
                  <div class="text-xs" :class="it.overdue ? 'text-red-600 font-bold' : 'text-indigo-700 font-bold'">{{ it.data_kontakt || 'Brak daty' }}</div>
                </div>
              </Link>
            </template>

            <!-- KONTAKTY -->
            <template v-else-if="todoModal === 'kontakty'">
              <Link v-for="it in myTodo.items.kontakty" :key="it.id" :href="it.link" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-blue-300 transition-all p-4" @click="todoModal = null">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[10px] font-mono text-gray-400">#{{ it.id }}</span>
                  <span v-if="it.overdue" class="text-[9px] font-bold uppercase text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Zaległe</span>
                </div>
                <div class="font-bold text-sm text-gray-900 mb-1 truncate" :title="it.client">{{ it.client || 'Brak klienta' }}</div>
                <div class="text-xs text-gray-600 italic mb-3 line-clamp-2" :title="it.subject">"{{ it.subject || 'brak tematu' }}"</div>
                <div class="flex flex-col gap-1 pt-2 border-t border-gray-50">
                  <div class="text-[10px] uppercase text-gray-400 font-bold">Termin kontaktu</div>
                  <div class="text-xs" :class="it.overdue ? 'text-red-600 font-bold' : 'text-indigo-700 font-bold'">
                    {{ it.next_call_date || 'Brak daty' }}
                    <span v-if="it.next_call_time" class="ml-1 text-gray-500">{{ it.next_call_time }}</span>
                  </div>
                </div>
              </Link>
            </template>

            <!-- ZADANIA -->
            <template v-else-if="todoModal === 'zadania'">
              <Link v-for="it in myTodo.items.zadania" :key="it.id" :href="it.link" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-orange-300 transition-all p-4" @click="todoModal = null">
                <div class="flex items-start justify-between mb-2">
                  <span class="text-[10px] font-mono text-gray-400">#{{ it.id }}</span>
                  <span v-if="it.overdue" class="text-[9px] font-bold uppercase text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Zaległe</span>
                </div>
                <div class="font-bold text-sm text-gray-900 mb-1" :title="it.subject">{{ it.subject || 'Brak tematu' }}</div>
                <div v-if="it.client" class="text-xs text-gray-600 mb-2 truncate">{{ it.client }}</div>
                <div class="flex flex-col gap-1 pt-2 border-t border-gray-50">
                  <div class="text-[10px] uppercase text-gray-400 font-bold">Termin wykonania</div>
                  <div class="text-xs" :class="it.overdue ? 'text-red-600 font-bold' : (it.deadline ? 'text-orange-700 font-bold' : 'text-gray-400 italic')">
                    {{ it.deadline || 'Brak terminu' }}
                  </div>
                </div>
              </Link>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import Dropdown from '@/Shared/Dropdown'
import MainMenu from '@/Shared/MainMenu'
import FlashMessages from '@/Shared/FlashMessages'
import NotificationBell from '@/Shared/NotificationBell'
import { initPushNotifications } from '@/Shared/PushNotifications'
import { imieninyDnia } from '@/Shared/imieniny'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Link,
    Logo,
    MainMenu,
    NotificationBell,
  },
  props: {
    auth: Object,
    mainMenus: Array,
    unreadNotificationsCount: {
      type: Number,
      default: 0,
    },
    vapidPublicKey: String,
    onlineUsers: {
      type: Array,
      default: () => [],
    },
    myTodo: {
      type: Object,
      default: null,
    },
    userFirstNames: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      todoModal: null, // 'zapytania' | 'oferty' | 'kontakty' | 'zadania' | null
    }
  },
  computed: {
    isAdmin() {
      if (!this.auth || !this.auth.user || !this.auth.user.roles) return false
      return this.auth.user.roles.includes('super-admin') || this.auth.user.roles.includes('Administrator')
    },
    onlineCount() {
      if (!this.onlineUsers) return 0
      return this.onlineUsers.filter(u => u.is_logged_in).length
    },
    todoIcon() {
      const t = this.myTodo ? this.myTodo.total : 0
      if (t === 0) return '🍹'
      if (t <= 3) return '🔨'
      if (t <= 10) return '💪'
      if (t <= 25) return '🐴'
      return '🥵'
    },
    todoColorClass() {
      const t = this.myTodo ? this.myTodo.total : 0
      if (t <= 3) return 'text-gray-700'
      if (t <= 10) return 'text-indigo-600'
      if (t <= 25) return 'text-orange-600'
      return 'text-red-600'
    },
    todoHeadline() {
      const t = this.myTodo ? this.myTodo.total : 0
      if (t === 0) return 'Nic pilnego — chill 🍹'
      if (t <= 3) return 'Kilka drobiazgów do zrobienia'
      if (t <= 10) return 'Trochę roboty przed Tobą 💪'
      if (t <= 25) return 'Do roboty jak koń! 🐴'
      return 'Kupa roboty — trzymaj się! 🥵'
    },
    todoTitle() {
      if (!this.myTodo) return ''
      return `Do zrobienia w ciagu 7 dni: ${this.myTodo.total} (zapytania ${this.myTodo.zapytania}, oferty ${this.myTodo.oferty}, kontakty ${this.myTodo.kontakty}, zadania ${this.myTodo.zadania})`
    },
    imieninyToday() {
      return imieninyDnia(new Date())
    },
    todoModalTitle() {
      return ({
        zapytania: 'Zapytania — termin złożenia',
        oferty: 'Oferty — data kontaktu',
        kontakty: 'Kontakty — nadchodzące terminy',
        zadania: 'Zadania — deadline',
      })[this.todoModal] || ''
    },
    todoModalColorClass() {
      return ({
        zapytania: 'bg-indigo-500',
        oferty: 'bg-green-500',
        kontakty: 'bg-blue-500',
        zadania: 'bg-orange-500',
      })[this.todoModal] || 'bg-gray-400'
    },
    imieninyMatchedEmployees() {
      // Lista pracownikow ktorzy dzis maja imieniny (do tooltipa)
      return this.userFirstNames.filter(fn => this.imieninyToday.some(im => this.namesMatch(im, fn)))
    },
    imieninyTitle() {
      const base = `Imieniny obchodzą dzisiaj: ${this.imieninyToday.join(', ')}`
      if (this.imieninyMatchedEmployees.length === 0) return base
      return `${base}\n\n🎉 W firmie świętują: ${this.imieninyMatchedEmployees.join(', ')}`
    },
  },
  mounted() {
    if (this.vapidPublicKey) {
      initPushNotifications(this.vapidPublicKey)
    }
  },
  methods: {
    openTodoModal(category) {
      if (this.myTodo && this.myTodo[category] > 0) {
        this.todoModal = category
      }
    },
    formatKwota(v) {
      if (!v) return '0'
      return new Intl.NumberFormat('pl-PL').format(v)
    },
    // Dopasowanie: user first_name w mianowniku vs imieniny (moga byc w dopelniaczu np. 'Marii').
    // Uznajemy match jesli: dokladnie taki sam, lub imieniny to forma wywodzaca sie z imienia
    // (imieniny zaczynaja sie od rdzenia imienia i roznica dlugosci <= 2 znaki).
    namesMatch(imieninyName, userName) {
      if (!imieninyName || !userName) return false
      const im = imieninyName.toLowerCase().trim()
      const u = userName.toLowerCase().trim()
      if (im === u) return true
      if (u.length < 4) return false // krotkie imiona jak "Ada" - wymagamy dokladnego dopasowania
      const root = u.slice(0, -1) // np. "Maria" -> "Mari"
      if (!im.startsWith(root)) return false
      // Roznica dlugosci nie wieksza niz 2 (dopelniacz zwykle: +1..+2 znaki jak "Marii", "Marię", "Filipa")
      return Math.abs(im.length - u.length) <= 2
    },
    matchesEmployee(imieninyName) {
      return this.userFirstNames.some(fn => this.namesMatch(imieninyName, fn))
    },
    formatRelative(dt) {
      if (!dt) return ''
      const then = new Date(dt.replace(' ', 'T'))
      const diffSec = Math.floor((Date.now() - then.getTime()) / 1000)
      if (diffSec < 60) return 'teraz'
      const min = Math.floor(diffSec / 60)
      if (min < 60) return min + ' min temu'
      const hrs = Math.floor(min / 60)
      if (hrs < 24) return hrs + ' godz. temu'
      const days = Math.floor(hrs / 24)
      return days + ' d. temu'
    },
  },
}
</script>

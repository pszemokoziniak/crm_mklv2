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
            <div class="mr-4 mt-1 font-bold text-indigo-600 uppercase tracking-wider">
              {{ auth.user.roles[0] || 'Użytkownik' }}
            </div>
            <div class="flex items-center space-x-2">
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
  },
  mounted() {
    if (this.vapidPublicKey) {
      initPushNotifications(this.vapidPublicKey)
    }
  },
  methods: {
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

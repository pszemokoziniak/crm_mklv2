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

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Link,
    Logo,
    MainMenu,
  },
  props: {
    auth: Object,
    mainMenus: Array, // Add mainMenus to props
  },
}
</script>

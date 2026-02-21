<template>
  <div class="space-y-1">
    <div v-for="item in menuItems" :key="item.href">
      <Link
        class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200"
        :href="item.href"
        :class="isUrl(item.activeRule) ? 'bg-indigo-900 text-white shadow-inner' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white'"
      >
        <icon
          :name="item.icon"
          class="mr-3 w-5 h-5 transition-colors duration-200"
          :class="isUrl(item.activeRule) ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'"
        />
        <div class="font-medium">{{ item.label }}</div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
    Link,
  },
  data() {
    return {
      menuItems: [
        { label: 'Home', href: '/', icon: 'home', activeRule: '' },
        { label: 'Klienci', href: '/clients', icon: 'clients', activeRule: 'clients' },
        { label: 'Zapytania', href: '/zapytania', icon: 'zapytania', activeRule: 'zapytania' },
        { label: 'Oferty', href: '/oferta', icon: 'oferty', activeRule: 'oferta' },
        { label: 'Kontakty', href: '/kontakt', icon: 'contact', activeRule: 'kontakt' },
        { label: 'Zadania', href: '/zadania', icon: 'tasks', activeRule: 'zadania' },
        { label: 'Kalendarz', href: '/calendar', icon: 'calendar', activeRule: 'calendar' },
        { label: 'Przyszłe projekty', href: '/futureproject', icon: 'future', activeRule: 'futureproject' },
        { label: 'LinkedIn', href: '/linkedin', icon: 'linkedin', activeRule: 'linkedin' },
        { label: 'Linki www', href: '/stronywww', icon: 'www', activeRule: 'stronywww' },
        { label: 'Statystyki', href: '/stats', icon: 'statystyki', activeRule: 'stats' },
        { label: 'Ustawienia', href: '/edit', icon: 'edit', activeRule: 'edit' },
        { label: 'Użytkownicy', href: '/users', icon: 'users', activeRule: 'users' },
        { label: 'Historia', href: '/activity', icon: 'historia', activeRule: 'activity' },
        { label: 'BackUp', href: '/backup', icon: 'backup', activeRule: 'backup' },
      ],
    }
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>

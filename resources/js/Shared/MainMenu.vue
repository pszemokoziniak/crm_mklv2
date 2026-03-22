<template>
  <div class="space-y-1">
    <div v-for="item in filteredMenuItems" :key="item.href">
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
        { label: 'Klienci', href: '/clients', icon: 'clients', activeRule: 'clients', permission: 'view_clients' },
        { label: 'Zapytania', href: '/zapytania', icon: 'zapytania', activeRule: 'zapytania', permission: 'view_zapytania' },
        { label: 'Oferty', href: '/oferta', icon: 'oferty', activeRule: 'oferta', permission: 'view_oferty' },
        { label: 'Kontakty', href: '/kontakt', icon: 'contact', activeRule: 'kontakt', permission: 'view_kontakt' },
        { label: 'Zadania', href: '/zadania', icon: 'tasks', activeRule: 'zadania', permission: 'view_zadania' },
        { label: 'Kalendarz', href: '/calendar', icon: 'calendar', activeRule: 'calendar', permission: 'view_calendar' },
        { label: 'Przyszłe projekty', href: '/futureproject', icon: 'future', activeRule: 'futureproject', permission: 'view_future_projects' },
        { label: 'LinkedIn', href: '/linkedin', icon: 'linkedin', activeRule: 'linkedin', permission: 'view_linkedin' },
        { label: 'Linki www', href: '/stronywww', icon: 'www', activeRule: 'stronywww', permission: 'view_stronywww' },
        { label: 'Statystyki', href: '/stats', icon: 'statystyki', activeRule: 'stats', permission: 'view_stats' },
        { label: 'Ustawienia', href: '/edit', icon: 'edit', activeRule: 'edit', permission: 'manage_settings' },
        { label: 'Użytkownicy', href: '/users', icon: 'users', activeRule: 'users', permission: 'manage_users' },
        { label: 'Historia', href: '/activity', icon: 'historia', activeRule: 'activity', permission: 'view_activity' },
      ],
    }
  },
  computed: {
    filteredMenuItems() {
      const userPermissions = this.$page.props.auth.user.permissions || []
      const isSuperAdmin = this.$page.props.auth.user.is_super_admin // Assuming you have a flag for super admin

      return this.menuItems.filter(item => {
        // Super admin sees everything
        if (isSuperAdmin) {
          return true
        }

        // If an item has no specific permission, it's visible to everyone (unless other filters apply)
        if (!item.permission) {
          return true
        }

        // Check if the user has the required permission
        return userPermissions.includes(item.permission)
      })
    },
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

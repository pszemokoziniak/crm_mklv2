<template>
  <div class="space-y-1">
    <div v-for="item in filteredMainMenus" :key="item.route">
      <Link
        class="group flex items-center px-4 py-3 rounded-lg transition-all duration-200"
        :href="item.route"
        :class="isUrl(item.route) ? 'bg-indigo-900 text-white shadow-inner' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white'"
      >
        <icon
          :name="item.icon"
          class="mr-3 w-5 h-5 transition-colors duration-200"
          :class="isUrl(item.route) ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'"
        />
        <div class="font-medium">{{ item.name }}</div>
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
  props: {
    mainMenus: Array, // This prop will now hold the menu items from the database
  },
  computed: {
    filteredMainMenus() {
      return [...this.mainMenus].sort((a, b) => a.order - b.order)
    },
  },
  methods: {
    isUrl(url) {
      let currentUrl = this.$page.url.substr(1)
      if (url === '/') {
        return currentUrl === ''
      }
      const cleanUrl = url.startsWith('/') ? url.substring(1) : url
      return currentUrl.startsWith(cleanUrl)
    },
  },
}
</script>

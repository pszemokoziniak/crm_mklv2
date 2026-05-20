<template>
  <div>
    <Head title="Ustawienia" />
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-800">Ustawienia systemowe</h1>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <Link v-for="item in menuItems" :key="item.href" :href="item.href" class="flex items-center p-5 bg-white rounded-xl shadow-sm border border-gray-200 hover:border-indigo-500 hover:shadow-md transition-all duration-200 group">
        <div class="flex-shrink-0 p-3 rounded-lg bg-indigo-50 group-hover:bg-indigo-600 transition-colors duration-200">
          <icon :name="item.icon" class="w-6 h-6 fill-indigo-600 group-hover:fill-white" />
        </div>
        <div class="ml-4">
          <span class="text-lg font-semibold text-gray-700 group-hover:text-indigo-900">{{ item.label }}</span>
        </div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Head,
    Link,
    Icon,
  },
  layout: Layout,
  data() {
    return {
      menuItems: [
        { label: 'Branże', href: '/branza/', icon: 'office' },
        { label: 'Kraj', href: '/kraj/', icon: 'home' },
        { label: 'Zakres', href: '/zakres/', icon: 'tasks' },
        { label: 'Oferty Status', href: '/ofertastatus/', icon: 'oferty' },
        { label: 'Kursy', href: '/kursy/', icon: 'statystyki' },
        { label: 'Faza projektu', href: '/faza/', icon: 'future' },
        { label: 'Obiekt', href: '/objekt/', icon: 'office' },
        { label: 'Waluta', href: '/waluta/', icon: 'oferty' },
        { label: 'Uprawnienia', href: '/uprawnienia/', icon: 'users' },
        { label: 'Przypomnienia mailowe', href: '/reminder-rules', icon: 'mail' },
        { label: 'Menu', href: '/menu', icon: 'menu' }, // Added Menu item
      ],
    }
  },
}
</script>

<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Uprawnienia</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" href="/uprawnienia/create">
        <span>Dodaj</span>
        <span class="hidden md:inline">&nbsp;Uprawnienie</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Nazwa</th>
          <th class="pb-4 pt-6 px-6">Przypisane Menu</th>
          <th class="pb-4 pt-6 px-6" colspan="2" />
        </tr>
        <tr v-for="uprawnienie in filteredUprawnienia" :key="uprawnienie.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/uprawnienia/${uprawnienie.id}/edit`">
              {{ uprawnienie.name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/uprawnienia/${uprawnienie.id}/edit`">
              <span v-if="uprawnienie.main_menus && uprawnienie.main_menus.length">
                {{ uprawnienie.main_menus.map(menu => menu.name).join(', ') }}
              </span>
              <span v-else class="text-gray-500">Brak</span>
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/uprawnienia/${uprawnienie.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="filteredUprawnienia.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak uprawnień do wyświetlenia.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'

export default {
  components: {
    Icon,
    Link,
  },
  layout: Layout,
  props: {
    uprawnienia: Array,
  },
  computed: {
    filteredUprawnienia() {
      return this.uprawnienia.filter(uprawnienie =>
        uprawnienie.name !== 'super-admin' && uprawnienie.name !== 'Administrator',
      )
    },
  },
}
</script>

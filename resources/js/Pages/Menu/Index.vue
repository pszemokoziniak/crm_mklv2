<template>
  <div>
    <h1 class="mb-8 text-3xl font-bold">Menu</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-primary" :href="route('menu.create')">
        <span>Create</span>
        <span class="hidden md:inline">&nbsp;Menu Item</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Name</th>
          <th class="pb-4 pt-6 px-6">Route</th>
          <th class="pb-4 pt-6 px-6" colspan="2">Order</th>
        </tr>
        <!-- Loop through menu items here -->
        <tr v-for="menuItem in menuItems" :key="menuItem.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="route('menu.edit', menuItem.id)">
              {{ menuItem.name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="route('menu.edit', menuItem.id)" tabindex="-1">
              {{ menuItem.route }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="route('menu.edit', menuItem.id)" tabindex="-1">
              {{ menuItem.order }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="route('menu.edit', menuItem.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="menuItems.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">No menu items found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3' // Changed from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'

export default {
  components: {
    Icon,
    Link,
  },
  layout: Layout,
  props: {
    menuItems: Array, // This will be passed from the controller
  },
}
</script>

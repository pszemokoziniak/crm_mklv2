<template>
  <div>
    <Head title="Menu" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Menu</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/menu/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj pozycję menu</span>
      </Link>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full whitespace-nowrap">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Nazwa</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Route</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Ikona</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Kolejność</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="menuItem in menuItems" :key="menuItem.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition-colors" :href="`/menu/${menuItem.id}/edit`">
                  {{ menuItem.name }}
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="text-xs text-gray-600 font-medium" :href="`/menu/${menuItem.id}/edit`" tabindex="-1">
                  <code class="px-1.5 py-0.5 bg-gray-100 rounded text-[11px]">{{ menuItem.route }}</code>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="text-xs text-gray-600 font-medium" :href="`/menu/${menuItem.id}/edit`" tabindex="-1">
                  {{ menuItem.icon || '—' }}
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link :href="`/menu/${menuItem.id}/edit`" tabindex="-1">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-600 shadow-sm">
                    {{ menuItem.order }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/menu/${menuItem.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="menuItems.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="5">
                <div class="flex flex-col items-center">
                  <icon name="menu" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Brak pozycji menu.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
  },
  layout: Layout,
  props: {
    menuItems: Array,
  },
}
</script>
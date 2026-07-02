<template>
  <div>
    <Head title="Aktywność systemowa" />
    <h1 class="mb-8 text-3xl font-bold">Aktywność systemowa</h1>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="w-full table-fixed">
        <colgroup>
          <col style="width:22%" /><!-- Uzytkownik -->
          <col style="width:18%" /><!-- Akcja -->
          <col style="width:38%" /><!-- Model -->
          <col style="width:22%" /><!-- Data -->
        </colgroup>
        <thead>
          <tr class="text-left font-bold text-gray-400 text-[10px] uppercase tracking-widest bg-gray-50/50">
            <th class="px-3 py-3">Użytkownik</th>
            <th class="px-3 py-3">Akcja</th>
            <th class="px-3 py-3">Model</th>
            <th class="px-3 py-3">Data</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="item in historia.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
            <td class="px-3 py-3 overflow-hidden">
              <span class="block font-medium text-gray-900 text-xs truncate" :title="item.causer">{{ item.causer }}</span>
            </td>
            <td class="px-3 py-3 overflow-hidden">
              <span
                :class="[
                  item.description === 'created' ? 'text-green-600' :
                  item.description === 'deleted' ? 'text-red-600' : 'text-blue-600',
                  'font-bold uppercase text-xs tracking-wider whitespace-nowrap'
                ]"
              >
                {{ item.description === 'created' ? 'Utworzono' :
                  item.description === 'updated' ? 'Zaktualizowano' :
                  item.description === 'deleted' ? 'Usunięto' : item.description }}
              </span>
            </td>
            <td class="px-3 py-3 overflow-hidden">
              <Link v-if="item.link" :href="item.link" class="block text-indigo-600 hover:underline font-semibold text-xs truncate" :title="`${item.subject_type} #${item.subject_id}`">
                {{ item.subject_type }} #{{ item.subject_id }}
              </Link>
              <span v-else class="block text-gray-400 text-xs truncate" :title="`${item.subject_type} #${item.subject_id}`">{{ item.subject_type }} #{{ item.subject_id }}</span>
            </td>
            <td class="px-3 py-3 text-xs text-gray-500 whitespace-nowrap overflow-hidden">
              {{ item.created_at }}
            </td>
          </tr>
          <tr v-if="historia.data && historia.data.length === 0">
            <td class="px-6 py-8 text-center text-gray-400 text-xs" colspan="4">
              Brak zarejestrowanej aktywności.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-4" :links="historia.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'

export default {
  components: {
    Head,
    Link,
    Pagination,
  },
  layout: Layout,
  props: {
    historia: Object,
  },
}
</script>

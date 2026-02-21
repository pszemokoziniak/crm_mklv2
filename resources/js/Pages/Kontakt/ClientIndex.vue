<template>
  <div>
    <Head title="Kontakty" />
    <h1 class="mb-8 text-3xl font-bold">
      Kontakty /
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/clients/${client_id}/edit`">
        <span>{{ client?.nazwa }}</span>
      </Link>
    </h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" :href="`/kontakt/create/${client_id}`">
        <span>Dodaj nowy kontakt</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead>
          <tr class="text-left font-bold bg-gray-50">
            <th class="pb-4 pt-6 px-6">Temat</th>
            <th class="pb-4 pt-6 px-6 hidden md:table-cell">Opis</th>
            <th class="pb-4 pt-6 px-6">Osoba kontaktowa</th>
            <th class="pb-4 pt-6 px-6">Data kontaktu</th>
            <th class="pb-4 pt-6 px-6 text-indigo-600">Następny kontakt</th>
            <th class="pb-4 pt-6 px-6" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in kontakt" :key="item.id" class="hover:bg-gray-50 focus-within:bg-gray-100 transition-colors">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500 font-medium text-indigo-700" :href="`/kontakt/${item.id}/edit`">
                {{ item.subject }}
                <span v-if="item.replies_count > 0" class="ml-2 text-xs text-gray-500">({{ item.replies_count }} odpowiedzi)</span>
                <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t hidden md:table-cell">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                <div class="max-w-xs truncate">
                  {{ item.description }}
                </div>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                <span v-if="item.kontaktperson">
                  {{ item.kontaktperson.last_name }} {{ item.kontaktperson.first_name }}
                </span>
                <span v-else class="text-gray-400 italic">Brak osoby</span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                {{ item.call_date }} <span class="ml-2 text-gray-400 text-xs">{{ item.call_time }}</span>
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 font-semibold text-indigo-600" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                <span v-if="item.next_call_date">
                  {{ item.next_call_date }} <span class="ml-1 text-xs text-indigo-400">{{ item.next_call_time }}</span>
                </span>
                <span v-else class="text-gray-300 font-normal">-</span>
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="kontakt.length === 0">
            <td class="px-6 py-4 border-t text-center text-gray-500" colspan="6">Nie znaleziono żadnych kontaktów.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'

export default {
  components: {
    Head,
    Icon,
    Link,
  },
  layout: Layout,
  props: {
    kontakt: Array,
    client_id: [String, Number],
    client: Object,
  },
}
</script>

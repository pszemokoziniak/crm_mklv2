<template>
  <div>
    <Head title="Kontakty" />
    <div class="flex items-center justify-between mb-6">
      <h1 class="mb-8 text-3xl font-bold"> Kontakty /
        <Link class="text-indigo-400 hover:text-indigo-600" :href="`/kontaktperson/${client_id}/index`">
          <span>Osoby kontaktowe</span>
        </Link>
      </h1>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Temat</th>
          <th class="pb-4 pt-6 px-6">Opis</th>
          <th class="pb-4 pt-6 px-6">Osoba kontaktowa</th>
          <th class="pb-4 pt-6 px-6" colspan="2">Data kontaktu</th>
        </tr>
        <tr v-for="item in kontakt" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/kontakt/${item.id}/edit`">
              {{ item.subject }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
              <div class="max-w-xs truncate">
                {{ item.description }}
              </div>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
              <span v-if="item.kontaktperson">
                {{ item.kontaktperson.last_name }} {{ item.kontaktperson.first_name }}
              </span>
              <span v-else class="text-gray-400">Brak osoby</span>
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
              {{ item.call_date }} {{ item.call_time }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/kontakt/${item.id}/edit`" tabindex="-1">
              <svg class="block w-6 h-6 fill-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <polygon points="12.95 10.707 13.657 10 8 4.343 6.586 5.757 10.828 10 6.586 14.243 8 15.657 12.95 10.707" />
              </svg>
            </Link>
          </td>
        </tr>
        <tr v-if="kontakt.length === 0">
          <td class="px-6 py-4 border-t" colspan="5">Nie znaleziono.</td>
        </tr>
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
  },
}
</script>

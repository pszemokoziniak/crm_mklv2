<template>
  <div>
    <Head title="Kontakt" />
    <h1 class="mb-8 text-3xl font-bold">Kontakty</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" :href="`/kontakt/create/${client_id}`">
        <span>Dodaj</span>
        <span class="hidden md:inline">&nbsp;Kontak</span>
      </Link>
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
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/kontakt/${client_id}/edit`">
              {{ item.subject }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${client_id}/edit`" tabindex="-1">
              {{ item.kontaktperson.last_name }} {{ item.kontaktperson.first_name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${client_id}/edit`" tabindex="-1">
              {{ item.description }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${client_id}/edit`" tabindex="-1">
              {{ item.call_time }}
            </Link>
          </td>

<!--          <td class="border-t">-->
<!--            <Link class="flex items-center px-6 py-4" :href="`/kontakt/${client_id}/edit`" tabindex="-1">-->
<!--              <div v-if="item.zapytania">-->
<!--                {{ item.zapytania.nazwa_projektu }}-->
<!--              </div>-->
<!--            </Link>-->
<!--          </td>-->
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/kontakt/${client_id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="kontakt.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Nie znaleziono.</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="kontakt.links" />-->
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    kontakt: Object,
    client_id: String,
  },
  data() {
    return {
      form: {
        // search: this.filters.search,
        // trashed: this.filters.trashed,
      },
    }
  },
  // watch: {
  //   form: {
  //     deep: true,
  //     handler: throttle(function () {
  //       this.$inertia.get('/contacts', pickBy(this.form), { preserveState: true })
  //     }, 150),
  //   },
  // },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>

<template>
  <div>
    <Head title="Osoby kontaktowe" />
    <h1 class="mb-8 text-3xl font-bold">Osoby kontaktowe /
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/clients/${client_id}/edit`">
        <span>{{client[0].nazwa}}</span>
      </Link>
    </h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" :href="`/kontaktperson/create/${client_id}`">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Nazwisko Imię</th>
          <th class="pb-4 pt-6 px-6">Pozycja</th>
          <th class="pb-4 pt-6 px-6">Telefon</th>
          <th class="pb-4 pt-6 px-6">Email</th>
          <th class="pb-4 pt-6 px-6">Dodał</th>
        </tr>
        <tr v-for="contact in kontaktPerson" :key="contact.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/kontaktperson/${contact.id}/edit`">
              {{ contact.last_name }} {{ contact.first_name }}
              <icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
              {{ contact.position }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
              {{ contact.phone1 }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
              {{ contact.email }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
              <div v-if="contact.user">
                {{ contact.user.last_name }} {{ contact.user.first_name }}
              </div>
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="kontaktPerson.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Nie znaleziono.</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="kontaktPerson.links" />-->
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
    kontaktPerson: Object,
    client_id: String,
    client: Object,
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

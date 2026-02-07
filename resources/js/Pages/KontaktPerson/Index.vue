<template>
  <div>
    <Head title="Osoby kontaktowe" />
    <h1 class="mb-8 text-3xl font-bold">
      Osoby kontaktowe /
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/clients/${client_id}/edit`">
        <span>{{ client[0]?.nazwa }}</span>
      </Link>
    </h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" :href="`/kontaktperson/create/${client_id}`">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full table-auto">
        <thead>
          <tr class="text-left font-bold bg-gray-50">
            <th class="pb-4 pt-6 px-6">Nazwisko Imię</th>
            <th class="pb-4 pt-6 px-6 hidden md:table-cell">Pozycja</th>
            <th class="pb-4 pt-6 px-6">Telefon</th>
            <th class="pb-4 pt-6 px-6 hidden lg:table-cell">Email</th>
            <th class="pb-4 pt-6 px-6 hidden sm:table-cell">Miasto</th>
            <th class="pb-4 pt-6 px-6 hidden xl:table-cell">Dodał</th>
            <th class="pb-4 pt-6 px-6" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="contact in kontaktPerson" :key="contact.id" class="hover:bg-gray-50 focus-within:bg-gray-100 transition-colors">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500 font-medium text-indigo-700" :href="`/kontaktperson/${contact.id}/edit`">
                {{ contact.last_name }} {{ contact.first_name }}
                <icon v-if="contact.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t hidden md:table-cell">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
                {{ contact.position }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
                {{ contact.phone1 }}
              </Link>
            </td>
            <td class="border-t hidden lg:table-cell">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
                {{ contact.email }}
              </Link>
            </td>
            <td class="border-t hidden sm:table-cell">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
                {{ contact.miasto }}
              </Link>
            </td>
            <td class="border-t hidden xl:table-cell">
              <Link class="flex items-center px-6 py-4 text-gray-600" :href="`/kontaktperson/${contact.id}/edit`" tabindex="-1">
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
            <td class="px-6 py-4 border-t text-center text-gray-500" colspan="7">Nie znaleziono.</td>
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
    kontaktPerson: Array,
    client_id: [String, Number],
    client: Array,
  },
}
</script>

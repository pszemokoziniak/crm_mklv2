<template>
  <div>
    <Head title="LinkedIn" />
    <h1 class="mb-8 text-3xl font-bold">LinkedIn</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" href="/linkedin/create">
        <span>Dodaj</span>
<!--        <span class="hidden md:inline">&nbsp;Branza</span>-->
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Firma</th>
          <th class="pb-4 pt-6 px-6">Link</th>
          <th class="pb-4 pt-6 px-6">Update</th>
          <th class="pb-4 pt-6 px-6">Ostatni click</th>
        </tr>
        <tr v-for="item in linkedin" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
              {{ item.name }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
              {{ item.link }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
              {{ item.updated_at }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
              {{ item.click }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="btn-indigo" :href="`/linkedin/${item.id}/click`">
              <span>Odwiedź stronę</span>
            </Link>
          </td>
        </tr>
        <tr v-if="linkedin.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="faza.links" />-->
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
  },
  layout: Layout,
  props: {
    linkedin: Object,
  },
}
</script>

<template>
  <div>
    <Head title="Historia" />
    <h1 class="mb-8 text-3xl font-bold">Historia</h1>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Typ</th>
          <th class="pb-4 pt-6 px-6">Firma</th>
<!--          <th class="pb-4 pt-6 px-6">Info</th>-->
          <th class="pb-4 pt-6 px-6">Użytkownik</th>
          <th class="pb-4 pt-6 px-6">Data</th>
        </tr>
        <tr v-for="item in historia.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/${item.link_action}/${item.link_id}/edit`">
              {{ item.action }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/${item.link_action}/${item.link_id}/edit`">
              {{ item.client.nazwa }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
<!--          <td class="border-t">-->
<!--            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/${item.link_action}/${item.id}/edit`">-->
<!--&lt;!&ndash;              {{ item.updated_at }}&ndash;&gt;-->
<!--              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
<!--            </Link>-->
<!--          </td>-->
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/${item.link_action}/${item.link_id}/edit`">
              {{ item.user.last_name }} {{ item.user.first_name }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/${item.link_action}/${item.link_id}/edit`">
              {{ item.created_at }}
              <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="historia.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak</td>
        </tr>
      </table>
    </div>
    <pagination class="mt-6" :links="historia.links" />
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
    historia: Object,
  },
}
</script>

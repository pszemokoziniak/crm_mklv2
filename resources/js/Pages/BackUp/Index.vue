<template>
  <div>
    <Head title="Back Up" />
    <h1 class="mb-8 text-3xl font-bold">Back Up</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" href="/backup/store">
        <span>Dodaj</span>
                <span class="hidden md:inline">&nbsp;Back Up</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Back Up</th>
          <th class="pb-4 pt-6 px-6">Akcja</th>
        </tr>
        <tr v-for="item in files" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <p class="flex items-center px-6 py-4 focus:text-indigo-500">
              {{ item }}
            </p>
          </td>
          <td class="border-t">
            <a :href="`/backup/download/${item}`" class="btn-indigo">
              <span>Pobierz</span>
            </a>
          </td>
        </tr>
        <tr v-if="files.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak</td>
        </tr>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="files.links" />-->
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
    files: Object,
  },
}
</script>

<template>
  <div>
    <Head title="Strony WWW" />
    <h1 class="mb-8 text-3xl font-bold">Strony WWW</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset" />
      <Link class="btn-indigo" href="/stronywww/create">
        <span>Dodaj</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-hidden">
      <table class="w-full table-auto">
        <thead>
          <tr class="text-left font-bold bg-gray-50">
            <th class="pb-4 pt-6 px-2 sm:px-6">Nazwa</th>
            <th class="pb-4 pt-6 px-2 sm:px-6">Link</th>
            <th class="pb-4 pt-6 px-2 sm:px-6 hidden md:table-cell">Update</th>
            <th class="pb-4 pt-6 px-2 sm:px-6 text-center">Kliknięcia</th>
            <th class="pb-4 pt-6 px-2 sm:px-6" />
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stronywww.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t px-2 sm:px-6 py-4">
              <Link class="flex items-center focus:text-indigo-500" :href="`/stronywww/${item.id}/edit`">
                <span class="font-medium break-words">{{ item.name }}</span>
                <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t px-2 sm:px-6 py-4">
              <Link class="flex items-center focus:text-indigo-500" :href="`/stronywww/${item.id}/edit`">
                <div class="text-blue-600 break-all text-xs sm:text-sm line-clamp-2" :title="item.link">
                  {{ item.link }}
                </div>
              </Link>
            </td>
            <td class="border-t px-2 sm:px-6 py-4 hidden md:table-cell">
              <Link class="flex items-center text-xs whitespace-nowrap" :href="`/stronywww/${item.id}/edit`">
                {{ item.updated_at }}
              </Link>
            </td>
            <td class="border-t px-2 sm:px-6 py-4 text-center">
              <Link class="block text-sm" :href="`/stronywww/${item.id}/edit`">
                {{ item.click }}
              </Link>
            </td>
            <td class="border-t px-2 sm:px-6 py-4 text-right">
              <div class="flex items-center justify-end">
                <a class="btn-indigo py-1 px-2 sm:px-3 text-[10px] sm:text-xs mr-1 sm:mr-4" target="_blank" :href="`/stronywww/${item.id}/click`">
                  Odwiedź
                </a>
                <Link :href="`/stronywww/${item.id}/edit`" tabindex="-1" class="hidden sm:inline-block">
                  <icon name="cheveron-right" class="w-5 h-5 fill-gray-400" />
                </Link>
              </div>
            </td>
          </tr>
          <tr v-if="stronywww.data.length === 0">
            <td class="px-6 py-4 border-t text-center" colspan="5">Brak wpisów Strony WWW</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="stronywww.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'

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
    filters: Object,
    stronywww: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/stronywww', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>

<template>
  <div>
    <Head title="LinkedIn" />
    <h1 class="mb-8 text-3xl font-bold text-gray-900">LinkedIn</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset" />
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95" href="/linkedin/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Dodaj link</span>
      </Link>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full table-auto">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Firma</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Link</div>
              </th>
              <th class="px-6 py-1.5 hidden md:table-cell whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Aktualizacja</div>
              </th>
              <th class="px-6 py-1.5 text-center whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-center whitespace-nowrap">Kliknięcia</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in linkedin.data" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="flex items-center focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
                  <span class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ item.client?.nazwa || 'Brak klienta' }}</span>
                  <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="flex items-center focus:text-indigo-500" :href="`/linkedin/${item.id}/edit`">
                  <div class="text-blue-600 break-all text-[11px] font-medium line-clamp-1 hover:underline" :title="item.link">
                    {{ item.link }}
                  </div>
                </Link>
              </td>
              <td class="px-6 py-4 hidden md:table-cell">
                <Link class="flex items-center text-[10px] text-gray-400 font-medium whitespace-nowrap" :href="`/linkedin/${item.id}/edit`">
                  {{ item.updated_at }}
                </Link>
              </td>
              <td class="px-6 py-4 text-center">
                <Link class="inline-flex items-center justify-center min-w-[32px] h-6 px-2 rounded-full bg-gray-100 text-gray-700 text-xs font-bold" :href="`/linkedin/${item.id}/edit`">
                  {{ item.click }}
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-3">
                  <a class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded hover:bg-indigo-100 transition-colors whitespace-nowrap uppercase tracking-wider shadow-sm" target="_blank" :href="`/linkedin/${item.id}/click`">
                    Odwiedź
                  </a>
                  <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/linkedin/${item.id}/edit`" tabindex="-1">
                    <icon name="cheveron-right" class="w-4 h-4" />
                  </Link>
                </div>
              </td>
            </tr>
            <tr v-if="linkedin.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="5">
                <div class="flex flex-col items-center">
                  <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Brak linków LinkedIn spełniających kryteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <pagination class="mt-6" :links="linkedin.links" />
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
    linkedin: Object,
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
        this.$inertia.get('/linkedin', pickBy(this.form), { preserveState: true })
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

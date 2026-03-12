<template>
  <div>
    <Head title="Użytkownicy" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Użytkownicy</h1>
      <Link class="btn-indigo flex items-center justify-center px-6 py-2 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95 w-full md:w-auto" href="/users/create">
        <icon name="plus" class="w-4 h-4 mr-2" />
        <span>Utwórz użytkownika</span>
      </Link>
    </div>

    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-2xl" @reset="reset">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rola:</label>
            <select v-model="form.role" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option :value="null">Wszystkie role</option>
              <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
            <select v-model="form.trashed" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option :value="null">Aktywni</option>
              <option value="with">Wszystko</option>
              <option value="only">Tylko zarchiwizowane</option>
            </select>
          </div>
        </div>
      </search-filter>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full whitespace-nowrap">
          <thead>
            <tr class="text-left text-gray-500 bg-gray-50/50 border-b border-gray-100">
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Użytkownik</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Email</div>
              </th>
              <th class="px-6 py-1.5 whitespace-nowrap">
                <div class="text-[10px] font-semibold uppercase tracking-tight scale-[0.8] origin-left whitespace-nowrap">Rola</div>
              </th>
              <th class="px-6 py-1.5 w-12" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="user in users.data" :key="user.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="flex items-center focus:outline-none" :href="`/users/${user.id}/edit`">
                  <div class="relative flex-shrink-0">
                    <img v-if="user.photo" class="block w-8 h-8 rounded-full border border-gray-200 shadow-sm" :src="user.photo" alt="photo" />
                    <div v-else class="flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs border border-indigo-200">
                      {{ user.name.split(' ').map(n => n[0]).join('') }}
                    </div>
                    <div :class="user.active ? 'bg-green-500' : 'bg-gray-400'" class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 border-2 border-white rounded-full" />
                  </div>
                  <div class="ml-3">
                    <div class="text-xs font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                      {{ user.name }}
                      <icon v-if="user.deleted_at" name="trash" class="inline-block ml-2 w-3 h-3 fill-rose-400" />
                    </div>
                  </div>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="text-xs text-gray-600 font-medium" :href="`/users/${user.id}/edit`" tabindex="-1">
                  {{ user.email }}
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link :href="`/users/${user.id}/edit`" tabindex="-1">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-600 uppercase tracking-wider shadow-sm">
                    {{ user.roles.join(', ') }}
                  </span>
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <Link class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-50 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm" :href="`/users/${user.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-4 h-4" />
                </Link>
              </td>
            </tr>
            <tr v-if="users.data.length === 0">
              <td class="px-6 py-12 text-center text-gray-400" colspan="4">
                <div class="flex flex-col items-center">
                  <icon name="users" class="w-12 h-12 mb-2 opacity-20" />
                  <p class="text-xs">Brak użytkowników spełniających kryteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <pagination class="mt-6" :links="users.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import SearchFilter from '@/Shared/SearchFilter'
import Pagination from '@/Shared/Pagination'


export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
    Pagination,
  },
  layout: Layout,
  props: {
    filters: Object,
    users: Object,
    roles: Array,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/users', pickBy(this.form), { preserveState: true })
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

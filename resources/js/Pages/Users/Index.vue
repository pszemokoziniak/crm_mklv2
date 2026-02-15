<template>
  <div>
    <Head title="Użytkownicy" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">Użytkownicy</h1>
      <Link class="btn-indigo shadow-md inline-flex items-center px-6 py-3" href="/users/create">
        <icon name="plus" class="w-4 h-4 mr-2 fill-white" />
        <span>Utwórz użytkownika</span>
      </Link>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 md:p-6">
        <search-filter v-model="form.search" class="w-full max-w-2xl" @reset="reset">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Rola:</label>
              <select v-model="form.role" class="form-select w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option :value="null">Wszystkie role</option>
                <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status archiwum:</label>
              <select v-model="form.trashed" class="form-select w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <option :value="null">Aktywni</option>
                <option value="with">Wszystko</option>
                <option value="only">Tylko zarchiwizowane</option>
              </select>
            </div>
          </div>
        </search-filter>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap">
          <thead>
            <tr class="text-left bg-gray-50 border-b border-gray-100">
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Użytkownik</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Rola</th>
              <th class="px-6 py-4" />
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="user in users" :key="user.id" class="hover:bg-indigo-50/30 transition-colors group">
              <td class="px-6 py-4">
                <Link class="flex items-center focus:outline-none" :href="`/users/${user.id}/edit`">
                  <div class="relative">
                    <img v-if="user.photo" class="block w-10 h-10 rounded-full border border-gray-200 shadow-sm" :src="user.photo" alt="photo" />
                    <div v-else class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm border border-indigo-200">
                      {{ user.name.split(' ').map(n => n[0]).join('') }}
                    </div>
                    <div :class="user.active ? 'bg-green-500' : 'bg-gray-400'" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 border-2 border-white rounded-full" />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                      {{ user.name }}
                      <icon v-if="user.deleted_at" name="trash" class="inline-block ml-2 w-3 h-3 fill-rose-400" />
                    </div>
                  </div>
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="text-sm text-gray-600" :href="`/users/${user.id}/edit`" tabindex="-1">
                  {{ user.email }}
                </Link>
              </td>
              <td class="px-6 py-4">
                <Link class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800" :href="`/users/${user.id}/edit`" tabindex="-1">
                  {{ user.roles.join(', ') }}
                </Link>
              </td>
              <td class="px-6 py-4 text-right">
                <Link class="inline-flex items-center p-2 text-gray-400 hover:text-indigo-600 transition-colors" :href="`/users/${user.id}/edit`" tabindex="-1">
                  <icon name="cheveron-right" class="w-5 h-5" />
                </Link>
              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td class="px-6 py-12 text-center text-gray-500" colspan="4">
                <div class="flex flex-col items-center">
                  <icon name="users" class="w-12 h-12 text-gray-200 mb-2" />
                  <p>Nie znaleziono użytkowników</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
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


export default {
  components: {
    Head,
    Icon,
    Link,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    users: Array,
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

<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div class="flex items-center">
        <div class="relative">
          <img v-if="user.photo" class="block w-16 h-16 rounded-full border-2 border-white shadow-sm" :src="user.photo" alt="phot" />
          <div v-else class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xl border-2 border-white shadow-sm">
            {{ user.first_name[0] }}{{ user.last_name[0] }}
          </div>
          <div :class="user.active ? 'bg-green-500' : 'bg-gray-400'" class="absolute bottom-0 right-0 w-4 h-4 border-2 border-white rounded-full" />
        </div>
        <div class="ml-4">
          <h1 class="text-3xl font-bold text-gray-900">
            <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/users">Użytkownicy</Link>
            <span class="text-gray-300 font-light mx-2">/</span>
            <span class="text-gray-600">{{ form.first_name }} {{ form.last_name }}</span>
          </h1>
          <div class="text-sm text-gray-500 font-medium">{{ user.email }}</div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button v-if="!user.deleted_at" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
          <icon :name="isActive ? 'check' : 'edit'" class="w-4 h-4 mr-2" />
          {{ isActive ? 'Tryb edycji aktywny' : 'Edytuj dane' }}
        </button>
      </div>
    </div>

    <trashed-message v-if="user.deleted_at" class="mb-6 shadow-sm" @restore="restore">
      Ten użytkownik został zarchiwizowany.
    </trashed-message>

    <div class="max-w-3xl">
      <div id="form-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
        <form @submit.prevent="update">
          <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <text-input v-model="form.first_name" :error="form.errors.first_name" :disabled="disable" label="Imię" />
              <text-input v-model="form.last_name" :error="form.errors.last_name" :disabled="disable" label="Nazwisko" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <text-input v-model="form.email" :error="form.errors.email" :disabled="disable" label="Email" />
              <text-input v-model="form.password" :error="form.errors.password" :disabled="disable" type="password" autocomplete="new-password" label="Hasło (zostaw puste, aby nie zmieniać)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Pole wyboru roli - widoczne tylko dla admina -->
              <select-input v-if="$page.props.auth.user.roles.includes('super-admin')" v-model="form.role" :error="form.errors.role" :disabled="disable" label="Uprawnienia">
                <option :value="null" />
                <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
              </select-input>

              <file-input v-model="form.photo" :error="form.errors.photo" :disabled="disable" type="file" accept="image/*" label="Zdjęcie profilowe" />
            </div>
          </div>

          <!-- Form Actions -->
          <div v-if="isActive" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <button v-if="!user.deleted_at" class="text-rose-600 font-semibold hover:text-rose-800 transition-colors flex items-center" tabindex="-1" type="button" @click="destroy">
              <icon name="trash" class="w-4 h-4 mr-2" />
              Archiwizuj
            </button>

            <div class="flex gap-3 ml-auto">
              <loading-button :loading="form.processing" class="btn-indigo shadow-md px-8" type="submit">
                Zapisz zmiany
              </loading-button>
            </div>
          </div>
        </form>

        <!-- Secondary Actions Bar -->
        <div v-if="!user.deleted_at && !isActive" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-1 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Edytuj dane</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from '@/Shared/Icon.vue'

export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Icon,
  },
  layout: Layout,
  props: {
    user: Object,
    roles: Array,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        role: this.user.role,
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/users/${this.user.id}`, {
        onSuccess: () => {
          this.form.reset('password', 'photo')
          this.disable = true
          this.isActive = false
        },
      })
    },
    destroy() {
      if (confirm('Czy na pewno chcesz zarchiwizować tego użytkownika?')) {
        this.$inertia.delete(`/users/${this.user.id}`)
      }
    },
    restore() {
      if (confirm('Czy na pewno chcesz przywrócić tego użytkownika?')) {
        this.$inertia.put(`/users/${this.user.id}/restore`)
      }
    },
    blockActive() {
      if (confirm('Zablokować konto?')) {
        this.$inertia.post(`/users/${this.user.id}/block`)
      }
    },
    unblockActive() {
      if (confirm('Odblokować konto?')) {
        this.$inertia.post(`/users/${this.user.id}/unblock`)
      }
    },
    disableForm() {
      this.isActive = !this.isActive
      this.disable = !this.disable
    },
  },
}
</script>

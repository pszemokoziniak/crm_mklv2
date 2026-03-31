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
        <button v-if="!user.deleted_at && canEdit" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
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
              <!-- Pole wyboru roli - widoczne tylko dla super-admina i Administratora, ale NIE przy edycji własnego profilu (chyba że jest się super-adminem/adminem, ale tu blokujemy zmianę własnych uprawnień dla bezpieczeństwa lub zgodnie z prośbą) -->
              <select-input v-if="canChangeRole" v-model="form.role" :error="form.errors.role" :disabled="disable" label="Uprawnienia">
                <option :value="null" />
                <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
              </select-input>

              <file-input v-model="form.photo" :error="form.errors.photo" :disabled="disable" type="file" accept="image/*" label="Zdjęcie profilowe" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <checkbox-input v-model="form.preliminarz_email" :error="form.errors.preliminarz_email" :disabled="disable" label="Powiadomienia Preliminarz" description="Czy użytkownik otrzymuje powiadomienia e-mail dla nowych zapytań z opcją PRELIMINARZ - TAK" />
            </div>
          </div>

          <!-- Form Actions -->
          <div v-if="isActive" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <button v-if="!user.deleted_at && (isSuperAdmin || isAdmin)" class="text-rose-600 font-semibold hover:text-rose-800 transition-colors flex items-center" tabindex="-1" type="button" @click="destroy">
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
        <div v-if="!user.deleted_at && !isActive && canEdit" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-1 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Edytuj dane</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Historia zmian (Activity Log) - NA SAMYM DOLE -->
    <div class="mt-12 mb-12 max-w-3xl">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-gray-50/30 cursor-pointer hover:bg-gray-100/50 transition-colors" @click="isHistoryVisible = !isHistoryVisible">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="printer" class="w-6 h-6 fill-amber-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia zmian systemowych</h2>
          </div>
          <icon name="cheveron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': isHistoryVisible }" />
        </div>
        <div v-if="isHistoryVisible" class="p-8">
          <div v-if="filteredActivities && filteredActivities.length > 0" class="flow-root">
            <ul role="list" class="-mb-8">
              <li v-for="(activity, index) in filteredActivities" :key="activity.id">
                <div class="relative pb-8">
                  <span v-if="index !== filteredActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        :class="[
                          activity.description === 'deleted' ? 'bg-red-500' : 'bg-blue-500',
                          'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                        ]"
                      >
                        <icon name="edit" class="w-4 h-4 text-white" />
                      </span>
                    </div>
                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                      <div>
                        <p class="text-sm text-gray-500">
                          <span class="font-medium text-gray-900">{{ activity.user }}</span>
                          {{ activity.description === 'updated' ? 'zaktualizował(a) dane' :
                            activity.description === 'deleted' ? 'usunął/ęła rekord' : activity.description }}
                        </p>
                        <!-- Wyświetlanie zmian -->
                        <div v-if="activity.changes && activity.changes.attributes" class="mt-2 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                          <div v-for="(val, key) in activity.changes.attributes" :key="key" class="mb-1 last:mb-0">
                            <span class="font-bold text-gray-600">{{ key }}:</span>
                            <span v-if="activity.changes.old && activity.changes.old[key]" class="text-red-500 line-through mx-1">{{ activity.changes.old[key] }}</span>
                            <span class="text-green-600 font-medium">{{ val }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="whitespace-nowrap text-right text-sm text-gray-500">
                        <time :datetime="activity.created_at">{{ activity.created_at }}</time>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="text-center py-6 text-gray-400 italic">
            Brak zarejestrowanych zmian.
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
import CheckboxInput from '@/Shared/CheckboxInput'
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
    CheckboxInput,
    Icon,
  },
  layout: Layout,
  props: {
    user: Object,
    roles: Array,
    activities: Array,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      isHistoryVisible: false,
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        role: this.user.role,
        photo: null,
        preliminarz_email: this.user.preliminarz_email,
      }),
    }
  },
  computed: {
    isSuperAdmin() {
      return this.$page.props.auth.user.roles.includes('super-admin')
    },
    isAdmin() {
      return this.$page.props.auth.user.roles.includes('Administrator')
    },
    isOwnProfile() {
      return this.$page.props.auth.user.id === this.user.id
    },
    canEdit() {
      return this.isSuperAdmin || this.isAdmin || this.isOwnProfile
    },
    canChangeRole() {
      // Super-admin zawsze może zmieniać rolę. Administrator może zmieniać rolę, ale nie swoją własną.
      return this.isSuperAdmin || (this.isAdmin && !this.isOwnProfile)
    },
    filteredActivities() {
      if (!this.activities) {
        return []
      }
      return this.activities.filter(activity => activity.description !== 'created')
    },
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

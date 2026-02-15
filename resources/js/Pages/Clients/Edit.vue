<template>
  <div>
    <Head :title="`${form.nazwa}`" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div class="flex items-center">
        <div class="ml-4">
          <h1 class="text-3xl font-bold text-gray-900">
            <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/clients">Klienci</Link>
            <span class="text-gray-300 font-light mx-2">/</span>
            <span class="text-gray-600">{{ form.nazwa }}</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button v-if="!client.deleted_at" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
          <icon :name="isActive ? 'check' : 'edit'" class="w-4 h-4 mr-2" />
          {{ isActive ? 'Tryb edycji aktywny' : 'Edytuj dane' }}
        </button>
      </div>
    </div>

    <trashed-message v-if="client.deleted_at" class="mb-6 shadow-sm" @restore="restore">
      Klient został usunięty.
    </trashed-message>

    <div class="max-w-3xl">
      <div id="form-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
        <form @submit.prevent="update">
          <div class="flex flex-wrap -mb-8 -mr-6 p-8">
            <text-input v-model="form.nazwa" :error="form.errors.nazwa" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />
            <text-input v-model="form.ulica" :error="form.errors.ulica" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Ulica" />
            <text-input v-model="form.miasto" :error="form.errors.miasto" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Miasto" />
            <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
              <option :value="null" />
              <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select-input>
            <div class="pb-8 pr-6 w-full lg:w-1/2">
              <div class="flex items-center justify-between">
                <label class="form-label">WWW:</label>
                <a v-if="form.www" :href="formatUrl(form.www)" target="_blank" class="text-indigo-600 hover:underline text-sm mb-1">Otwórz &rarr;</a>
              </div>
              <text-input v-model="form.www" :error="form.errors.www" :disabled="disable" />
            </div>
            <div class="pb-8 pr-6 w-full lg:w-1/2">
              <div class="flex items-center justify-between">
                <label class="form-label">LinkedIn:</label>
                <a v-if="form.linkedIn" :href="formatUrl(form.linkedIn)" target="_blank" class="text-indigo-600 hover:underline text-sm mb-1">Otwórz &rarr;</a>
              </div>
              <text-input v-model="form.linkedIn" :error="form.errors.linkedIn" :disabled="disable" />
            </div>
            <select-input v-model="form.branza_id" :error="form.errors.branza_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Branża">
              <option :value="null" />
              <option v-for="item in branza" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select-input>
            <select-input
              v-model="form.user_id" :error="form.errors.user_id" :disabled="disable"
              class="pb-8 pr-6 w-full lg:w-1/2" label="Opiekun"
            >
              <option :value="null" />
              <option v-for="item in user" :key="item.id" :value="item.id">{{ item.first_name }} {{ item.last_name }}</option>
            </select-input>
            <text-area-input v-model="form.message" :error="form.errors.message" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Informacje" />
          </div>

          <!-- Form Actions -->
          <div v-if="isActive" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <archive-button v-if="!client.deleted_at" @click="destroy" />
            <div class="flex gap-3 ml-auto">
              <loading-button :loading="form.processing" class="btn-indigo shadow-md px-8" type="submit">
                Zapisz zmiany
              </loading-button>
            </div>
          </div>
        </form>

        <!-- Secondary Actions Bar -->
        <div v-if="!client.deleted_at && !isActive" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-3 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Edytuj dane</span>
            </button>
            <Link class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" :href="`/kontaktperson/${client_id}/index`">
              <icon name="addPerson" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Osoby kontaktowe</span>
            </Link>
            <Link class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" :href="`/kontakt/${client_id}/index`">
              <icon name="addContact" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Kontakty</span>
            </Link>
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
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from '@/Shared/Icon.vue'
import ArchiveButton from '@/Shared/ArchiveButton.vue'

export default {
  components: {
    Icon,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    TextAreaInput,
    ArchiveButton,
  },
  layout: Layout,
  props: {
    client: Object,
    branza: Object,
    kraj: Object,
    user: Object,
    // eslint-disable-next-line vue/prop-name-casing
    client_id: String,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      form: this.$inertia.form({
        nazwa: this.client.nazwa,
        ulica: this.client.ulica,
        miasto: this.client.miasto,
        www: this.client.www,
        linkedIn: this.client.linkedIn,
        waluta: this.client.waluta,
        message: this.client.message,
        user_id: this.client.user_id,
        branza_id: this.client.branza_id,
        kraj_id: this.client.kraj_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/clients/${this.client.id}`, {
        onSuccess: () => {
          this.disable = true
          this.isActive = false
        },
      })
    },
    destroy() {
      if (confirm('Czy chcesz usunąć tego klienta?')) {
        this.$inertia.delete(`/clients/${this.client.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić klienta?')) {
        this.$inertia.put(`/clients/${this.client.id}/restore`)
      }
    },
    disableForm() {
      this.isActive = !this.isActive
      this.disable = !this.disable
    },
    contactPerson() {
      this.form.get(`/kontaktperson/${this.client.id}/index`)
    },
    formatUrl(url) {
      if (!url) return ''
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url
      }
      return `https://${url}`
    },
  },
}
</script>

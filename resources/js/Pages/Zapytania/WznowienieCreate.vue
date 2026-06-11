<template>
  <div>
    <Head :title="`Dodaj wznowienie dla ${zapytania.nazwa_projektu}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/zapytania/${zapytania.id}/edit`">Zapytania</Link>
      <span class="text-indigo-400 font-medium">/</span> Dodaj wznowienie
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-area v-model="form.text" :error="form.errors.text" class="pb-8 pr-6 w-full" label="Opis wznowienia" />
          <select-input v-model="form.id_user" :error="form.errors.id_user" class="pb-8 pr-6 w-full lg:w-1/2" label="Użytkownik">
            <option :value="null" />
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.last_name }} {{ user.first_name }}</option>
          </select-input>

          <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data otrzymania" />
          <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Termin złożenia" />

          <select-input v-model="form.preliminarz" :error="form.errors.preliminarz" class="pb-8 pr-6 w-full lg:w-1/2" label="Preliminarz">
            <option value="Tak">Tak</option>
            <option value="Nie">Nie</option>
          </select-input>
          <select-input v-model="form.zakres_id" :error="form.errors.zakres_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zakres">
            <option :value="null" />
            <option v-for="item in zakres" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>

          <select-input v-model="form.user_opracowuje_id" :error="form.errors.user_opracowuje_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Opracowuje">
            <option :value="null" />
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.last_name }} {{ user.first_name }}</option>
          </select-input>

          <text-input v-model="form.start" :error="form.errors.start" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany start" />
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany koniec" />

          <number-input v-model="form.kwota" :error="form.errors.kwota" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Dodaj wznowienie</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TextArea from '@/Shared/TextareaInput.vue'
import TextInput from '@/Shared/TextInput.vue' // Import TextInput
import NumberInput from '@/Shared/NumberInput.vue' // Import NumberInput

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextArea,
    TextInput, // Register TextInput
    NumberInput, // Register NumberInput
  },
  layout: Layout,
  props: {
    zapytania: Object,
    users: Array,
    zakres: Array, // Add zakres prop
    waluta: Array, // Add waluta prop
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        text: null,
        id_zapytania: this.zapytania.id,
        id_user: this.$page.props.auth.user.id, // Default to current user
        data_otrzymania: null,
        data_zlozenia: null,
        preliminarz: null,
        zakres_id: null,
        user_opracowuje_id: null,
        start: null,
        end: null,
        kwota: null,
        waluta_id: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(`/zapytania/${this.zapytania.id}/storeWznowienie`)
    },
  },
}
</script>

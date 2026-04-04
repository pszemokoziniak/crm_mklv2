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
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.first_name }} {{ user.last_name }}</option>
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

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextArea,
  },
  layout: Layout,
  props: {
    zapytania: Object,
    users: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        text: null,
        id_zapytania: this.zapytania.id,
        id_user: this.$page.props.auth.user.id, // Default to current user
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

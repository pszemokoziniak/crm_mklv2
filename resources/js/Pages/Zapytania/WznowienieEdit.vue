<template>
  <div>
    <Head :title="`Edytuj wznowienie dla ${zapytania.nazwa_projektu}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/zapytania/${zapytania.id}/edit`">Zapytania</Link>
      <span class="text-indigo-400 font-medium">/</span>
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/zapytania/${zapytania.id}/edit`">{{ zapytania.nazwa_projektu }}</Link>
      <span class="text-indigo-400 font-medium">/</span> Wznowienie {{ wznowienie.created_at || wznowienie.id }}
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
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
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń wznowienie</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Aktualizuj wznowienie</loading-button>
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
import TextInput from '@/Shared/TextInput.vue'
import NumberInput from '@/Shared/NumberInput.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextArea,
    TextInput,
    NumberInput,
  },
  layout: Layout,
  props: {
    zapytania: Object,
    wznowienie: Object, // Prop for the specific wznowienie being edited
    users: Array,
    zakres: Array,
    waluta: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        text: this.wznowienie.text,
        id_zapytania: this.wznowienie.id_zapytania,
        id_user: this.wznowienie.id_user,
        data_otrzymania: this.wznowienie.data_otrzymania,
        data_zlozenia: this.wznowienie.data_zlozenia,
        preliminarz: this.wznowienie.preliminarz,
        zakres_id: this.wznowienie.zakres_id,
        user_opracowuje_id: this.wznowienie.user_opracowuje_id,
        start: this.wznowienie.start,
        end: this.wznowienie.end,
        kwota: this.wznowienie.kwota,
        waluta_id: this.wznowienie.waluta_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/zapytania/${this.zapytania.id}/wznowienia/${this.wznowienie.id}`)
    },
    destroy() {
      if (confirm('Czy na pewno chcesz usunąć to wznowienie?')) {
        this.$inertia.delete(`/zapytania/${this.zapytania.id}/wznowienia/${this.wznowienie.id}`)
      }
    },
  },
}
</script>

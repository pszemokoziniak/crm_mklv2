<template>
  <div>
    <Head :title="`${form.nazwa_projektu}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zapytania">Zapytanie</Link>
      <span class="text-indigo-400 font-medium"></span>
      {{ form.id_zapyt }}
    </h1>
    <form @submit.prevent="submit" action="pdf" ref="form">
      <div class="pb-5">
        <input type='hidden' name="param" :value="`${zapytania.id}`" class="px-2 ml-2 rounded-lg border">
        <button type="submit" class="btn-indigo">Generuj PDF</button>
      </div>
    </form>
    <trashed-message v-if="zapytania.deleted_at" class="mb-6" @restore="restore"> Zapytanie zostało usunięte </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.user_otrzymal_id" :error="form.errors.user_otrzymal_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Otrzymał">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data otrzymania" />
          <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin złożenia" />
          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa projektu" />
          <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowść" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.zakres_id" :error="form.errors.zakres_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zakres">
            <option :value="null" />
            <option v-for="item in zakres" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.user_opracowuje_id" :error="form.errors.user_opracowuje_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Opracowuje">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.start" :error="form.errors.start" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin rozpoczęcia" />
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin zakończenia realizacji" />
          <number-input v-model="form.kwota" :error="form.errors.kwota" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta" :error="form.errors.waluta" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.waluta }}</option>
          </select-input>
          <text-area v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!zapytania.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Popraw</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import NumberInput from '@/Shared/NumberInput.vue'
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextArea from "@/Shared/TextareaInput.vue";

export default {
  components: {
    TextArea,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    TextAreaInput,
    NumberInput,
  },
  layout: Layout,
  props: {
    zapytania: Object,
    clients: Object,
    branzas: Object,
    krajs: Object,
    users: Object,
    zakres: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id_zapyt: this.zapytania.id_zapyt,
        user_otrzymal_id: this.zapytania.user_otrzymal_id,
        data_otrzymania: this.zapytania.data_otrzymania,
        data_zlozenia: this.zapytania.data_zlozenia,
        client_id: this.zapytania.client_id,
        nazwa_projektu: this.zapytania.nazwa_projektu,
        miejscowosc: this.zapytania.miejscowosc,
        kraj_id: this.zapytania.kraj_id,
        zakres_id: this.zapytania.zakres_id,
        user_opracowuje_id: this.zapytania.user_opracowuje_id,
        start: this.zapytania.start,
        end: this.zapytania.end,
        kwota: this.zapytania.kwota,
        waluta: this.zapytania.waluta,
        opis: this.zapytania.opis,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/zapytania/${this.zapytania.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz usunąć te zapytanie?')) {
        this.$inertia.delete(`/zapytania/${this.zapytania.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić zapytanie?')) {
        this.$inertia.put(`/zapytania/${this.zapytania.id}/restore`)
      }
    },
    submit: function(){
      this.$refs.form.submit()
    },
  },
}
</script>

<template>
  <div>
    <Head title="Edycja oferty" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/oferta">Oferty</Link>
      <span class="text-indigo-400 font-medium">/</span> Edycja
    </h1>
    <trashed-message v-if="oferta.deleted_at" class="mb-6" @restore="restore"> Oferta została usunięta </trashed-message>

    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <!-- Nagłówek z linkami do relacji -->
      <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex flex-wrap gap-2 items-center text-lg font-semibold">
        <Link v-if="zapytaniaById" class="text-indigo-600 hover:underline" :href="`/zapytania/${oferta.zapytania_id}/edit`">
          {{ zapytaniaById.nazwa_projektu }}
        </Link>
        <span class="text-gray-400">/</span>
        <Link v-if="clientById" class="text-indigo-600 hover:underline" :href="`/clients/${oferta.client_id}/edit`">
          {{ clientById.nazwa }}
        </Link>
      </div>

      <form :class="{ 'border-t-4 border-indigo-500': !disable }" @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Zapytanie">
            <option v-for="item in zapytanie" :key="item.id" :value="item.id">{{ item.nazwa_projektu }} </option>
          </select-input>

          <select-input v-model="form.typ" :error="form.errors.typ" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Typ">
            <option :value="null" />
            <option value="Klient oferuje">Klient oferuje</option>
            <option value="Klienta ma kontakt">Klienta ma kontakt</option>
          </select-input>

          <select-input v-model="form.client_id" :error="form.errors.client_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }} </option>
          </select-input>

          <text-input v-model="form.data_wyslania" :error="form.errors.data_wyslania" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wysłania" />

          <number-input v-model="form.kwota" :error="form.errors.kwota" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />

          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>

          <text-input v-model="form.data_kontakt" :error="form.errors.data_kontakt" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />

          <select-input v-model="form.oferta_status_id" :error="form.errors.oferta_status_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option v-for="item in statuses" :key="item.id" :value="item.id">{{ item.name }} </option>
          </select-input>

          <text-area-input v-model="form.opis" :error="form.errors.opis" :disabled="disable" class="pb-8 pr-6 w-full" label="Opis" />
        </div>

        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!oferta.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>

          <div class="ml-auto flex items-center gap-4">
            <button v-if="disable && !oferta.deleted_at" class="btn-indigo bg-gray-600 hover:bg-gray-700" type="button" @click="enableForm">
              <icon name="edit" class="mr-2 w-4 h-4 fill-white inline" />
              Edytuj
            </button>
            <loading-button v-if="!disable" :loading="form.processing" class="btn-indigo" type="submit">Zapisz zmiany</loading-button>
          </div>
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
import Icon from '@/Shared/Icon.vue'

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
    NumberInput,
  },
  layout: Layout,
  props: {
    oferta: Object,
    zapytanie: Object,
    clients: Object,
    statuses: Object,
    clientById: Object,
    zapytaniaById: Object,
    waluta: Object,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      form: this.$inertia.form({
        id: this.oferta.id,
        zapytania_id: this.oferta.zapytania_id,
        typ: this.oferta.typ,
        client_id: this.oferta.client_id,
        data_wyslania: this.oferta.data_wyslania,
        kwota: this.oferta.kwota,
        waluta_id: this.oferta.waluta_id,
        data_kontakt: this.oferta.data_kontakt,
        oferta_status_id: this.oferta.oferta_status_id,
        opis: this.oferta.opis,
        user_id: this.oferta.user_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/oferta/${this.oferta.id}`, {
        onSuccess: () => this.disable = true,
      })
    },
    destroy() {
      if (confirm('Czy na pewno chcesz zarchiwizować tę ofertę?')) {
        this.$inertia.delete(`/oferta/${this.oferta.id}`)
      }
    },
    restore() {
      if (confirm('Czy chcesz przywrócić tę ofertę?')) {
        this.$inertia.put(`/oferta/${this.oferta.id}/restore`)
      }
    },
    enableForm() {
      this.disable = false
    },
  },
}
</script>

<template>
  <div>
    <Head title="Popraw ofertę" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/oferta">Oferty</Link>
      <span class="text-indigo-400 font-medium">/</span> Popraw
    </h1>
    <trashed-message v-if="oferta.deleted_at" class="mb-6" @restore="restore"> Oferta została usunięta </trashed-message>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <dix class="text-2xl font-bold border-1 border-green text-red-800 -mr-6 p-8">
        <Link class="text-indigo-400 hover:text-indigo-600" :href="`/zapytania/${oferta.zapytania_id}/edit`">{{zapytaniaById.nazwa_projektu}}</Link>
        /
        <Link class="text-indigo-400 hover:text-indigo-600" :href="`/clients/${oferta.client_id}/edit`">{{clientById.nazwa}}</Link>
      </dix>
      <form @submit.prevent="update" :class=" (isActive) ? 'border-2 border-green-500' : ''">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Zapytanie">
            <option v-for="item in zapytanie" :key="item.id" :value="item.id">{{ item.nazwa_projektu }} </option>
          </select-input>
          <select-input v-model="form.typ" :error="form.errors.typ" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Typ klienta">
            <option :value="null" />
            <option :value="'Klient oferuje'">Klient oferuje</option>
            <option :value="'Klienta ma kontakt'">Klienta ma kontakt</option>
          </select-input>
          <select-input v-model="form.client_id" :error="form.errors.client_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }} </option>
          </select-input>
          <text-input v-model="form.data_wyslania" :error="form.errors.data_wyslania" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Ofertę wysłano" />
          <number-input v-model="form.kwota" :error="form.errors.kwota" :disabled="disable"  class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" :disabled="disable"  class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <text-input v-model="form.data_kontakt" :error="form.errors.data_kontakt" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <select-input v-model="form.oferta_status_id" :error="form.errors.oferta_status_id" :disabled="disable"  class="pb-8 pr-6 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option v-for="item in statuses" :key="item.id" :value="item.id">{{ item.name }} </option>
          </select-input>
          <text-area v-model="form.opis" :error="form.errors.opis" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <hr>
        <div class="grid gap-1 grid-cols-3 p-5">
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <icon name="edit" class="mr-2 w-4 h-4 inline"/>
            <button v-if="!oferta.deleted_at" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="disableForm">Edytuj dane</button>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!oferta.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
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
import Icon from "@/Shared/Icon.vue";

export default {
  components: {
    Icon,
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
    oferta: Object,
    zapytanie: Object,
    clients: Object,
    branzas: Object,
    krajs: Object,
    users: Object,
    zakres: Object,
    statuses: Object,
    clientById: Object,
    zapytaniaById: Object,
    waluta: Object,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
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
        user_id: this.oferta.user_id
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/oferta/${this.oferta.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz usunąć te zapytanie?')) {
        this.$inertia.delete(`/oferta/${this.oferta.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić ofertę?')) {
        this.$inertia.put(`/oferta/${this.oferta.id}/restore`)
      }
    },
    disableForm() {
      this.isActive=true
      let elems_input = document.getElementById('form').getElementsByTagName('input');
      for(let i = 0; i < elems_input.length; i++) {
        elems_input[i].disabled = false;
      }
      let elems_select = document.getElementById('form').getElementsByTagName('select');
      for(let i = 0; i < elems_select.length; i++) {
        elems_select[i].disabled = false;
      }
      let elems_text_area = document.getElementById('form').getElementsByTagName('textarea');
      for(let i = 0; i < elems_text_area.length; i++) {
        elems_text_area[i].disabled = false;
      }
    },
  },
}
</script>

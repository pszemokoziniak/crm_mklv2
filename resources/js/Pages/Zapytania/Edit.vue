<template>
  <div>
    <Head :title="`${form.nazwa_projektu}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zapytania">Zapytanie</Link>
      <span class="text-indigo-400 font-medium"></span>
      {{ form.id_zapyt }}
    </h1>

    <trashed-message v-if="zapytania.deleted_at" class="mb-6" @restore="restore"> Zapytanie zostało usunięte </trashed-message>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <dix class="text-2xl font-bold border-1 border-green text-red-800 -mr-6 p-8">
          {{zapytania.nazwa_projektu}} /
          <Link class="text-indigo-400 hover:text-indigo-600" :href="`/clients/${zapytania.client_id}/edit`">{{clientById.nazwa}}</Link>
      </dix>
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.user_otrzymal_id" :error="form.errors.user_otrzymal_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Otrzymał">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data otrzymania" />
          <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin złożenia" />
          <select-input v-model="form.client_id" :error="form.errors.client_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa projektu" />
          <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowść" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.zakres_id" :error="form.errors.zakres_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Zakres">
            <option :value="null" />
            <option v-for="item in zakres" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.user_opracowuje_id" :error="form.errors.user_opracowuje_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Opracowuje">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.start" :error="form.errors.start" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin rozpoczęcia" />
          <text-input v-model="form.end" :error="form.errors.end" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin zakończenia realizacji" />
          <number-input v-model="form.kwota" :error="form.errors.kwota" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta" :error="form.errors.waluta" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.waluta }}</option>
          </select-input>
          <text-area v-model="form.opis" :error="form.errors.opis" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <hr>
        <div class="grid gap-1 grid-cols-3 p-5">
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <icon name="edit" class="mr-2 w-4 h-4 inline"/>
            <button v-if="!zapytania.deleted_at" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="disableForm">Edytuj dane</button>
          </div>
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <form @submit.prevent="submit" action="pdf" ref="form">
              <input type='hidden' name="param" :value="`${zapytania.id}`">
              <icon name="edit" class="mr-2 w-4 h-4 inline"/>
              <button type="submit" class="text-indigo-600 hover:underline ml-auto" tabindex="-1">Generuj PDF</button>
            </form>
          </div>
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <icon name="mail" class="mr-2 w-4 h-4 inline"/>
            <button v-if="!zapytania.deleted_at" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="mail">Wyślij mail</button>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!zapytania.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
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
    zapytania: Object,
    clients: Object,
    branzas: Object,
    krajs: Object,
    users: Object,
    zakres: Object,
    clientById: Object,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
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
    mail() {
      this.form.get(`/zapytania/${this.zapytania.id}/mail`)
    },
    disableForm() {
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

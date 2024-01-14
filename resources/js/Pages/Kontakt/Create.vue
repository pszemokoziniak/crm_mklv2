<template>
  <div>
    <Head title="Dodaj kontakt" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/kontakt/${client_id}/index`">Kontakty</Link>
      <span class="text-indigo-400 font-medium">/</span> Dodaj
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <div class="-mb-8 -mr-6 p-8">
        <h1 class="max-w-3xl font-bold">Osoba kontaktowa:</h1>
        <p class="py-3">Nazwisko: {{kontaktPerson.last_name}}</p>
        <p class="pb-3">Imię: {{kontaktPerson.first_name}}</p>
        <p class="pb-3">Telefon: <span class="font-bold"> {{kontaktPerson.phone1}} {{kontaktPerson.phone2}}</span></p>
      </div>
    </div>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full lg:w-1/2" label="Temat" />
          <TextareaInput v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
          <text-input v-model="form.call_time" :error="form.errors.call_time" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zapytanie">
            <option :value="null" />
            <option v-for="item in zapytanias" :key="item.id" :value="item.id">{{ item.nazwa_projektu }}</option>
          </select-input>
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Contact</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TextareaInput from "@/Shared/TextareaInput.vue";
import Icon from "@/Shared/Icon.vue";
export default {
  components: {
    Icon,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  layout: Layout,
  props: {
    zapytanias: Object,
    client_id: String,
    kontaktPersons: Object,
    kontaktPerson: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        subject: '',
        description: '',
        call_time: '',
        // phone: '',
        zapytania_id: '',
        kontakt_person_id: this.kontaktPerson.id,
        client_id: this.client_id,
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/kontakt/post/'+this.client_id)
    },
  },
}
</script>

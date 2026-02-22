<template>
  <div>
    <Head title="Dodaj kontakt" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/kontakt">Kontakty</Link>
      <span class="text-indigo-400 font-medium">/</span>
      <span v-if="form.parent_id">Odpowiedz w wątku</span>
      <span v-else>Nowy kontakt</span>
    </h1>

    <div class="max-w-4xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <!-- 1. Wybór Klienta -->
          <select-input v-if="!parent_id" v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full" label="Klient" @change="fetchClientData">
            <option :value="null">Wybierz klienta</option>
            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.nazwa }}</option>
          </select-input>
          <div v-else class="pb-8 pr-6 w-full">
            <label class="form-label">Klient:</label>
            <div class="mt-2 font-semibold text-gray-700">{{ client?.nazwa }}</div>
          </div>

          <!-- 2. Wybór Wątku -->
          <div v-if="form.client_id && !parent_id && existingTopics.length > 0" class="pb-8 pr-6 w-full">
            <label class="form-label mb-2 block">Typ wpisu:</label>
            <div class="flex gap-4">
              <label class="flex items-center cursor-pointer">
                <input v-model="entryType" type="radio" value="new" class="form-radio text-indigo-600" @change="resetParent" />
                <span class="ml-2 text-sm text-gray-700">Nowy temat</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input v-model="entryType" type="radio" value="reply" class="form-radio text-indigo-600" />
                <span class="ml-2 text-sm text-gray-700">Kontynuacja istniejącego wątku</span>
              </label>
            </div>
          </div>

          <!-- 3. Wybór istniejącego tematu -->
          <select-input v-if="entryType === 'reply' && !parent_id" v-model="form.parent_id" :error="form.errors.parent_id" class="pb-8 pr-6 w-full" label="Wybierz wątek" @change="syncSubject">
            <option :value="null">Wybierz temat rozmowy...</option>
            <option v-for="topic in existingTopics" :key="topic.id" :value="topic.id">{{ topic.subject }}</option>
          </select-input>

          <!-- 4. Temat -->
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full" label="Temat rozmowy" :disabled="entryType === 'reply' || !!parent_id" placeholder="O czym rozmawialiście?" />

          <!-- 5. Opis -->
          <textarea-input v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full" label="Szczegóły rozmowy / Notatki" />

          <!-- 6. Osoba kontaktowa, Zapytanie, Oferta -->
          <select-input v-model="form.kontakt_person_id" :error="form.errors.kontakt_person_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Osoba kontaktowa">
            <option :value="null">Brak</option>
            <option v-for="person in localKontaktPersons" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select-input>

          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Zapytanie">
            <option :value="null">Brak</option>
            <option v-for="item in localZapytanias" :key="item.id" :value="item.id">{{ item.nazwa_projektu }}</option>
          </select-input>

          <select-input v-model="form.oferta_id" :error="form.errors.oferta_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Oferta">
            <option :value="null">Brak</option>
            <option v-for="item in localOfertas" :key="item.id" :value="item.id">{{ item.label }}</option>
          </select-input>

          <!-- Opiekun -->
          <select-input v-model="form.opiekun_id" :error="form.errors.opiekun_id" class="pb-8 pr-6 w-full" label="Opiekun (osoba dedykowana)">
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select-input>

          <!-- 7. Data i Czas Kontaktu -->
          <text-input v-model="form.call_date" :error="form.errors.call_date" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <text-input v-model="form.call_time" :error="form.errors.call_time" type="time" class="pb-8 pr-6 w-full lg:w-1/2" label="Godzina kontaktu" />

          <!-- 8. Następny Kontakt -->
          <text-input v-model="form.next_call_date" :error="form.errors.next_call_date" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data następnego kontaktu" />
          <text-input v-model="form.next_call_time" :error="form.errors.next_call_time" type="time" class="pb-8 pr-6 w-full lg:w-1/2" label="Godzina następnego kontaktu" />
        </div>

        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz wpis</loading-button>
        </div>
      </form>
    </div>

    <!-- Szybki podgląd wybranej osoby -->
    <div v-if="selectedPerson" class="mt-8 max-w-4xl bg-indigo-50 rounded-md p-6 border border-indigo-100">
      <h2 class="font-bold text-indigo-900 mb-2">Dane osoby kontaktowej:</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <p><span class="text-indigo-400">Nazwisko:</span> {{ selectedPerson.last_name }}</p>
        <p><span class="text-indigo-400">Imię:</span> {{ selectedPerson.first_name }}</p>
        <p><span class="text-indigo-400">Telefon:</span> {{ selectedPerson.phone1 }} {{ selectedPerson.phone2 }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TextareaInput from '@/Shared/TextareaInput.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextareaInput,
  },
  layout: Layout,
  props: {
    clients: Array,
    users: Array,
    zapytanias: Array,
    ofertas: Array,
    client_id: [String, Number],
    client: Object,
    kontaktPersons: Array,
    existingTopics: Array,
    selected_kontakt_person_id: [String, Number],
    parent_id: [String, Number],
    parent_subject: String,
    selected_zapytania_id: [String, Number],
    selected_oferta_id: [String, Number],
    selected_opiekun_id: [String, Number],
  },
  remember: 'form',
  data() {
    const now = new Date()
    const currentDate = now.toISOString().split('T')[0]
    const currentTime = now.toTimeString().split(' ')[0].substring(0, 5)

    return {
      entryType: this.parent_id ? 'reply' : 'new',
      localZapytanias: this.zapytanias || [],
      localOfertas: this.ofertas || [],
      localKontaktPersons: this.kontaktPersons || [],
      form: this.$inertia.form({
        subject: this.parent_subject || '',
        description: '',
        call_date: currentDate,
        call_time: currentTime,
        next_call_date: '',
        next_call_time: '',
        zapytania_id: this.selected_zapytania_id || null,
        oferta_id: this.selected_oferta_id || null,
        kontakt_person_id: this.selected_kontakt_person_id || null,
        client_id: this.client_id || null,
        parent_id: this.parent_id || null,
        opiekun_id: this.selected_opiekun_id || null,
      }),
    }
  },
  methods: {
    fetchClientData() {
      if (!this.form.client_id) {
        this.localZapytanias = []
        this.localOfertas = []
        this.localKontaktPersons = []
        return
      }

      this.$inertia.get('/kontakt/create', { client: this.form.client_id }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: (page) => {
          this.localZapytanias = page.props.zapytanias
          this.localOfertas = page.props.ofertas
          this.localKontaktPersons = page.props.kontaktPersons
          this.form.client_id = page.props.client_id
        },
      })
    },
    resetParent() {
      this.form.parent_id = null
      this.form.subject = ''
    },
    syncSubject() {
      const topic = this.existingTopics.find(t => t.id == this.form.parent_id)
      if (topic) {
        this.form.subject = topic.subject
      }
    },
    store() {
      this.form.post('/kontakt/post')
    },
  },
}
</script>

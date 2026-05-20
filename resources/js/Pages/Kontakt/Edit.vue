<template>
  <div>
    <Head :title="`Wątek: ${kontakt.subject}`" />
    <h1 class="mb-6 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/kontakt">Kontakty</Link>
      <span class="text-indigo-400 font-medium">/</span>
      <span>{{ kontakt.subject }}</span>
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-800">Wpisy w wątku ({{ entries.length }})</h2>
          <Link :href="`/kontakt/create?parent_id=${kontakt.id}`" class="btn-indigo">
            Dodaj kolejny wpis
          </Link>
        </div>

        <div v-if="entries.length === 0" class="bg-gray-50 p-8 text-center rounded-lg border-2 border-dashed border-gray-200 text-gray-500">
          Brak wpisów w tym wątku.
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="entry in entries"
            :key="entry.id"
            class="bg-white p-6 rounded-lg shadow-sm border-l-4"
            :class="entry.id === kontakt.id ? 'border-indigo-500' : 'border-indigo-300'"
          >
            <template v-if="editingId !== entry.id">
              <div class="flex justify-between items-start mb-2">
                <div class="text-sm text-gray-500 flex items-center flex-wrap">
                  <span class="font-bold text-gray-700">
                    {{ entry.user ? `${entry.user.first_name} ${entry.user.last_name}` : '—' }}
                  </span>
                  <span v-if="entry.opiekun && entry.opiekun.id !== entry.user_id" class="ml-2 text-indigo-600">
                    (Przekazano do: {{ entry.opiekun.first_name }} {{ entry.opiekun.last_name }})
                  </span>
                  <span class="mx-2">•</span>
                  <span>{{ entry.call_date }} {{ entry.call_time }}</span>
                  <span v-if="entry.contact_type" class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs uppercase font-bold flex items-center">
                    <template v-if="entry.contact_type === 'telefon'"><icon name="phone" class="w-3 h-3 mr-1" /></template>
                    <template v-else-if="entry.contact_type === 'email'"><icon name="mail" class="w-3 h-3 mr-1" /></template>
                    <template v-else-if="entry.contact_type === 'osobisty'"><icon name="contact" class="w-3 h-3 mr-1" /></template>
                    {{ entry.contact_type }}
                  </span>
                  <span v-if="entry.id === kontakt.id" class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs uppercase font-bold">
                    Wątek główny
                  </span>
                </div>
                <button type="button" @click="startEdit(entry)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                  Edytuj
                </button>
              </div>
              <div class="text-gray-700 whitespace-pre-wrap">{{ entry.description }}</div>
              <div v-if="entry.next_call_date" class="mt-3 text-xs font-semibold text-indigo-600">
                Następny kontakt: {{ entry.next_call_date }} {{ entry.next_call_time }}
              </div>
            </template>

            <form v-else @submit.prevent="update(entry)">
              <div class="flex flex-wrap -mb-4 -mr-4">
                <text-input v-model="form.subject" :error="form.errors.subject" class="pb-4 pr-4 w-full lg:w-3/4" label="Temat" :disabled="entry.id !== kontakt.id" />
                <select-input v-model="form.contact_type" :error="form.errors.contact_type" class="pb-4 pr-4 w-full lg:w-1/4" label="Typ kontaktu">
                  <option :value="null">Wybierz...</option>
                  <option value="telefon">Telefon</option>
                  <option value="email">Email</option>
                  <option value="osobisty">Osobisty</option>
                </select-input>
                <textarea-input v-model="form.description" :error="form.errors.description" class="pb-4 pr-4 w-full" label="Opis / Notatki" />
                <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-4 pr-4 w-full lg:w-1/2" label="Klient">
                  <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.nazwa }}</option>
                </select-input>
                <select-input v-model="form.kontakt_person_id" :error="form.errors.kontakt_person_id" class="pb-4 pr-4 w-full lg:w-1/2" label="Osoba kontaktowa">
                  <option :value="null">Brak</option>
                  <option v-for="person in kontaktPersons" :key="person.id" :value="person.id">
                    {{ person.first_name }} {{ person.last_name }}
                  </option>
                </select-input>
                <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-4 pr-4 w-full lg:w-1/3" label="Zapytanie">
                  <option :value="null">Brak</option>
                  <option v-for="item in zapytanias" :key="item.id" :value="item.id">{{ item.nazwa_projektu }}</option>
                </select-input>
                <select-input v-model="form.oferta_id" :error="form.errors.oferta_id" class="pb-4 pr-4 w-full lg:w-1/3" label="Oferta">
                  <option :value="null">Brak</option>
                  <option v-for="item in ofertas" :key="item.id" :value="item.id">{{ item.label }}</option>
                </select-input>
                <select-input v-model="form.future_project_id" :error="form.errors.future_project_id" class="pb-4 pr-4 w-full lg:w-1/3" label="Przyszły projekt">
                  <option :value="null">Brak</option>
                  <option v-for="item in futureProjects" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
                </select-input>
                <select-input v-model="form.opiekun_id" :error="form.errors.opiekun_id" class="pb-4 pr-4 w-full" label="Opiekun (osoba dedykowana)">
                  <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select-input>
                <text-input v-model="form.call_date" :error="form.errors.call_date" type="date" class="pb-4 pr-4 w-full lg:w-1/2" label="Data kontaktu" />
                <text-input v-model="form.call_time" :error="form.errors.call_time" type="time" class="pb-4 pr-4 w-full lg:w-1/2" label="Godzina kontaktu" />
                <text-input v-model="form.next_call_date" :error="form.errors.next_call_date" type="date" class="pb-4 pr-4 w-full lg:w-1/2" label="Data następnego kontaktu" />
                <text-input v-model="form.next_call_time" :error="form.errors.next_call_time" type="time" class="pb-4 pr-4 w-full lg:w-1/2" label="Godzina następnego kontaktu" />
              </div>
              <div class="flex items-center mt-4 pt-4 border-t border-gray-100">
                <button type="button" class="text-red-600 hover:underline mr-4" @click="destroy(entry)">Usuń</button>
                <button type="button" class="text-gray-600 hover:underline" @click="cancelEdit">Anuluj</button>
                <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zapisz zmiany</loading-button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div v-if="client" class="bg-white rounded-md shadow p-6 mb-8">
          <h3 class="text-lg font-bold mb-4 border-b pb-2">Informacje o kliencie</h3>
          <div class="space-y-3">
            <div>
              <span class="text-xs text-gray-500 uppercase font-bold">Nazwa:</span>
              <div class="font-medium">
                <Link :href="`/clients/${client.id}/edit`" class="text-indigo-600 hover:underline">{{ client.nazwa }}</Link>
              </div>
            </div>
            <div v-if="client.adres">
              <span class="text-xs text-gray-500 uppercase font-bold">Adres:</span>
              <div class="text-sm">{{ client.adres }}</div>
            </div>
            <div v-if="client.nip">
              <span class="text-xs text-gray-500 uppercase font-bold">NIP:</span>
              <div class="text-sm">{{ client.nip }}</div>
            </div>
          </div>
        </div>

        <div class="bg-indigo-50 rounded-md shadow p-6">
          <h3 class="text-indigo-900 font-bold mb-2">Szybkie akcje</h3>
          <div class="space-y-2">
            <Link v-if="client" :href="`/kontakt/create?client=${client.id}`" class="block text-sm text-indigo-700 hover:underline">Nowy kontakt dla tego klienta</Link>
            <Link :href="`/kontakt/create?parent_id=${kontakt.id}`" class="block text-sm text-indigo-700 hover:underline">Dodaj odpowiedź w tym wątku</Link>
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
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TextareaInput from '@/Shared/TextareaInput.vue'
import Icon from '@/Shared/Icon.vue'

export default {
  components: {
    TextareaInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    Icon,
  },
  layout: Layout,
  props: {
    kontakt: Object,
    requestedId: [Number, String],
    users: Array,
    replies: Array,
    zapytanias: Array,
    ofertas: Array,
    futureProjects: Array,
    kontaktPersons: Array,
    client: Object,
    clients: Array,
  },
  data() {
    const initialEditingId = this.requestedId && this.requestedId !== this.kontakt.id ? this.requestedId : null
    const initialEntry = initialEditingId
      ? (this.replies || []).find(r => r.id === initialEditingId) || null
      : null
    return {
      editingId: initialEntry ? initialEditingId : null,
      form: this.$inertia.form(this.buildFormData(initialEntry)),
    }
  },
  computed: {
    entries() {
      return [this.kontakt, ...(this.replies || [])]
    },
  },
  methods: {
    buildFormData(entry) {
      const e = entry || {}
      return {
        subject: e.subject ?? '',
        contact_type: e.contact_type ?? null,
        description: e.description ?? '',
        call_date: e.call_date ?? null,
        call_time: e.call_time ?? null,
        next_call_date: e.next_call_date ?? null,
        next_call_time: e.next_call_time ?? null,
        zapytania_id: e.zapytania_id ?? null,
        oferta_id: e.oferta_id ?? null,
        future_project_id: e.future_project_id ?? null,
        kontakt_person_id: e.kontakt_person_id ?? null,
        client_id: e.client_id ?? null,
        opiekun_id: e.opiekun_id ?? null,
      }
    },
    startEdit(entry) {
      this.editingId = entry.id
      this.form = this.$inertia.form(this.buildFormData(entry))
    },
    cancelEdit() {
      this.editingId = null
    },
    update(entry) {
      this.form.put(`/kontakt/${entry.id}`, {
        onSuccess: () => { this.editingId = null },
      })
    },
    destroy(entry) {
      const msg = entry.id === this.kontakt.id
        ? 'Czy na pewno chcesz usunąć główny wpis? Wszystkie odpowiedzi również zostaną usunięte.'
        : 'Czy na pewno chcesz usunąć ten wpis?'
      if (confirm(msg)) {
        this.$inertia.delete(`/kontakt/${entry.id}`)
      }
    },
  },
}
</script>

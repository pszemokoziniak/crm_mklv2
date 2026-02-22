<template>
  <div>
    <Head title="Popraw kontakt" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/kontakt">Kontakty</Link>
      <span class="text-indigo-400 font-medium">/</span>
      <span v-if="kontakt.parent_id">Odpowiedź w wątku: {{ kontakt.subject }}</span>
      <span v-else>{{ kontakt.subject }}</span>
    </h1>

    <div v-if="kontakt.parent_id" class="mb-6">
      <Link :href="`/kontakt/${kontakt.parent_id}/edit`" class="btn-indigo inline-flex items-center">
        <icon name="cheveron-right" class="w-4 h-4 mr-2 rotate-180" />
        Wróć do głównego wątku
      </Link>
    </div>

    <div class="max-w-4xl bg-white rounded-md shadow overflow-hidden mb-8">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full" label="Temat" :disabled="!!kontakt.parent_id" />
          <textarea-input v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full" label="Opis / Notatki" />

          <select-input v-model="form.kontakt_person_id" :error="form.errors.kontakt_person_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Osoba kontaktowa">
            <option :value="null">Brak</option>
            <option v-for="person in kontaktPersons" :key="person.id" :value="person.id">
              {{ person.first_name }} {{ person.last_name }}
            </option>
          </select-input>

          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Zapytanie">
            <option :value="null">Brak</option>
            <option v-for="item in zapytanias" :key="item.id" :value="item.id">{{ item.nazwa_projektu }}</option>
          </select-input>

          <select-input v-model="form.oferta_id" :error="form.errors.oferta_id" class="pb-8 pr-6 w-full lg:w-1/3" label="Oferta">
            <option :value="null">Brak</option>
            <option v-for="item in ofertas" :key="item.id" :value="item.id">{{ item.numer_oferty }}</option>
          </select-input>

          <!-- Opiekun -->
          <select-input v-model="form.opiekun_id" :error="form.errors.opiekun_id" class="pb-8 pr-6 w-full" label="Opiekun (osoba dedykowana)">
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select-input>

          <text-input v-model="form.call_date" :error="form.errors.call_date" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <text-input v-model="form.call_time" :error="form.errors.call_time" type="time" class="pb-8 pr-6 w-full lg:w-1/2" label="Godzina kontaktu" />

          <text-input v-model="form.next_call_date" :error="form.errors.next_call_date" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data następnego kontaktu" />
          <text-input v-model="form.next_call_time" :error="form.errors.next_call_time" type="time" class="pb-8 pr-6 w-full lg:w-1/2" label="Godzina następnego kontaktu" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!kontakt.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zapisz zmiany</loading-button>
        </div>
      </form>
    </div>

    <!-- Sekcja Wątku (tylko dla głównego kontaktu) -->
    <div v-if="!kontakt.parent_id" class="max-w-4xl">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Historia wątku</h2>
        <Link :href="`/kontakt/create?parent_id=${kontakt.id}`" class="btn-indigo">
          Dodaj kolejny wpis
        </Link>
      </div>

      <div v-if="replies.length > 0" class="space-y-4">
        <div v-for="reply in replies" :key="reply.id" class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
          <div class="flex justify-between items-start mb-2">
            <div class="text-sm text-gray-500">
              <span class="font-bold text-gray-700">{{ reply.user.first_name }} {{ reply.user.last_name }}</span>
              <span v-if="reply.opiekun_id && reply.opiekun_id !== reply.user_id" class="ml-2 text-indigo-600">
                (Przekazano do: {{ reply.opiekun.first_name }} {{ reply.opiekun.last_name }})
              </span>
              • {{ reply.call_date }} {{ reply.call_time }}
            </div>
            <Link :href="`/kontakt/${reply.id}/edit`" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
              Edytuj
            </Link>
          </div>
          <div class="text-gray-700 whitespace-pre-wrap">{{ reply.description }}</div>
          <div v-if="reply.next_call_date" class="mt-3 text-xs font-semibold text-indigo-600">
            Następny kontakt: {{ reply.next_call_date }} {{ reply.next_call_time }}
          </div>
        </div>
      </div>
      <div v-else class="bg-gray-50 p-8 text-center rounded-lg border-2 border-dashed border-gray-200 text-gray-500">
        Brak dodatkowych wpisów w tym wątku.
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
    users: Array,
    replies: Array,
    zapytanias: Array,
    ofertas: Array,
    kontaktPersons: Array,
    client_id: [String, Number],
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        subject: this.kontakt.subject,
        description: this.kontakt.description,
        call_date: this.kontakt.call_date,
        call_time: this.kontakt.call_time,
        next_call_date: this.kontakt.next_call_date,
        next_call_time: this.kontakt.next_call_time,
        zapytania_id: this.kontakt.zapytania_id,
        oferta_id: this.kontakt.oferta_id,
        kontakt_person_id: this.kontakt.kontakt_person_id,
        opiekun_id: this.kontakt.opiekun_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/kontakt/${this.kontakt.id}`)
    },
    destroy() {
      if (confirm('Czy na pewno chcesz usunąć ten wpis? Jeśli to główny wątek, wszystkie odpowiedzi również zostaną usunięte.')) {
        this.$inertia.delete(`/kontakt/${this.kontakt.id}`)
      }
    },
  },
}
</script>

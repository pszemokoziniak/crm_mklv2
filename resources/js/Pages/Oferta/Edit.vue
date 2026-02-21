<template>
  <div>
    <Head title="Edycja oferty" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div class="flex items-center">
        <div class="ml-4">
          <h1 class="text-3xl font-bold text-gray-900">
            <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/oferta">Oferty</Link>
            <span class="text-gray-300 font-light mx-2">/</span>
            <span class="text-gray-600">Edycja</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button v-if="!oferta.deleted_at" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
          <icon :name="isActive ? 'check' : 'edit'" class="w-4 h-4 mr-2" />
          {{ isActive ? 'Tryb edycji aktywny' : 'Edytuj dane' }}
        </button>
      </div>
    </div>

    <trashed-message v-if="oferta.deleted_at" class="mb-6 shadow-sm" @restore="restore">
      Oferta została usunięta.
    </trashed-message>

    <div class="max-w-5xl space-y-8">
      <div id="form-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
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

        <form @submit.prevent="update">
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

          <!-- Form Actions -->
          <div v-if="isActive" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <button v-if="!oferta.deleted_at" class="text-rose-600 font-semibold hover:text-rose-800 transition-colors flex items-center" tabindex="-1" type="button" @click="destroy">
              <icon name="trash" class="w-4 h-4 mr-2" />
              Archiwizuj
            </button>

            <div class="flex gap-3 ml-auto">
              <loading-button :loading="form.processing" class="btn-indigo shadow-md px-8" type="submit">
                Zapisz zmiany
              </loading-button>
            </div>
          </div>
        </form>

        <!-- Secondary Actions Bar -->
        <div v-if="!oferta.deleted_at && !isActive" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-1 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Edytuj dane</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Kontakty Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50 bg-gray-50/30">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="contact" class="w-6 h-6 fill-indigo-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia kontaktów</h2>
          </div>
          <Link :href="`/kontakt/create?client=${oferta.client_id}&oferta_id=${oferta.id}&zapytania_id=${oferta.zapytania_id}`" class="btn-indigo flex items-center px-6 py-3 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95">
            <icon name="plus" class="w-4 h-4 mr-2" />
            <span>Nowy kontakt</span>
          </Link>
        </div>

        <div class="p-8">
          <div v-if="kontakty.length > 0" class="space-y-6">
            <div v-for="kontakt in kontakty" :key="kontakt.id" class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
              <!-- Główny wpis w wątku -->
              <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                  <span class="font-bold text-indigo-900">{{ kontakt.subject }}</span>
                  <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-medium">Wątek</span>
                </div>
                <div class="flex items-center gap-4">
                  <span class="text-xs text-gray-500">{{ kontakt.call_date }} {{ kontakt.call_time }}</span>
                  <Link :href="`/kontakt/${kontakt.id}/edit`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold uppercase tracking-wider">Edytuj</Link>
                </div>
              </div>
              <div class="p-4 text-gray-700 whitespace-pre-wrap text-sm">
                <div class="mb-2 text-xs text-gray-500">
                  Osoba: <span v-if="kontakt.kontaktperson" class="font-semibold">{{ kontakt.kontaktperson.first_name }} {{ kontakt.kontaktperson.last_name }}</span>
                  <span v-else class="italic">Brak</span>
                  • Przez: <span class="font-semibold">{{ kontakt.user.first_name }} {{ kontakt.user.last_name }}</span>
                </div>
                {{ kontakt.description }}
              </div>

              <!-- Odpowiedzi w wątku -->
              <div v-if="kontakt.children && kontakt.children.length > 0" class="bg-white border-t border-gray-50">
                <div v-for="reply in kontakt.children" :key="reply.id" class="p-4 border-b border-gray-50 last:border-0 ml-8 border-l-2 border-indigo-100">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-xs font-bold text-gray-600">{{ reply.user.first_name }} {{ reply.user.last_name }}</span>
                    <div class="flex items-center gap-3">
                      <span class="text-xs text-gray-400">{{ reply.call_date }} {{ reply.call_time }}</span>
                      <Link :href="`/kontakt/${reply.id}/edit`" class="text-indigo-400 hover:text-indigo-600 text-xs">Edytuj</Link>
                    </div>
                  </div>
                  <div class="text-sm text-gray-600 whitespace-pre-wrap">{{ reply.description }}</div>
                </div>
              </div>

              <!-- Przycisk odpowiedzi -->
              <div class="bg-gray-50/30 p-3 text-right">
                <Link :href="`/kontakt/create?parent_id=${kontakt.id}&oferta_id=${oferta.id}&zapytania_id=${oferta.zapytania_id}`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center justify-end">
                  <icon name="plus" class="w-3 h-3 mr-1" />
                  Dodaj odpowiedź w tym wątku
                </Link>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 text-gray-400">
            <icon name="contact" class="w-12 h-12 mx-auto mb-3 opacity-20" />
            <p>Brak zarejestrowanych kontaktów dla tej oferty.</p>
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
    kontakty: Array,
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
        user_id: this.oferta.user_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/oferta/${this.oferta.id}`, {
        onSuccess: () => {
          this.disable = true
          this.isActive = false
        },
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
    disableForm() {
      this.isActive = !this.isActive
      this.disable = !this.disable
    },
  },
}
</script>

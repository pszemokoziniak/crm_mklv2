<template>
  <div>
    <Head :title="`${form.nazwa}`" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div class="flex items-center">
        <div class="ml-4">
          <h1 class="text-3xl font-bold text-gray-900">
            <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/clients">Klienci</Link>
            <span class="text-gray-300 font-light mx-2">/</span>
            <span class="text-gray-600">{{ form.nazwa }}</span>
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button v-if="!client.deleted_at" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
          <icon :name="isActive ? 'check' : 'edit'" class="w-4 h-4 mr-2" />
          {{ isActive ? 'Tryb edycji aktywny' : 'Edytuj dane' }}
        </button>
      </div>
    </div>

    <trashed-message v-if="client.deleted_at" class="mb-6 shadow-sm" @restore="restore">
      Klient został usunięty.
    </trashed-message>

    <div class="w-full mb-8">
      <div id="form-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
        <form @submit.prevent="update">
          <div class="flex flex-wrap -mb-8 -mr-6 p-8">
            <text-input v-model="form.nazwa" :error="form.errors.nazwa" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />
            <text-input v-model="form.ulica" :error="form.errors.ulica" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Ulica" />
            <text-input v-model="form.miasto" :error="form.errors.miasto" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Miasto" />
            <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
              <option :value="null" />
              <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select-input>
            <div class="pb-8 pr-6 w-full lg:w-1/2">
              <div class="flex items-center justify-between">
                <label class="form-label">WWW:</label>
                <a v-if="form.www" :href="formatUrl(form.www)" target="_blank" class="text-indigo-600 hover:underline text-sm mb-1">Otwórz &rarr;</a>
              </div>
              <text-input v-model="form.www" :error="form.errors.www" :disabled="disable" />
            </div>
            <div class="pb-8 pr-6 w-full lg:w-1/2">
              <div class="flex items-center justify-between">
                <label class="form-label">LinkedIn:</label>
                <a v-if="form.linkedIn" :href="formatUrl(form.linkedIn)" target="_blank" class="text-indigo-600 hover:underline text-sm mb-1">Otwórz &rarr;</a>
              </div>
              <text-input v-model="form.linkedIn" :error="form.errors.linkedIn" :disabled="disable" />
            </div>
            <select-input v-model="form.branza_id" :error="form.errors.branza_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Branża">
              <option :value="null" />
              <option v-for="item in branza" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select-input>
            <select-input
              v-model="form.user_id" :error="form.errors.user_id" :disabled="disable"
              class="pb-8 pr-6 w-full lg:w-1/2" label="Opiekun"
            >
              <option :value="null" />
              <option v-for="item in user" :key="item.id" :value="item.id">{{ item.first_name }} {{ item.last_name }}</option>
            </select-input>
            <text-area-input v-model="form.message" :error="form.errors.message" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Informacje" />
          </div>

          <!-- Form Actions -->
          <div v-if="isActive" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <archive-button v-if="!client.deleted_at" @click="destroy" />
            <div class="flex gap-3 ml-auto">
              <button type="button" class="btn-secondary px-8" @click="disableForm">Anuluj</button>
              <loading-button :loading="form.processing" class="btn-indigo shadow-md px-8" type="submit">
                Zapisz zmiany
              </loading-button>
            </div>
          </div>
        </form>

        <!-- Secondary Actions Bar -->
        <div v-if="!client.deleted_at && !isActive" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Edytuj dane</span>
            </button>
            <Link class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" :href="`/kontaktperson/${client_id}/index`">
              <icon name="users" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Osoby kontaktowe</span>
            </Link>
            <Link class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" :href="`/kontakt/${client_id}/index`">
              <icon name="contact" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Wszystkie kontakty</span>
            </Link>
            <Link class="flex items-center justify-center px-4 py-4 hover:bg-indigo-50 text-indigo-600 transition-all group" :href="`/zadania/create?client_id=${client_id}`">
              <icon name="plus" class="mr-2 w-4 h-4 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Nowe zadanie</span>
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Kontakty Section -->
    <div class="mt-12">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50 bg-gray-50/30">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="contact" class="w-6 h-6 fill-indigo-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia kontaktów</h2>
          </div>
          <Link :href="`/kontakt/create?client=${client.id}`" class="btn-indigo flex items-center px-6 py-3 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95">
            <icon name="plus" class="w-4 h-4 mr-2" />
            <span>Nowy kontakt</span>
          </Link>
        </div>

        <div class="p-8">
          <div v-if="kontakty.length > 0" class="space-y-6">
            <div v-for="kontakt in kontakty" :key="kontakt.id" class="border border-gray-100 rounded-xl overflow-hidden shadow-sm" :class="{ 'opacity-70': kontakt.deleted_at }">
              <!-- Główny wpis w wątku -->
              <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                  <span class="font-bold text-indigo-900">{{ kontakt.subject }}</span>
                  <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-medium">Wątek</span>
                  <span v-if="kontakt.deleted_at" class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-600 border border-rose-100" :title="`Zarchiwizowane ${kontakt.deleted_at}`">ARCHIWUM</span>
                </div>
                <div class="flex items-center gap-4">
                  <span class="text-xs text-gray-500">{{ kontakt.call_date }} {{ kontakt.call_time }}</span>
                  <Link :href="`/kontakt/${kontakt.id}/edit`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold uppercase tracking-wider">Edytuj</Link>
                </div>
              </div>
              <div class="p-4 text-gray-700 whitespace-pre-wrap text-sm">
                <div class="mb-2 text-xs text-gray-500">
                  Kontakt: <span v-if="kontakt.kontaktperson" class="font-semibold">{{ kontakt.kontaktperson.first_name
                  }} {{ kontakt.kontaktperson.last_name }}</span>
                  <span v-else class="italic">Brak</span>
                  • Opiekun: <span class="font-semibold">{{ kontakt.user.first_name }} {{ kontakt.user.last_name
                  }}</span>
                </div>
                {{ kontakt.description }}
              </div>

              <!-- Odpowiedzi w wątku -->
              <div v-if="kontakt.children && kontakt.children.length > 0" class="bg-white border-t border-gray-50">
                <div v-for="reply in kontakt.children" :key="reply.id" class="p-4 border-b border-gray-50 last:border-0 ml-8 border-l-2">
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
                <Link :href="`/kontakt/create?parent_id=${kontakt.id}`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center justify-end">
                  <icon name="plus" class="w-3 h-3 mr-1" />
                  Dodaj odpowiedź w tym wątku
                </Link>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 text-gray-400">
            <icon name="contact" class="w-12 h-12 mx-auto mb-3 opacity-20" />
            <p>Brak zarejestrowanych kontaktów dla tego klienta.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Zapytania i Oferty Table -->
    <div class="mt-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Zapytania i Oferty</h2>
      <div class="space-y-6">
        <div v-for="zapytanie in zapytania" :key="zapytanie.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" :class="{ 'opacity-70': zapytanie.deleted_at }">
          <!-- Zapytanie Header -->
          <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-5 flex flex-col">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 flex items-center gap-2">
                <span>Zapytanie</span>
                <span v-if="zapytanie.deleted_at" class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-600 border border-rose-100" :title="`Zarchiwizowane ${zapytanie.deleted_at}`">ARCHIWUM</span>
              </span>
              <Link :href="`/zapytania/${zapytanie.id}/edit`" class="text-base font-bold text-gray-900 hover:text-indigo-600 transition-colors truncate" :title="zapytanie.nazwa_projektu">
                {{ zapytanie.nazwa_projektu }}
              </Link>
            </div>
            <div class="md:col-span-3 flex flex-col">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Data</span>
              <span class="text-sm font-medium text-gray-600">{{ zapytanie.created_at }}</span>
            </div>
            <div class="md:col-span-4 flex flex-col">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Opiekun</span>
              <span class="text-sm font-medium text-gray-600">{{ zapytanie.user }}</span>
            </div>
          </div>

          <!-- Oferty Table -->
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="text-left font-bold text-gray-400 text-[10px] uppercase tracking-widest bg-white border-b border-gray-50">
                  <th class="px-6 py-3 whitespace-nowrap w-1/3">Oferta</th>
                  <th class="px-6 py-3 whitespace-nowrap w-1/3 text-center">Status</th>
                  <th class="px-6 py-3 whitespace-nowrap w-1/3 text-right">Wartość</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="oferta in zapytanie.oferty" :key="oferta.id" class="hover:bg-indigo-50/20 transition-colors" :class="{ 'opacity-60': oferta.deleted_at }">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <Link :href="`/oferta/${oferta.id}/edit`" class="flex flex-col group">
                        <span class="text-sm font-semibold text-indigo-600 group-hover:underline flex items-center gap-2">
                          {{ oferta.numer_oferty }}
                          <span v-if="oferta.deleted_at" class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-600 border border-rose-100" :title="`Zarchiwizowane ${oferta.deleted_at}`">ARCHIWUM</span>
                        </span>
                        <span class="text-[10px] text-gray-400 group-hover:text-indigo-400 transition-colors">
                          {{ oferta.created_at }}
                        </span>
                      </Link>
                      <Link :href="`/oferta/${oferta.id}/edit`" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Przejdź do oferty">
                        <icon name="cheveron-right" class="w-4 h-4" />
                      </Link>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                      {{ oferta.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                    {{ oferta.kwota || '-' }} {{ oferta.waluta }}
                  </td>
                </tr>
                <tr v-if="zapytanie.oferty.length === 0">
                  <td class="px-6 py-4 text-center text-sm text-gray-400 italic" colspan="3">
                    Brak ofert dla tego zapytania.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="zapytania.length === 0" class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
          <icon name="users" class="w-12 h-12 mx-auto mb-4 text-gray-300" />
          <p class="text-gray-500 font-medium">Brak powiązanych zapytań dla tego klienta.</p>
        </div>
      </div>
    </div>

    <!-- Zadania Section -->
    <div class="mt-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Zadania</h2>
        <Link :href="`/zadania/create?client_id=${client_id}`" class="btn-indigo flex items-center px-5 py-2 rounded-lg shadow-sm transition-all hover:shadow-md active:scale-95 text-sm">
          <icon name="plus" class="w-4 h-4 mr-2" />
          <span>Nowe zadanie</span>
        </Link>
      </div>
      <div class="space-y-3">
        <div v-for="zadanie in zadania" :key="zadanie.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <Link :href="`/zadania/${zadanie.id}/edit`" class="block hover:bg-indigo-50/30 transition-colors">
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
              <div class="md:col-span-5 flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Temat</span>
                <span class="text-base font-bold text-gray-900 truncate" :title="zadanie.subject">{{ zadanie.subject }}</span>
              </div>
              <div class="md:col-span-3 flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Termin</span>
                <span class="text-sm font-medium text-gray-600">{{ zadanie.deadline || '—' }}</span>
              </div>
              <div class="md:col-span-3 flex flex-col">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Odpowiedzialny</span>
                <span class="text-sm font-medium text-gray-600">{{ zadanie.responsible_person || '—' }}</span>
              </div>
              <div class="md:col-span-1 flex justify-end">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border"
                  :class="zadanieStatusClasses(zadanie.status)"
                >
                  {{ zadanieStatusLabel(zadanie.status) }}
                </span>
              </div>
            </div>
          </Link>
        </div>
        <div v-if="zadania.length === 0" class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
          <icon name="contact" class="w-12 h-12 mx-auto mb-4 text-gray-300" />
          <p class="text-gray-500 font-medium">Brak zadań dla tego klienta.</p>
        </div>
      </div>
    </div>

    <!-- Historia zmian (Activity Log) - PRZENIESIONA NA DÓŁ -->
    <div class="mt-12 mb-12">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-gray-50/30 cursor-pointer hover:bg-gray-100/50 transition-colors" @click="isHistoryVisible = !isHistoryVisible">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="printer" class="w-6 h-6 fill-amber-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia zmian systemowych</h2>
          </div>
          <icon name="cheveron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': isHistoryVisible }" />
        </div>
        <div v-if="isHistoryVisible" class="p-8">
          <div v-if="filteredActivities && filteredActivities.length > 0" class="flow-root">
            <ul role="list" class="-mb-8">
              <li v-for="(activity, index) in filteredActivities" :key="activity.id">
                <div class="relative pb-8">
                  <span v-if="index !== filteredActivities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        :class="[
                          activity.description === 'deleted' ? 'bg-red-500' : 'bg-blue-500',
                          'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                        ]"
                      >
                        <icon name="edit" class="w-4 h-4 text-white" />
                      </span>
                    </div>
                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                      <div>
                        <p class="text-sm text-gray-500">
                          <span class="font-medium text-gray-900">{{ activity.user }}</span>
                          {{ activity.description === 'updated' ? 'zaktualizował(a) dane' :
                            activity.description === 'deleted' ? 'usunął/ęła rekord' : activity.description }}
                        </p>
                        <!-- Wyświetlanie zmian -->
                        <div v-if="activity.changes && activity.changes.attributes" class="mt-2 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                          <div v-for="(val, key) in activity.changes.attributes" :key="key" class="mb-1 last:mb-0">
                            <span class="font-bold text-gray-600">{{ key }}:</span>
                            <span v-if="activity.changes.old && activity.changes.old[key]" class="text-red-500 line-through mx-1">{{ activity.changes.old[key] }}</span>
                            <span class="text-green-600 font-medium">{{ val }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="whitespace-nowrap text-right text-sm text-gray-500">
                        <time :datetime="activity.created_at">{{ activity.created_at }}</time>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="text-center py-6 text-gray-400 italic">
            Brak zarejestrowanych zmian.
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
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from '@/Shared/Icon.vue'
import ArchiveButton from '@/Shared/ArchiveButton.vue'

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
    ArchiveButton,
  },
  layout: Layout,
  props: {
    client: Object,
    branza: Object,
    kraj: Object,
    user: Object,
    // eslint-disable-next-line vue/prop-name-casing
    client_id: String,
    zapytania: Array,
    kontakty: Array,
    activities: Array,
    zadania: { type: Array, default: () => [] },
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      isHistoryVisible: false,
      form: this.$inertia.form({
        nazwa: this.client.nazwa,
        ulica: this.client.ulica,
        miasto: this.client.miasto,
        www: this.client.www,
        linkedIn: this.client.linkedIn,
        waluta: this.client.waluta,
        message: this.client.message,
        user_id: this.client.user_id,
        branza_id: this.client.branza_id,
        kraj_id: this.client.kraj_id,
      }),
    }
  },
  computed: {
    filteredActivities() {
      if (!this.activities) {
        return []
      }
      return this.activities.filter(activity => activity.description !== 'created')
    },
  },
  methods: {
    update() {
      this.form.put(`/clients/${this.client.id}`, {
        onSuccess: () => {
          this.disable = true
          this.isActive = false
        },
      })
    },
    destroy() {
      if (confirm('Czy chcesz usunąć tego klienta?')) {
        this.$inertia.delete(`/clients/${this.client.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić klienta?')) {
        this.$inertia.put(`/clients/${this.client.id}/restore`)
      }
    },
    disableForm() {
      this.isActive = !this.isActive
      this.disable = !this.disable
    },
    contactPerson() {
      this.form.get(`/kontaktperson/${this.client.id}/index`)
    },
    formatUrl(url) {
      if (!url) return ''
      if (url.startsWith('http://') || url.startsWith('https://')) {
        return url
      }
      return `https://${url}`
    },
    zadanieStatusLabel(status) {
      if (status === 'aktywne') return 'Aktywne'
      if (status === 'do_akceptacji') return 'Do akceptacji'
      if (status === 'zamkniete') return 'Zamknięte'
      return status || '—'
    },
    zadanieStatusClasses(status) {
      if (status === 'aktywne') return 'bg-blue-50 text-blue-700 border-blue-100'
      if (status === 'do_akceptacji') return 'bg-yellow-50 text-yellow-700 border-yellow-100'
      if (status === 'zamkniete') return 'bg-gray-50 text-gray-600 border-gray-100'
      return 'bg-gray-50 text-gray-600 border-gray-100'
    },
  },
}
</script>

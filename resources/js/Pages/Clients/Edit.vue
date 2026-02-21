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
          <div class="grid grid-cols-3 divide-x divide-gray-100">
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
        <div v-for="zapytanie in zapytania" :key="zapytanie.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <!-- Zapytanie Header -->
          <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-5 flex flex-col">
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Zapytanie</span>
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
                <tr v-for="oferta in zapytanie.oferty" :key="oferta.id" class="hover:bg-indigo-50/20 transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <Link :href="`/oferta/${oferta.id}/edit`" class="flex flex-col group">
                        <span class="text-sm font-semibold text-indigo-600 group-hover:underline">
                          {{ oferta.numer_oferty }}
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
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
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
  },
}
</script>

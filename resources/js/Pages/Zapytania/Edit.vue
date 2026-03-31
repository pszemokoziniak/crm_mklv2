<template>
  <div>
    <Head :title="`${form.nazwa_projektu}`" />

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div class="flex items-center">
        <h1 class="text-3xl font-bold text-gray-900">
          <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/zapytania">Zapytania</Link>
          <span class="text-gray-300 font-light mx-2">/</span>
          <span class="text-gray-600">{{ form.id_zapyt }}</span>
        </h1>
        <span v-if="zapytania.wznowienie === 2" class="ml-4 px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold uppercase tracking-wider rounded-full shadow-sm border border-rose-200">
          Wznowiony
        </span>
      </div>

      <div class="flex items-center gap-2">
        <button v-if="zapytania.can.edit && !zapytania.deleted_at" :class="isActive ? 'bg-green-600 text-white border-green-700' : 'bg-white text-indigo-600 border-indigo-200'" class="flex items-center px-4 py-2 border rounded-lg text-sm font-medium hover:shadow-md transition-all" @click="disableForm">
          <icon :name="isActive ? 'check' : 'edit'" class="w-4 h-4 mr-2" />
          {{ isActive ? 'Tryb edycji aktywny' : 'Edytuj dane' }}
        </button>
      </div>
    </div>

    <trashed-message v-if="zapytania.deleted_at" class="mb-6 shadow-sm" @restore="restore">
      To zapytanie znajduje się w archiwum.
    </trashed-message>

    <div class="max-w-5xl space-y-8">
      <!-- Main Form Card -->
      <div id="form-container" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
        <!-- Project Info Header -->
        <div class="px-8 py-6 border-b border-gray-50 bg-gradient-to-r from-gray-50 to-white">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h2 class="text-xl font-extrabold text-gray-800 tracking-tight">{{ zapytania.nazwa_projektu }}</h2>
              <div class="flex items-center mt-1 text-sm text-gray-500">
                <icon name="office" class="w-4 h-4 mr-1 fill-gray-400" />
                Klient: <Link class="ml-1 text-indigo-600 font-semibold hover:underline" :href="`/clients/${zapytania.client_id}/edit`">{{ clientById.nazwa }}</Link>
              </div>
            </div>
          </div>

          <!-- Archive Reason -->
          <div v-if="archiwumOpis[0]" class="mt-6 p-4 bg-amber-50 border-l-4 border-amber-400 rounded-r-lg text-sm text-amber-900">
            <div class="flex items-start">
              <icon name="info" class="w-5 h-5 mr-3 fill-amber-500 flex-shrink-0 mt-0.5" />
              <div>
                <p class="font-bold text-amber-900 mb-1 uppercase text-xs tracking-widest">Powód archiwizacji</p>
                <p class="leading-relaxed">{{ archiwumOpis[0].description }}</p>
                <div class="mt-2 text-xs text-amber-700 italic opacity-80">
                  Przez: {{ archiwumOpis[0].user.last_name }} {{ archiwumOpis[0].user.first_name }} • {{ archiwumOpis[0].created_at }}
                </div>
              </div>
            </div>
          </div>

          <!-- No Permission Alert -->
          <div v-if="!zapytania.can.edit" class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg text-sm text-blue-900 flex items-center">
            <icon name="info" class="w-5 h-5 mr-3 fill-blue-500 flex-shrink-0" />
            <span class="font-medium">Tryb tylko do odczytu.</span>
            <span class="ml-1 opacity-80">Nie masz uprawnień do edycji lub nie jesteś przypisany do tego projektu.</span>
          </div>
        </div>

        <!-- Form Fields -->
        <form @submit.prevent="update">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 p-8">
            <select-input v-model="form.user_otrzymal_id" :error="form.errors.user_otrzymal_id" :disabled="disable" class="w-full" label="Zarejestrował">
              <option :value="null" />
              <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
            </select-input>

            <div class="grid grid-cols-2 gap-4">
              <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" :disabled="disable" type="date" label="Data otrzymania" />
              <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" :disabled="disable" type="date" label="Termin złożenia" />
            </div>

            <select-input v-model="form.client_id" :error="form.errors.client_id" :disabled="disable" class="w-full" label="Klient">
              <option :value="null" />
              <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
            </select-input>

            <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" :disabled="disable" class="w-full" label="Nazwa projektu" />

            <div class="grid grid-cols-2 gap-4">
              <select-input v-model="form.preliminarz" :error="form.errors.preliminarz" :disabled="disable" label="Preliminarz">
                <option value="Tak">Tak</option>
                <option value="Nie">Nie</option>
              </select-input>
              <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" :disabled="disable" label="Miejscowość" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" :disabled="disable" label="Kraj">
                <option :value="null" />
                <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.name }}</option>
              </select-input>
              <select-input v-model="form.zakres_id" :error="form.errors.zakres_id" :disabled="disable" label="Zakres">
                <option :value="null" />
                <option v-for="item in zakres" :key="item.id" :value="item.id">{{ item.name }}</option>
              </select-input>
            </div>

            <select-input v-model="form.user_opracowuje_id" :error="form.errors.user_opracowuje_id" :disabled="disable" class="w-full" label="Opracowuje">
              <option :value="null" />
              <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
            </select-input>

            <div class="grid grid-cols-2 gap-4">
              <text-input v-model="form.start" :error="form.errors.start" :disabled="disable" type="date" label="Planowany start" />
              <text-input v-model="form.end" :error="form.errors.end" :disabled="disable" type="date" label="Planowany koniec" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <number-input v-model="form.kwota" :error="form.errors.kwota" :disabled="disable" label="Kwota" />
              <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" :disabled="disable" label="Waluta">
                <option :value="null" />
                <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
              </select-input>
            </div>

            <text-area v-model="form.opis" :error="form.errors.opis" :disabled="disable" class="md:col-span-2" label="Opis projektu" rows="8" />
          </div>

          <!-- Form Actions -->
          <div v-if="zapytania.can.edit" class="flex items-center justify-between px-8 py-6 bg-gray-50 border-t border-gray-100">
            <button v-if="!zapytania.deleted_at" class="text-rose-600 font-semibold hover:text-rose-800 transition-colors flex items-center" tabindex="-1" type="button" @click="destroy">
              <icon name="trash" class="w-4 h-4 mr-2" />
              Archiwizuj zapytanie
            </button>
            <div class="flex gap-3 ml-auto">
              <loading-button :loading="form.processing" class="btn-indigo shadow-md px-8" type="submit">
                Zapisz zmiany
              </loading-button>
            </div>
          </div>
        </form>

        <!-- Secondary Actions Bar -->
        <div v-if="!zapytania.deleted_at" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 divide-x divide-gray-100">
            <form ref="form" action="pdf" class="flex" @submit.prevent="submit">
              <input type="hidden" name="param" :value="`${zapytania.id}`" />
              <button type="submit" class="flex-1 flex items-center justify-center px-4 py-5 hover:bg-indigo-50 text-indigo-600 transition-all group">
                <icon name="pdf" class="mr-2 w-5 h-5 group-hover:scale-110 transition-transform" />
                <span class="text-sm font-bold">PDF</span>
              </button>
            </form>

            <button class="flex items-center justify-center px-4 py-5 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="mail">
              <icon name="mail" class="mr-2 w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold">Email</span>
            </button>

            <button v-if="zapytania.wznowienie === 1 && zapytania.can.edit" class="flex items-center justify-center px-4 py-5 hover:bg-indigo-50 text-indigo-600 transition-all group" @click="wznowienie">
              <icon name="historia" class="mr-2 w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold text-center">Wznowienie</span>
            </button>

            <button v-if="zapytania.wznowienie === 2 && zapytania.can.edit" class="flex items-center justify-center px-4 py-5 hover:bg-rose-50 text-rose-600 transition-all group" @click="deleteWznowienie">
              <icon name="trash" class="mr-2 w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="text-sm font-bold text-center">Anuluj wznowienie</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Kontakty Section -->
      <div id="historia-kontaktow" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50 bg-gray-50/30">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="contact" class="w-6 h-6 fill-indigo-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia kontaktów</h2>
          </div>
          <Link :href="`/kontakt/create?client=${zapytania.client_id}&zapytania_id=${zapytania.id}`" class="btn-indigo flex items-center px-6 py-3 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95">
            <icon name="plus" class="w-4 h-4 mr-2" />
            <span>Nowy kontakt</span>
          </Link>
        </div>

        <div class="p-8">
          <div v-if="kontakty.length > 0" class="space-y-6">
            <div v-for="kontakt in kontakty" :id="`kontakt-${kontakt.id}`" :key="kontakt.id" class="border border-gray-100 rounded-xl overflow-hidden shadow-sm target:ring-2 target:ring-indigo-500 transition-all">
              <!-- Główny wpis w wątku -->
              <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                  <span class="font-bold text-indigo-900">{{ kontakt.subject }}</span>
                  <span v-if="kontakt.contact_type" class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs uppercase font-bold flex items-center">
                    <template v-if="kontakt.contact_type === 'telefon'">
                      <icon name="phone" class="w-3 h-3 mr-1" />
                    </template>
                    <template v-else-if="kontakt.contact_type === 'email'">
                      <icon name="mail" class="w-3 h-3 mr-1" />
                    </template>
                    <template v-else-if="kontakt.contact_type === 'osobisty'">
                      <icon name="contact" class="w-3 h-3 mr-1" />
                    </template>
                    {{ kontakt.contact_type }}
                  </span>
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
                <div v-for="reply in kontakt.children" :key="reply.id" class="p-4 border-b last:border-0 ml-8 border-l-2 border-indigo-100">
                  <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center">
                      <span class="text-xs font-bold text-gray-600">{{ reply.user.first_name }} {{ reply.user.last_name }}</span>
                      <span v-if="reply.contact_type" class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded text-xs uppercase font-bold flex items-center">
                        <template v-if="reply.contact_type === 'telefon'">
                          <icon name="phone" class="w-3 h-3 mr-1" />
                        </template>
                        <template v-else-if="reply.contact_type === 'email'">
                          <icon name="mail" class="w-3 h-3 mr-1" />
                        </template>
                        <template v-else-if="reply.contact_type === 'osobisty'">
                          <icon name="contact" class="w-3 h-3 mr-1" />
                        </template>
                        {{ reply.contact_type }}
                      </span>
                    </div>
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
                <Link :href="`/kontakt/create?parent_id=${kontakt.id}&zapytania_id=${zapytania.id}`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center justify-end">
                  <icon name="plus" class="w-3 h-3 mr-1" />
                  Dodaj odpowiedź w tym wątku
                </Link>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 text-gray-400">
            <icon name="contact" class="w-12 h-12 mx-auto mb-3 opacity-20" />
            <p>Brak zarejestrowanych kontaktów dla tego zapytania.</p>
          </div>
        </div>
      </div>

      <!-- Offers Section -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50 bg-gray-50/30">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="oferty" class="w-6 h-6 fill-indigo-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Oferty handlowe</h2>
          </div>
          <Link v-if="zapytania.can.edit" :href="`/oferta/create/data/${zapytania.id}/${zapytania.client_id}`" class="btn-indigo flex items-center px-6 py-3 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95">
            <icon name="plus" class="w-4 h-4 mr-2" />
            <span>Nowa oferta</span>
          </Link>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr class="text-left font-bold text-gray-400 text-xs uppercase tracking-widest bg-gray-50/50">
                <th class="px-8 py-4">Wartość</th>
                <th class="px-8 py-4">Data kontaktu</th>
                <th class="px-8 py-4">Opiekun</th>
                <th class="px-8 py-4" />
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="item in oferty" :key="item.id" class="hover:bg-indigo-50/30 transition-colors group">
                <td class="px-8 py-5">
                  <Link class="flex items-center font-bold text-gray-900 group-hover:text-indigo-600 transition-colors" :href="`/oferta/${item.id}/edit`">
                    {{ formatNumber(item.kwota) }} <span class="ml-1 text-gray-400 font-medium">{{ item.waluta }}</span>
                  </Link>
                </td>
                <td class="px-8 py-5">
                  <Link class="flex items-center text-gray-600" :href="`/oferta/${item.id}/edit`">
                    {{ item.data_kontakt }}
                    <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-rose-400" />
                  </Link>
                </td>
                <td class="px-8 py-5">
                  <Link class="flex flex-col" :href="`/oferta/${item.id}/edit`">
                    <span class="text-sm font-medium text-gray-700">{{ item.user.last_name }} {{ item.user.first_name }}</span>
                    <span class="text-xs text-gray-400">{{ item.created_at }}</span>
                  </Link>
                </td>
                <td class="px-8 py-5 text-right">
                  <Link class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                    <icon name="cheveron-right" class="w-5 h-5" />
                  </Link>
                </td>
              </tr>
              <tr v-if="oferty.length === 0">
                <td class="px-8 py-12 text-center text-gray-400 border-t" colspan="4">
                  <div class="flex flex-col items-center">
                    <icon name="zapytania" class="w-12 h-12 mb-2 opacity-20" />
                    <p>Brak ofert dla tego zapytania.</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Historia zmian (Activity Log) -->
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
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import NumberInput from '@/Shared/NumberInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextArea from '@/Shared/TextareaInput.vue'
import Icon from '@/Shared/Icon.vue'

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
    archiwumOpis: Object,
    waluta: Object,
    oferty: Object,
    kontakty: Array,
    activities: Array,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      isHistoryVisible: false,
      form: this.$inertia.form({
        id_zapyt: this.zapytania.id_zapyt,
        user_otrzymal_id: this.zapytania.user_otrzymal_id,
        data_otrzymania: this.zapytania.data_otrzymania,
        data_zlozenia: this.zapytania.data_zlozenia || null,
        client_id: this.zapytania.client_id,
        nazwa_projektu: this.zapytania.nazwa_projektu,
        preliminarz: this.zapytania.preliminarz,
        miejscowosc: this.zapytania.miejscowosc,
        kraj_id: this.zapytania.kraj_id,
        zakres_id: this.zapytania.zakres_id,
        user_opracowuje_id: this.zapytania.user_opracowuje_id,
        start: this.zapytania.start,
        end: this.zapytania.end,
        kwota: this.zapytania.kwota,
        waluta_id: this.zapytania.waluta_id,
        opis: this.zapytania.opis,
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
  mounted() {
    const urlParams = new URLSearchParams(window.location.search)
    const rememberKontakt = urlParams.get('remember_kontakt')

    if (rememberKontakt) {
      setTimeout(() => {
        const el = document.getElementById(`kontakt-${rememberKontakt}`)
        if (el) {
          el.scrollIntoView({ behavior: 'smooth', block: 'center' })
          el.classList.add('ring-4', 'ring-indigo-500', 'ring-opacity-50')
        } else {
          document.getElementById('historia-kontaktow')?.scrollIntoView({ behavior: 'smooth' })
        }
      }, 100)
    }
  },
  methods: {
    update() {
      this.form.put(`/zapytania/${this.zapytania.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz zarchiwizować te zapytanie?')) {
        this.$inertia.delete(`/zapytania/${this.zapytania.id}/destroy`)
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
    wznowienie() {
      this.form.post(`/zapytania/${this.zapytania.id}/wznowienie`)
    },
    deleteWznowienie() {
      this.form.get(`/zapytania/${this.zapytania.id}/deletewznowienie`)
    },
    disableForm() {
      if (!this.zapytania.can.edit) return
      this.isActive = !this.isActive
      this.disable = !this.disable
    },
    formatNumber (num) {
      if (!num) return '0,00'
      return new Intl.NumberFormat('pl-PL',{
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num)
    },
  },
}
</script>

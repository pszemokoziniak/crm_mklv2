<template>
  <div>
    <Head :title="`${form.nazwa_projektu}`" />
    <div class="flex items-center mb-8">
      <h1 class="text-3xl font-bold">
        <Link class="text-indigo-400 hover:text-indigo-600" href="/zapytania">Zapytania</Link>
        <span class="text-gray-400 font-medium"> /</span> {{ form.id_zapyt }}
      </h1>
      <span v-if="zapytania.wznowienie === 2" class="ml-4 px-3 py-1 bg-red-100 text-red-600 text-sm font-semibold rounded-full">Wznowiony</span>
    </div>

    <trashed-message v-if="zapytania.deleted_at" class="mb-6" @restore="restore"> Zapytanie zostało zarchiwizowane </trashed-message>

    <div class="max-w-5xl">
      <div id="form" class="bg-white rounded-md shadow overflow-hidden mb-8">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
          <div class="text-xl font-bold text-gray-800">
            {{ zapytania.nazwa_projektu }}
            <span class="text-gray-400 mx-2">|</span>
            <Link class="text-indigo-600 hover:underline" :href="`/clients/${zapytania.client_id}/edit`">{{ clientById.nazwa }}</Link>
          </div>
          <div v-if="archiwumOpis[0]" class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded text-sm text-amber-800">
            <p class="font-semibold mb-1">Powód archiwizacji:</p>
            {{ archiwumOpis[0].description }}
            <div class="mt-2 text-xs text-amber-600">
              Zarchiwizowane przez: {{ archiwumOpis[0].user.last_name }} {{ archiwumOpis[0].user.first_name }} dnia {{ archiwumOpis[0].created_at }}
            </div>
          </div>
        </div>

        <form :class="{ 'ring-2 ring-green-500 ring-inset': isActive }" @submit.prevent="update">
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
            <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa projektu" />
            <select-input v-model="form.preliminarz" :error="form.errors.preliminarz" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Preliminarz">
              <option value="Tak">Tak</option>
              <option value="Nie">Nie</option>
            </select-input>
            <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowość" />
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
            <text-input v-model="form.end" :error="form.errors.end" :disabled="disable" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin zakończenia" />
            <number-input v-model="form.kwota" :error="form.errors.kwota" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
            <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
              <option :value="null" />
              <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
            </select-input>
            <text-area v-model="form.opis" :error="form.errors.opis" :disabled="disable" class="pb-8 pr-6 w-full" label="Opis" />
          </div>
          <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
            <button v-if="!zapytania.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
            <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zapisz zmiany</loading-button>
          </div>
        </form>

        <div v-if="!zapytania.deleted_at" class="bg-white border-t border-gray-100">
          <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 divide-x divide-gray-100">
            <button class="flex items-center justify-center px-4 py-4 hover:bg-gray-50 text-indigo-600 transition" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4" />
              <span class="text-sm font-medium">Edytuj dane</span>
            </button>

            <form ref="form" action="pdf" class="flex" @submit.prevent="submit">
              <input type="hidden" name="param" :value="`${zapytania.id}`" />
              <button type="submit" class="flex-1 flex items-center justify-center px-4 py-4 hover:bg-gray-50 text-indigo-600 transition">
                <icon name="pdf" class="mr-2 w-4 h-4" />
                <span class="text-sm font-medium">Generuj PDF</span>
              </button>
            </form>

            <button class="flex items-center justify-center px-4 py-4 hover:bg-gray-50 text-indigo-600 transition" @click="mail">
              <icon name="mail" class="mr-2 w-4 h-4" />
              <span class="text-sm font-medium">Wyślij mail</span>
            </button>

            <button v-if="zapytania.wznowienie === 1" class="flex items-center justify-center px-4 py-4 hover:bg-gray-50 text-indigo-600 transition" @click="wznowienie">
              <icon name="wznowienie" class="mr-2 w-4 h-4" />
              <span class="text-sm font-medium">Wznowienie</span>
            </button>

            <button v-if="zapytania.wznowienie === 2" class="flex items-center justify-center px-4 py-4 hover:bg-gray-50 text-red-600 transition" @click="deleteWznowienie">
              <icon name="deleteWznowienie" class="mr-2 w-4 h-4" />
              <span class="text-sm font-medium">Anuluj wznowienie</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Sekcja Ofert -->
      <div class="bg-white rounded-md shadow overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
          <h2 class="text-2xl font-bold text-gray-800">Oferty</h2>
          <Link :href="`/oferta/create/data/${zapytania.id}/${zapytania.client_id}`" class="btn-indigo">
            <span>Dodaj ofertę</span>
          </Link>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full whitespace-nowrap">
            <thead>
              <tr class="text-left font-bold bg-gray-50">
                <th class="px-8 py-4">Kwota</th>
                <th class="px-8 py-4">Data kontaktu</th>
                <th class="px-8 py-4">Dodał</th>
                <th class="px-8 py-4" />
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in oferty" :key="item.id" class="hover:bg-gray-50 focus-within:bg-gray-50">
                <td class="border-t">
                  <Link class="flex items-center px-8 py-4 font-medium text-indigo-600" :href="`/oferta/${item.id}/edit`">
                    {{ formatNumber(item.kwota) }} {{ item.waluta }}
                  </Link>
                </td>
                <td class="border-t">
                  <Link class="flex items-center px-8 py-4" :href="`/oferta/${item.id}/edit`">
                    {{ item.data_kontakt }}
                    <icon v-if="item.deleted_at" name="trash" class="flex-shrink-0 ml-2 w-3 h-3 fill-gray-400" />
                  </Link>
                </td>
                <td class="border-t">
                  <Link class="flex items-center px-8 py-4 text-sm text-gray-600" :href="`/oferta/${item.id}/edit`">
                    <div>
                      {{ item.user.last_name }} {{ item.user.first_name }}
                      <div class="text-xs text-gray-400">{{ item.created_at }}</div>
                    </div>
                  </Link>
                </td>
                <td class="w-px border-t">
                  <Link class="flex items-center px-4" :href="`/oferta/${item.id}/edit`" tabindex="-1">
                    <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
                  </Link>
                </td>
              </tr>
              <tr v-if="oferty.length === 0">
                <td class="px-8 py-8 text-center text-gray-500 border-t" colspan="4">Brak ofert dla tego zapytania.</td>
              </tr>
            </tbody>
          </table>
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
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      form: this.$inertia.form({
        id_zapyt: this.zapytania.id_zapyt,
        user_otrzymal_id: this.zapytania.user_otrzymal_id,
        data_otrzymania: this.zapytania.data_otrzymania,
        data_zlozenia: this.zapytania.data_zlozenia,
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

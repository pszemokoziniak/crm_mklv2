<template>
  <div>
    <Head title="Nowa oferta" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/oferta">Oferty</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>

    <div v-if="wznowienieInfo" class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
      <p class="font-bold">Wznowienie</p>
      <p>Zapytanie: <strong>{{ wznowienieInfo.zapytania_nazwa }}</strong> (ID: {{ wznowienieInfo.zapytania_id }})</p>
    </div>

    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zapytanie">
            <option v-for="item in zapytanie" :key="item.id" :value="item.id">{{ item.nazwa_projektu }} </option>
          </select-input>

          <select-input v-model="form.typ" :error="form.errors.typ" class="pb-8 pr-6 w-full lg:w-1/2" label="Typ">
            <option :value="null" />
            <option value="Klient oferuje">Klient oferuje</option>
            <option value="Klienta ma kontakt">Klienta ma kontakt</option>
          </select-input>

          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }} </option>
          </select-input>

          <text-input v-model="form.data_wyslania" :error="form.errors.data_wyslania" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wysłania" />

          <text-input v-model="form.kwota" :error="form.errors.kwota" type="number" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />

          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>

          <text-input v-model="form.data_kontakt" :error="form.errors.data_kontakt" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />

          <select-input v-model="form.oferta_status_id" :error="form.errors.oferta_status_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Status">
            <option :value="null" />
            <option v-for="item in statuses" :key="item.id" :value="item.id">{{ item.name }} </option>
          </select-input>

          <text-area-input v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full" label="Opis" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Utwórz ofertę</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link} from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextAreaInput,
  },
  layout: Layout,
  props: {
    zapytanie: Object,
    clients: Object,
    statuses: Object,
    waluta: Object,
    clientById: [String, Number],
    zapytaniaById: [String, Number],
    wznowienieInfo: {
      type: Object,
      default: null,
    },
    prefillData: {
      type: Object,
      default: () => ({}),
    },
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        zapytania_id: this.zapytaniaById || this.prefillData.zapytania_id || '',
        typ: this.prefillData.typ || '',
        client_id: this.clientById || this.prefillData.client_id || '',
        data_wyslania: this.prefillData.data_wyslania || '',
        kwota: this.prefillData.kwota || '',
        waluta_id: this.prefillData.waluta_id || '',
        kurs: this.prefillData.kurs || '',
        data_kontakt: this.prefillData.data_kontakt || '',
        oferta_status_id: this.prefillData.oferta_status_id || '',
        opis: this.prefillData.opis || '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/oferta')
    },
  },
}
</script>

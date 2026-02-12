<template>
  <div>
    <Head title="Nowe zapytanie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zapytania">Zapytania</Link>
      <span class="text-gray-400 font-medium"> /</span> Nowe zapytanie
    </h1>
    <div class="max-w-5xl bg-white rounded-md shadow overflow-hidden">
      <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
        <div class="text-xl font-bold text-gray-800">
          Formularz nowego zapytania
          <span class="text-sm font-normal text-gray-500 ml-2">(Numer: {{ id_zapyt }})</span>
        </div>
      </div>
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.user_otrzymal_id" :error="form.errors.user_otrzymal_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zarejestrował">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data otrzymania" />
          <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin złożenia" />
          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa projektu" />
          <select-input v-model="form.preliminarz" :error="form.errors.preliminarz" class="pb-8 pr-6 w-full lg:w-1/2" label="Preliminarz">
            <option value="Tak">Tak</option>
            <option value="Nie">Nie</option>
          </select-input>
          <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowość" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.zakres_id" :error="form.errors.zakres_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zakres">
            <option :value="null" />
            <option v-for="item in zakres" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.user_opracowuje_id" :error="form.errors.user_opracowuje_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Opracowuje">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.start" :error="form.errors.start" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin rozpoczęcia" />
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin zakończenia" />
          <text-input v-model="form.kwota" :error="form.errors.kwota" type="number" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <text-area v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full" label="Opis" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Utwórz zapytanie</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link} from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextArea from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextArea,
  },
  layout: Layout,
  props: {
    zakres: Object,
    kraj: Object,
    users: Object,
    clients: Object,
    id_zapyt: String,
    waluta: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id_zapyt: this.id_zapyt,
        user_otrzymal_id: null,
        data_otrzymania: '',
        data_zlozenia: '',
        client_id: null,
        nazwa_projektu: '',
        preliminarz: 'Nie',
        miejscowosc: '',
        kraj_id: null,
        zakres_id: null,
        user_opracowuje_id: null,
        start: '',
        end: '',
        kwota: '',
        waluta_id: null,
        opis: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/zapytania')
    },
  },
}
</script>

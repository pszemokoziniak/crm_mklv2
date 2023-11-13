<template>
  <div>
    <Head title="Create Contact" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zapytania">Zapytania</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.user_otrzymal_id" :error="form.errors.user_otrzymal_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Otrzymał">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.data_otrzymania" :error="form.errors.data_otrzymania" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data otrzymania" />
          <text-input v-model="form.data_zlozenia" :error="form.errors.data_zlozenia" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin złożenia" />
          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Klient">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.nazwa_projektu" :error="form.errors.nazwa_projektu" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa projektu" />
          <text-input v-model="form.miejscowosc" :error="form.errors.miejscowosc" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowść" />
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
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Planowany termin zakończenia realizacji" />
          <text-input v-model="form.kwota" :error="form.errors.kwota" type="number" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          <select-input v-model="form.waluta" :error="form.errors.waluta" class="pb-8 pr-6 w-full lg:w-1/2" label="Waluta">
            <option :value="null" />
            <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.waluta }}</option>
          </select-input>
          <text-area v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz</loading-button>
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
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id_zapyt: this.id_zapyt,
        user_otrzymal_id: '',
        data_otrzymania: '',
        data_zlozenia: '',
        client_id: '',
        nazwa_projektu: '',
        miejscowosc: '',
        kraj_id: '',
        zakres_id: '',
        user_opracowuje_id: '',
        start: '',
        end: '',
        kwota: '',
        waluta: '',
        opis: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  computed() {
    console.log(this.id_zapyt)
  },
  methods: {
    store() {
      this.form.post('/zapytania')
    },
  },
}
</script>

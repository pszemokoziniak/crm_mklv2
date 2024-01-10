<template>
  <div>
    <Head :title="`${form.nazwa}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/futureproject">Przyszłe projekty</Link>
      <span class="text-indigo-400 font-medium"></span>
    </h1>
    <trashed-message v-if="futureproject.deleted_at" class="mb-6" @restore="restore"> Zapytanie zostało usunięte </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.nazwa" :error="form.errors.nazwa" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa projektu"/>
          <text-input v-model="form.miasto" :error="form.errors.miasto" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowość"/>
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.objekt_id" :error="form.errors.objekt_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Rodzaj obiektu">
            <option :value="null" />
            <option v-for="item in objekt" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Generalny wykonawca">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.start" :error="form.errors.start" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Start projektu" />
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Koniec projektu" />
          <text-area v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
          <text-input v-model="form.inwestor" :error="form.errors.inwestor" class="pb-8 pr-6 w-full lg:w-1/1" label="Inwestor" />
          <text-area v-model="form.dane_kontaktowe" :error="form.errors.dane_kontaktowe" class="pb-8 pr-6 w-full lg:w-1/1" label="Dane kontaktowe" />
          <text-input v-model="form.data_kontakt" :error="form.errors.data_kontakt" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <select-input v-model="form.faza_id" :error="form.errors.faza_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Faza projektu">
            <option :value="null" />
            <option v-for="item in faza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!futureproject.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Popraw</loading-button>
        </div>
      </form>
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
import TextArea from "@/Shared/TextareaInput.vue";

export default {
  components: {
    TextArea,
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
    futureproject: Object,
    clients: Object,
    faza: Object,
    objekt: Object,
    krajs: Object,
    users: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.futureproject.id,
        nazwa: this.futureproject.nazwa,
        miasto: this.futureproject.miasto,
        kraj_id: this.futureproject.kraj_id,
        objekt_id: this.futureproject.objekt_id,
        client_id: this.futureproject.client_id,
        start: this.futureproject.start,
        end: this.futureproject.end,
        opis: this.futureproject.opis,
        inwestor: this.futureproject.inwestor,
        dane_kontaktowe: this.futureproject.dane_kontaktowe,
        data_kontakt: this.futureproject.data_kontakt,
        faza_id: this.futureproject.faza_id,
        user_id: this.futureproject.user_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/futureproject/${this.futureproject.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz usunąć te zapytanie?')) {
        this.$inertia.delete(`/futureproject/${this.futureproject.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić zapytanie?')) {
        this.$inertia.put(`/futureproject/${this.futureproject.id}/restore`)
      }
    },
  },
}
</script>

<template>
  <div>
    <Head title="Popraw kontakt" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/kontakt/${client_id}/index`">Kontakty</Link>
      <span class="text-indigo-400 font-medium">/</span>
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full lg:w-1/2" label="Temat" />
          <TextareaInput v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
          <text-input v-model="form.call_time" :error="form.errors.call_time" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data kontaktu" />
          <select-input v-model="form.zapytania_id" :error="form.errors.zapytania_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Zapytanie">
            <option :value="null" />
            <option v-for="item in zapytanias" :key="item.id" :value="item.id">{{ item.nazwa_projektu }}</option>
          </select-input>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!kontakt.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
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
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextareaInput from "@/Shared/TextareaInput.vue";

export default {
  components: {
    TextareaInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    kontakt: Object,
    zapytanias: Object,
    client_id: String,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        subject: this.kontakt.subject,
        description: this.kontakt.description,
        zapytania_id: this.kontakt.zapytania_id,
        call_time: this.kontakt.call_time,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/kontakt/${this.kontakt.id}`)
    },
    destroy() {
      if (confirm('Aby napewno?')) {
        this.$inertia.delete(`/kontakt/${this.kontakt.id}`)
      }
    },
    // restore() {
    //   if (confirm('Are you sure you want to restore this kontakt?')) {
    //     this.$inertia.put(`/kontakt/${this.kontakt.id}/restore`)
    //   }
    // },
  },
}
</script>

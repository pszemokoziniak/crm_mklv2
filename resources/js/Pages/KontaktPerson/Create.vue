<template>
  <div>
    <Head title="Create Contact" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/kontaktperson">Osoby kontaktowe</Link>
      <span class="text-indigo-400 font-medium">/</span> Dodaj
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Imię" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwisko" />
          <text-input v-model="form.position" :error="form.errors.position" class="pb-8 pr-6 w-full lg:w-1/2" label="Stanowisko" />
          <text-input v-model="form.phone1" :error="form.errors.phone1" class="pb-8 pr-6 w-full lg:w-1/2" label="Telefon 1" />
          <text-input v-model="form.phone2" :error="form.errors.phone2" class="pb-8 pr-6 w-full lg:w-1/2" label="Telefon 2" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Dodaj</loading-button>
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
import TextArea from "@/Shared/TextareaInput.vue";

export default {
  components: {
    TextArea,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  props: {
    clients: Object,
    client_id: String,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        first_name: '',
        last_name: '',
        position: '',
        phone1: '',
        phone2: '',
        email: '',
        client_id: this.client_id,
        description: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/kontaktperson/post/'+this.client_id)
    },
  },
}
</script>

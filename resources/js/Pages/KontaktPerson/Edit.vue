<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/kontaktperson/${kontaktPerson.client_id}/index`">Osoby kontaktowe</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.first_name }} {{ form.last_name }}
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Imię" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwisko" />
          <text-input v-model="form.position" :error="form.errors.position" class="pb-8 pr-6 w-full lg:w-1/2" label="Stanowisko" />
          <text-input v-model="form.phone1" :error="form.errors.phone1" class="pb-8 pr-6 w-full lg:w-1/2" label="Telefon 1" />
          <text-input v-model="form.phone2" :error="form.errors.phone2" class="pb-8 pr-6 w-full lg:w-1/2" label="Telefon 2" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <hr>
        <div class="grid gap-1 grid-cols-3 p-5">
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <Link class="group flex items-center py-3" :href="`/kontakt/create/${form.client_id}/${kontaktPerson.id}`">
              <icon name="addContact" class="mr-2 w-4 h-4 inline"/>
              <div class="">Dodaj kontakt</div>
            </Link>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!kontaktPerson.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń osobę kontaktową</button>
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
import TextArea from "@/Shared/TextareaInput.vue";
import Icon from "@/Shared/Icon.vue";

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
  },
  layout: Layout,
  props: {
    kontaktPerson: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.id,
        first_name: this.kontaktPerson.first_name,
        last_name: this.kontaktPerson.last_name,
        position: this.kontaktPerson.position,
        phone1: this.kontaktPerson.phone1,
        phone2: this.kontaktPerson.phone2,
        email: this.kontaktPerson.email,
        client_id: this.kontaktPerson.client_id,
        description: this.kontaktPerson.description,
        user_id: this.kontaktPerson.user_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/kontaktperson/${this.kontaktPerson.id}`)
    },
    destroy() {
      if (confirm('Czy aby na pewno?')) {
        this.$inertia.delete(`/kontaktperson/${this.kontaktPerson.id}`)
      }
    },
  },
}
</script>

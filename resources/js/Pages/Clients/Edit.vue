<template>
  <div>
    <Head :title="`${form.nazwa}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/clients">Klient</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.nazwa }}
    </h1>
    <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore"> Klient został usunięty </trashed-message>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.nazwa" :error="form.errors.nazwa" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />
          <text-input v-model="form.ulica" :error="form.errors.ulica" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Ulica" />
          <text-input v-model="form.miasto" :error="form.errors.miasto" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Miasto" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <text-input v-model="form.www" :error="form.errors.www" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="WWW" />
          <text-input v-model="form.linkedIn" :error="form.errors.linkedIn" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="LinkedIn" />
          <select-input v-model="form.branza_id" :error="form.errors.branza_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Branża">
            <option :value="null" />
            <option v-for="item in branza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.user_id" :error="form.errors.user_id" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Użytkownik">
            <option :value="null" />
            <option v-for="item in user" :key="item.id" :value="item.id">{{ item.first_name }} {{ item.last_name }}</option>
          </select-input>
          <text-area-input v-model="form.message" :error="form.errors.message" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/1" label="Informacje" />
        </div>
        <hr>
        <div class="grid gap-1 grid-cols-3 p-5">
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 cursor-default" @click="disableForm">
            <div class="group flex items-center py-3 cursor-pointer" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 inline"/>
              <div class="">Edytuj dane</div>
            </div>
          </div>
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <Link class="group flex items-center py-3" :href="`/kontaktperson/${client_id}/index`">
              <icon name="addPerson" class="mr-2 w-4 h-4 inline"/>
              <div class="">Osoby kontaktowe</div>
            </Link>
          </div>
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">
            <Link class="group flex items-center py-3" :href="`/kontakt/${client_id}/index`">
              <icon name="addContact" class="mr-2 w-4 h-4 inline"/>
              <div class="">Dodaj kontakt</div>
            </Link>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!client.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
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
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from "@/Shared/Icon.vue";

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
  },
  layout: Layout,
  props: {
    client: Object,
    branza: Object,
    kraj: Object,
    user: Object,
    client_id: String,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
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
      this.form.put(`/clients/${this.client.id}`)
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
      let elems_input = document.getElementById('form').getElementsByTagName('input');
      for(let i = 0; i < elems_input.length; i++) {
        elems_input[i].disabled = false;
      }
      let elems_select = document.getElementById('form').getElementsByTagName('select');
      for(let i = 0; i < elems_select.length; i++) {
        elems_select[i].disabled = false;
      }
    },
    contactPerson() {
      this.form.get(`/kontaktperson/${this.client.id}/index`)
    }
  },
}
</script>

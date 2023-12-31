<template>
  <div>
    <Head :title="`${form.nazwa}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/clients">Klient</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.nazwa }}
    </h1>
    <trashed-message v-if="client.deleted_at" class="mb-6" @restore="restore"> Klient został usunięty </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.nazwa" :error="form.errors.nazwa" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />
          <text-input v-model="form.ulica" :error="form.errors.ulica" class="pb-8 pr-6 w-full lg:w-1/2" label="Ulica" />
          <text-input v-model="form.miasto" :error="form.errors.miasto" class="pb-8 pr-6 w-full lg:w-1/2" label="Miasto" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <text-input v-model="form.www" :error="form.errors.www" class="pb-8 pr-6 w-full lg:w-1/2" label="WWW" />
          <text-input v-model="form.linkedIn" :error="form.errors.linkedIn" class="pb-8 pr-6 w-full lg:w-1/2" label="LinkedIn" />
          <select-input v-model="form.branza_id" :error="form.errors.branza_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Branża">
            <option :value="null" />
            <option v-for="item in branza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.user_id" :error="form.errors.user_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Użytkownik">
            <option :value="null" />
            <option v-for="item in user" :key="item.id" :value="item.id">{{ item.first_name }} {{ item.last_name }}</option>
          </select-input>
          <text-area-input v-model="form.message" :error="form.errors.message" class="pb-8 pr-6 w-full lg:w-1/1" label="Informacje" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!client.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
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

export default {
  components: {
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
  },
  remember: 'form',
  data() {
    return {
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
  },
}
</script>

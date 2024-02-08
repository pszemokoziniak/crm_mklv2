<template>
  <div>
    <Head title="Email Popraw" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/email">Email</Link>
<!--      <span class="text-indigo-400 font-medium">/</span>-->
<!--      {{ form.name }}-->
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.user_id" :error="form.errors.waluta_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Użytkownik">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <select-input v-model="form.type_id" :error="form.errors.type_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Typ Email">
            <option :value="null" />
            <option v-for="item in emailsType" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!email.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zmień</loading-button>
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

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    email: Object,
    users: Object,
    emailsType: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.email.id,
        user_id: this.email.user_id,
        type_id: this.email.type_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/email/${this.email.id}`)
    },
    destroy() {
      if (confirm('Czy aby napewno?')) {
        this.$inertia.delete(`/email/${this.email.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić rekord?')) {
        this.$inertia.put(`/email/${this.email.id}/restore`)
      }
    },
  },
}
</script>

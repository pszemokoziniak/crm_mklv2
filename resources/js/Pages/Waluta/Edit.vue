<template>
  <div>
    <Head title="Waluta" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/waluta">Waluta</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
<!--    <trashed-message v-if="zakres.deleted_at" class="mb-6" @restore="restore"> Ten zakres został usunięty. </trashed-message>-->
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
<!--          <button v-if="!waluta.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>-->
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
    waluta: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.waluta.id,
        name: this.waluta.name,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/waluta/${this.waluta.id}`)
    },
    destroy() {
      if (confirm('Czy aby napewno?')) {
        this.$inertia.delete(`/waluta/${this.waluta.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić rekord?')) {
        this.$inertia.put(`/waluta/${this.waluta.id}/restore`)
      }
    },
  },
}
</script>

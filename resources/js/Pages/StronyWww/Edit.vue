<template>
  <div>
    <Head :title="`${form.name}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/stronywww">Strony WWW</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="stronywww.deleted_at" class="mb-6" @restore="restore"> This faza has been deleted. </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa" />
          <text-input v-model="form.link" :error="form.errors.link" class="pb-8 pr-6 w-full lg:w-1/1" label="Link" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!stronywww.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń</button>
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
    stronywww: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.stronywww.id,
        name: this.stronywww.name,
        link: this.stronywww.link,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/stronywww/${this.stronywww.id}`)
    },
    destroy() {
      if (confirm('Czy aby napewno?')) {
        this.$inertia.delete(`/stronywww/${this.stronywww.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić rekord?')) {
        this.$inertia.put(`/stronywww/${this.stronywww.id}/restore`)
      }
    },
  },
}
</script>

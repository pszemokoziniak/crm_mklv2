<template>
  <div>
    <Head title="Dodaj Uprawnienie" />
    <h1 class="mb-8 font-bold text-3xl">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/uprawnienia">Uprawnienia</Link>
      <span class="text-indigo-400 font-medium">/</span> Dodaj
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />
          <!-- Removed description input -->
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Dodaj Uprawnienie</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    TextInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        // Removed description from form data
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/uprawnienia', {
        onSuccess: () => this.form.reset('name'), // Removed description from reset
      })
    },
  },
}
</script>

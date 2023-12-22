<template>
  <div>
    <Head title="LinkedIn" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/faza">LinkedIn</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa" />
          <text-input v-model="form.link" :error="form.errors.link" class="pb-8 pr-6 w-full lg:w-1/1" label="Link" />
        </div>

<!--        <div class="flex flex-wrap -mb-8 -mr-6 p-8">-->
<!--          <text-input v-model="form.updated_at" :error="form.errors.updated_at" class="pb-8 pr-6 w-full lg:w-1/2" label="Update" />-->
<!--        </div>-->
<!--        <div class="flex flex-wrap -mb-8 -mr-6 p-8">-->
<!--          <text-input v-model="form.click" :error="form.errors.click" class="pb-8 pr-6 w-full lg:w-1/2" label="Click" />-->
<!--        </div>-->
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
import TextArea from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextArea,
  },
  layout: Layout,
  props: {
    // organizations: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        link: '',
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/linkedin')
    },
  },
}
</script>

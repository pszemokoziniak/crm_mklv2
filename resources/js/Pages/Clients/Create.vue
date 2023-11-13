<template>
  <div>
    <Head title="Create Contact" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/clients">Klienci</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.nazwa" :error="form.errors.nazwa" class="pb-8 pr-6 w-full lg:w-1/2" label="Firma" />
          <text-input v-model="form.ulica" :error="form.errors.ulica" class="pb-8 pr-6 w-full lg:w-1/2" label="Ulica" />
          <text-input v-model="form.miasto" :error="form.errors.miasto" class="pb-8 pr-6 w-full lg:w-1/2" label="Miasto" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in kraj" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
<!--          <text-input v-model="form.kraj" :error="form.errors.kraj" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj" />-->
          <text-input v-model="form.www" :error="form.errors.www" class="pb-8 pr-6 w-full lg:w-1/2" label="WWW" />
          <text-input v-model="form.linkedin" :error="form.errors.linkedin" class="pb-8 pr-6 w-full lg:w-1/2" label="LinkedIn" />
          <select-input v-model="form.branza_id" :error="form.errors.branza_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Branża">
            <option :value="null" />
            <option v-for="item in branza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <text-area v-model="form.message" :error="form.errors.message" class="pb-8 pr-6 w-full lg:w-full" label="Informacje" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link} from '@inertiajs/inertia-vue3'
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
    branza: Array,
    kraj: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        nazwa: '',
        ulica: '',
        miasto: '',
        kraj_id: '',
        www: '',
        linkedin: '',
        branza_id: null,
        message: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/clients')
    },
  },
}
</script>

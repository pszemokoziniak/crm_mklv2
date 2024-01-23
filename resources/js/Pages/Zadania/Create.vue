<template>
  <div>
    <Head title="Utwórz zadanie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zadania">Zadania</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.responsible_person_id" :error="form.errors.responsible_person_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Osoba odpowiedzialna">
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.deadline" :error="form.errors.deadline" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wykonania" />
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full lg:w-1/1" label="Temat" />
          <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
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
    users: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        responsible_person_id: '',
        subject: '',
        description: '',
        deadline: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/zadania')
    },
  },
}
</script>

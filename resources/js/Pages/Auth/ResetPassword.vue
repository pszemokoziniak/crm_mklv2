<template>
  <Head title="Resetuj hasło" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-bold">Nowe hasło</h1>
          <div class="mt-6 mx-auto w-24 border-b-2" />

          <text-input v-model="form.email" :error="form.errors.email" class="mt-10" label="Email" type="email" autofocus />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Nowe hasło" type="password" />
          <text-input v-model="form.password_confirmation" :error="form.errors.password_confirmation" class="mt-6" label="Potwierdź hasło" type="password" />
        </div>
        <div class="flex items-center justify-end px-10 py-4 bg-gray-100 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">
            Zmień hasło
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    email: String,
    token: String,
  },
  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: '',
      }),
    }
  },
  methods: {
    submit() {
      this.form.post('/reset-password', {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
}
</script>

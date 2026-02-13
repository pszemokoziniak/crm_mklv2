<template>
  <Head title="Zapomniałeś hasła" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-bold">Resetuj hasło</h1>
          <div class="mt-6 mx-auto w-24 border-b-2" />

          <div class="mt-10 text-sm text-gray-600">
            Zapomniałeś hasła? Żaden problem. Podaj nam swój adres e-mail, a wyślemy Ci link do resetowania hasła, który pozwoli Ci wybrać nowe.
          </div>

          <div v-if="status" class="mt-4 font-medium text-sm text-green-600">
            {{ status }}
          </div>

          <text-input v-model="form.email" :error="form.errors.email" class="mt-6" label="Email" type="email" autofocus autocapitalize="off" />
        </div>
        <div class="flex items-center justify-end px-10 py-4 bg-gray-100 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">
            Wyślij link do resetu
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
    status: String,
  },
  data() {
    return {
      form: this.$inertia.form({
        email: '',
      }),
    }
  },
  methods: {
    submit() {
      this.form.post('/forgot-password')
    },
  },
}
</script>

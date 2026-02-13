<template>
  <Head title="Login" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-gradient-to-br from-indigo-600 to-indigo-900 overflow-hidden">
    <div class="w-full max-w-md transition-all duration-1000 transform" :class="{'translate-y-0 opacity-100': loaded, 'translate-y-12 opacity-0': !loaded}">
      <logo class="block mx-auto w-full max-w-xs fill-white drop-shadow-2xl animate-pulse" height="60" />

      <form
        class="mt-10 bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 transition-all duration-500 transform hover:scale-[1.02] hover:shadow-[0_35px_60px_-15px_rgba(0,0,0,0.3)] ease-out"
        @submit.prevent="login"
      >
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight">Witaj ponownie</h1>
          <div class="mt-4 mx-auto w-16 h-1 bg-indigo-500 rounded-full" />

          <div v-if="status" class="mt-6 p-3 bg-green-50 border border-green-100 rounded-lg font-medium text-sm text-green-700 text-center animate-bounce">
            {{ status }}
          </div>

          <text-input v-model="form.email" :error="form.errors.email" class="mt-10 focus-within:scale-[1.01] transition-transform" label="Email" type="email" autofocus autocapitalize="off" />
          <text-input v-model="form.password" :error="form.errors.password" class="mt-6 focus-within:scale-[1.01] transition-transform" label="Hasło" type="password" />

          <div class="flex items-center justify-between mt-8">
            <label class="flex items-center group cursor-pointer select-none" for="remember">
              <input id="remember" v-model="form.remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-transform group-active:scale-90" type="checkbox" />
              <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Zapamiętaj mnie</span>
            </label>
            <Link href="/forgot-password" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors hover:underline">
              Zapomniałeś hasła?
            </Link>
          </div>

          <div v-if="$page.props.flash.error" class="mt-6 p-4 bg-red-50 border border-red-100 rounded-lg flex items-center text-red-700 text-sm animate-shake">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            {{ $page.props.flash.error }}
          </div>
        </div>
        <div class="flex px-10 py-6 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo w-full flex justify-center py-3 text-lg font-semibold shadow-md hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 transition-all duration-150" type="submit">
            Zaloguj się
          </loading-button>
        </div>
      </form>
      <p class="mt-8 text-center text-indigo-200 text-sm opacity-75">
        &copy; {{ new Date().getFullYear() }} MKL CRM. Wszystkie prawa zastrzeżone.
      </p>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Logo from '@/Shared/Logo'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    status: String,
    rememberedEmail: String,
  },
  data() {
    return {
      loaded: false,
      form: this.$inertia.form({
        email: this.rememberedEmail || '',
        password: '',
        remember: !!this.rememberedEmail,
      }),
    }
  },
  mounted() {
    // Małe opóźnienie dla efektu wejścia
    setTimeout(() => {
      this.loaded = true
    }, 100)
  },
  methods: {
    login() {
      this.form.post('/login')
    },
  },
}
</script>

<style scoped>
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
.animate-shake {
  animation: shake 0.2s ease-in-out 0s 2;
}
</style>

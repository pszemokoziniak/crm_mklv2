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
          <!-- Podpowiedź: ta sama lub podobna firma już jest w bazie (także w Archiwum) -->
          <div v-if="similar.length" class="-mt-4 pb-8 pr-6 w-full">
            <div class="px-4 py-3 text-sm rounded-md border" :class="hasExact ? 'border-red-200 bg-red-50' : 'border-yellow-200 bg-yellow-50'">
              <p class="mb-1 font-semibold" :class="hasExact ? 'text-red-700' : 'text-yellow-800'">
                {{ hasExact ? 'Ta firma już jest w bazie — nie dodawaj jej ponownie:' : 'Podobne firmy są już w bazie — sprawdź, czy to nie ta sama:' }}
              </p>
              <ul class="space-y-1">
                <li v-for="item in similar" :key="item.id" class="flex items-center">
                  <a :href="`/clients/${item.id}/edit`" target="_blank" class="text-indigo-600 hover:underline">{{ item.nazwa }}</a>
                  <span v-if="item.archived" class="ml-2 px-1.5 py-0.5 text-xs text-gray-700 bg-gray-200 rounded">Archiwum</span>
                </li>
              </ul>
            </div>
          </div>
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
import debounce from 'lodash/debounce'

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
      similar: [],
      checkSeq: 0,
    }
  },
  computed: {
    hasExact() {
      return this.similar.some((item) => item.exact)
    },
  },
  watch: {
    'form.nazwa': debounce(function (value) {
      this.checkName(value)
    }, 300),
  },
  methods: {
    /** Pyta serwer o firmy o tej samej/podobnej nazwie (także w Archiwum). */
    async checkName(value) {
      const seq = ++this.checkSeq

      if (!value || value.trim().length < 3) {
        this.similar = []
        return
      }

      try {
        const response = await fetch(`/clients/check-name?nazwa=${encodeURIComponent(value)}`, {
          headers: { Accept: 'application/json' },
        })
        const data = response.ok ? await response.json() : []

        // Odpowiedź do starszego zapytania (użytkownik pisał dalej) — ignorujemy.
        if (seq === this.checkSeq) {
          this.similar = data
        }
      } catch (e) {
        if (seq === this.checkSeq) {
          this.similar = []
        }
      }
    },
    store() {
      this.form.post('/clients')
    },
  },
}
</script>

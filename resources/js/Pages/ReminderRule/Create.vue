<template>
  <div>
    <Head title="Nowa reguła przypomnień" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/reminder-rules">Przypomnienia mailowe</Link>
      <span class="text-indigo-400 font-medium">/</span> Nowa reguła
    </h1>

    <div class="max-w-4xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa reguły" />

          <select-input v-model="form.event" :error="form.errors.event" class="pb-8 pr-6 w-full lg:w-1/2" label="Zdarzenie">
            <option :value="''" />
            <option v-for="(label, key) in events" :key="key" :value="key">{{ label }}</option>
          </select-input>

          <text-input v-model.number="form.days_before" :error="form.errors.days_before" type="number" min="0" max="365" class="pb-8 pr-6 w-full lg:w-1/2" label="Dni przed terminem" />

          <div class="pb-8 pr-6 w-full lg:w-1/2">
            <label class="form-label">Aktywna:</label>
            <label class="inline-flex items-center mt-2">
              <input v-model="form.active" type="checkbox" class="form-checkbox" />
              <span class="ml-2">Reguła włączona</span>
            </label>
          </div>

          <div class="pb-8 pr-6 w-full">
            <label class="form-label">Odbiorcy:</label>
            <div class="mt-2 space-y-2">
              <label class="flex items-center">
                <input type="checkbox" :checked="hasRecipient('opiekun')" class="form-checkbox" @change="toggleRecipient('opiekun')" />
                <span class="ml-2">Opiekun klienta</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" :checked="hasRecipient('opracowuje')" class="form-checkbox" @change="toggleRecipient('opracowuje')" />
                <span class="ml-2">Osoba opracowująca</span>
              </label>
            </div>
            <div v-if="form.errors.recipients" class="form-error mt-2">{{ form.errors.recipients }}</div>

            <label class="form-label mt-4">Dodatkowi użytkownicy:</label>
            <select v-model="selectedUsers" multiple class="form-select w-full" size="6" @change="syncUsers">
              <option v-for="u in users" :key="u.id" :value="u.id">{{ u.last_name }} {{ u.first_name }}</option>
            </select>
            <p class="text-xs text-gray-500 mt-1">Przytrzymaj Ctrl/Cmd, aby wybrać wielu.</p>
          </div>

          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full" label="Temat maila" />

          <text-area-input v-model="form.body" :error="form.errors.body" rows="10" class="pb-8 pr-6 w-full" label="Treść maila" />

          <div v-if="currentPlaceholders.length" class="pb-8 pr-6 w-full">
            <label class="form-label">Dostępne placeholdery:</label>
            <div class="flex flex-wrap gap-2 mt-2">
              <code v-for="p in currentPlaceholders" :key="p" class="bg-gray-100 px-2 py-1 rounded text-sm cursor-pointer" @click="insertPlaceholder(p)">{{ p }}</code>
            </div>
            <p class="text-xs text-gray-500 mt-1">Kliknij placeholder, aby wstawić go do treści.</p>
          </div>
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz regułę</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: { Head, Link, TextInput, TextAreaInput, SelectInput, LoadingButton },
  layout: Layout,
  props: {
    events: Object,
    users: Array,
    placeholders: Object,
  },
  data() {
    return {
      selectedUsers: [],
      form: this.$inertia.form({
        name: '',
        event: '',
        days_before: 3,
        recipients: [],
        subject: '',
        body: '',
        active: true,
      }),
    }
  },
  computed: {
    currentPlaceholders() {
      return this.form.event && this.placeholders[this.form.event] ? this.placeholders[this.form.event] : []
    },
  },
  methods: {
    hasRecipient(key) {
      return this.form.recipients.includes(key)
    },
    toggleRecipient(key) {
      const idx = this.form.recipients.indexOf(key)
      if (idx === -1) this.form.recipients.push(key)
      else this.form.recipients.splice(idx, 1)
    },
    syncUsers() {
      this.form.recipients = this.form.recipients.filter(r => !r.startsWith('user:'))
      this.selectedUsers.forEach(id => this.form.recipients.push(`user:${id}`))
    },
    insertPlaceholder(p) {
      this.form.body = (this.form.body || '') + p
    },
    store() {
      this.form.post('/reminder-rules')
    },
  },
}
</script>

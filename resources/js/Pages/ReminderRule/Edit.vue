<template>
  <div>
    <Head title="Edycja reguły przypomnień" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/reminder-rules">Przypomnienia mailowe</Link>
      <span class="text-indigo-400 font-medium">/</span> {{ rule.name }}
    </h1>

    <div class="max-w-4xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
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

          <div class="pb-8 pr-6 w-full">
            <wysiwyg-editor ref="bodyEditor" v-model="form.body" :error="form.errors.body" label="Treść maila" />
          </div>

          <div v-if="currentPlaceholders.length" class="pb-8 pr-6 w-full">
            <label class="form-label">Dostępne placeholdery:</label>
            <div class="flex flex-wrap gap-2 mt-2">
              <code v-for="p in currentPlaceholders" :key="p" class="bg-gray-100 px-2 py-1 rounded text-sm cursor-pointer" @click="insertPlaceholder(p)">{{ p }}</code>
            </div>
            <p class="text-xs text-gray-500 mt-1">Kliknij placeholder, aby wstawić go w miejscu kursora w edytorze.</p>
          </div>
        </div>
        <div class="flex items-center justify-between px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button type="button" class="text-red-600 hover:underline" @click="destroy">Usuń regułę</button>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz zmiany</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import WysiwygEditor from '@/Shared/WysiwygEditor.vue'

export default {
  components: { Head, Link, TextInput, SelectInput, LoadingButton, WysiwygEditor },
  layout: Layout,
  props: {
    rule: Object,
    events: Object,
    users: Array,
    placeholders: Object,
  },
  data() {
    const userTokens = (this.rule.recipients || []).filter(r => r && r.startsWith('user:'))
    return {
      selectedUsers: userTokens.map(t => parseInt(t.slice(5), 10)),
      form: this.$inertia.form({
        name: this.rule.name,
        event: this.rule.event,
        days_before: this.rule.days_before,
        recipients: [...(this.rule.recipients || [])],
        subject: this.rule.subject,
        body: this.rule.body,
        active: !!this.rule.active,
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
      if (this.$refs.bodyEditor && this.$refs.bodyEditor.insertAtCursor) {
        this.$refs.bodyEditor.insertAtCursor(p)
      } else {
        this.form.body = (this.form.body || '') + p
      }
    },
    update() {
      this.form.put(`/reminder-rules/${this.rule.id}`)
    },
    destroy() {
      if (!confirm('Na pewno usunąć tę regułę?')) return
      Inertia.delete(`/reminder-rules/${this.rule.id}`)
    },
  },
}
</script>

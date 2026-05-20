<template>
  <div>
    <Head title="Edycja reguły powiadomień" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/reminder-rules">Powiadomienia i przypomnienia</Link>
      <span class="text-indigo-400 font-medium">/</span> {{ rule.name }}
    </h1>

    <form class="max-w-4xl space-y-6" @submit.prevent="update">
      <!-- Sekcja: Podstawowe -->
      <section class="bg-white rounded-lg shadow overflow-hidden">
        <header class="px-8 pt-6 pb-2">
          <h2 class="text-lg font-semibold text-gray-800">Podstawowe</h2>
          <p class="text-sm text-gray-500">Nazwa reguły, zdarzenie, kiedy wysyłać.</p>
        </header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 pt-4">
          <text-input v-model="form.name" :error="form.errors.name" label="Nazwa reguły" />

          <select-input v-model="form.event" :error="form.errors.event" label="Zdarzenie">
            <option :value="''">— wybierz —</option>
            <option v-for="(label, key) in events" :key="key" :value="key">{{ label }}</option>
          </select-input>

          <select-input v-if="currentEventFilters" v-model="form.event_filter" :error="form.errors.event_filter" label="Zakres">
            <option v-for="(label, key) in currentEventFilters" :key="key" :value="key">{{ label }}</option>
          </select-input>

          <div v-if="!isImmediate">
            <label class="form-label">Dni przed terminem</label>
            <div class="flex items-center gap-3">
              <button type="button" class="w-9 h-9 rounded-md bg-gray-100 hover:bg-gray-200 text-lg font-bold text-gray-600" @click="adjustDays(-1)">−</button>
              <input v-model.number="form.days_before" type="number" min="0" max="365" class="form-input text-center font-semibold text-lg" style="width: 5rem" />
              <button type="button" class="w-9 h-9 rounded-md bg-gray-100 hover:bg-gray-200 text-lg font-bold text-gray-600" @click="adjustDays(1)">+</button>
              <span class="text-sm text-gray-500">{{ daysHint }}</span>
            </div>
            <div v-if="form.errors.days_before" class="form-error">{{ form.errors.days_before }}</div>
          </div>
          <div v-else>
            <label class="form-label">Moment wysyłki</label>
            <p class="text-sm text-gray-700 mt-2">Powiadomienie zostanie wysłane <strong>natychmiast</strong> po wystąpieniu zdarzenia.</p>
          </div>

          <div>
            <label class="form-label">Status</label>
            <button type="button" class="flex items-center gap-3 mt-1" @click="form.active = !form.active">
              <span class="relative inline-flex h-6 w-11 rounded-full transition" :class="form.active ? 'bg-indigo-500' : 'bg-gray-300'">
                <span class="inline-block h-5 w-5 bg-white rounded-full shadow transform transition" :class="form.active ? 'translate-x-5' : 'translate-x-0.5'" style="margin-top: 2px" />
              </span>
              <span class="text-sm" :class="form.active ? 'text-gray-800 font-medium' : 'text-gray-500'">{{ form.active ? 'Aktywna' : 'Wyłączona' }}</span>
            </button>
          </div>
        </div>
      </section>

      <!-- Sekcja: Kanały -->
      <section class="bg-white rounded-lg shadow overflow-hidden">
        <header class="px-8 pt-6 pb-2">
          <h2 class="text-lg font-semibold text-gray-800">Kanały powiadomień</h2>
          <p class="text-sm text-gray-500">Możesz wybrać kilka. Push działa tylko, jeśli użytkownik wcześniej zezwolił przeglądarce na powiadomienia.</p>
        </header>
        <div class="p-8 pt-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button
              v-for="ch in channelTiles"
              :key="ch.key"
              type="button"
              class="text-left rounded-lg border-2 p-4 transition relative"
              :class="hasChannel(ch.key) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'"
              @click="toggleChannel(ch.key)"
            >
              <div class="flex items-start justify-between">
                <span class="font-semibold text-gray-800">{{ ch.title }}</span>
                <span v-if="hasChannel(ch.key)" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-500 text-white text-xs">✓</span>
                <span v-else class="inline-block w-5 h-5 rounded-full border border-gray-300" />
              </div>
              <p class="text-sm text-gray-500 mt-1">{{ ch.desc }}</p>
            </button>
          </div>
          <div v-if="form.errors.channels" class="form-error mt-3">{{ form.errors.channels }}</div>
        </div>
      </section>

      <!-- Sekcja: Odbiorcy -->
      <section class="bg-white rounded-lg shadow overflow-hidden">
        <header class="px-8 pt-6 pb-2">
          <h2 class="text-lg font-semibold text-gray-800">Odbiorcy</h2>
          <p class="text-sm text-gray-500">Kto otrzyma powiadomienie.</p>
        </header>
        <div class="p-8 pt-4 space-y-5">
          <div>
            <label class="form-label">Według roli w rekordzie</label>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="opt in recipientPills"
                :key="opt.key"
                type="button"
                class="px-3 py-2 rounded-full text-sm border transition"
                :class="hasRecipient(opt.key) ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-300'"
                @click="toggleRecipient(opt.key)"
              >
                <span v-if="hasRecipient(opt.key)" class="mr-1">✓</span>{{ opt.label }}
              </button>
            </div>
          </div>

          <div v-if="form.errors.recipients" class="form-error">{{ form.errors.recipients }}</div>

          <div>
            <label class="form-label">Dodatkowi użytkownicy</label>
            <div v-if="selectedUserChips.length" class="flex flex-wrap gap-2 mb-2">
              <span v-for="u in selectedUserChips" :key="u.id" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-sm">
                {{ u.last_name }} {{ u.first_name }}
                <button type="button" class="text-indigo-500 hover:text-indigo-800 font-bold" @click="removeUser(u.id)">×</button>
              </span>
            </div>
            <select v-model="addUserId" class="form-select w-full md:w-1/2" @change="addUser">
              <option :value="''">+ Dodaj użytkownika…</option>
              <option v-for="u in availableUsers" :key="u.id" :value="u.id">{{ u.last_name }} {{ u.first_name }}</option>
            </select>
          </div>
        </div>
      </section>

      <!-- Sekcja: Treść -->
      <section class="bg-white rounded-lg shadow overflow-hidden">
        <header class="px-8 pt-6 pb-2">
          <h2 class="text-lg font-semibold text-gray-800">Treść</h2>
          <p class="text-sm text-gray-500">Tytuł jest używany jako temat maila i tytuł powiadomienia push. Treść tylko w mailu.</p>
        </header>
        <div class="p-8 pt-4 space-y-6">
          <text-input v-model="form.subject" :error="form.errors.subject" label="Tytuł / temat" />

          <wysiwyg-editor ref="bodyEditor" v-model="form.body" :error="form.errors.body" label="Treść maila" />

          <div v-if="currentPlaceholders.length">
            <label class="form-label">Dostępne placeholdery</label>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="p in currentPlaceholders"
                :key="p"
                type="button"
                class="bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 px-2 py-1 rounded text-sm font-mono"
                @click="insertPlaceholder(p)"
              >
                {{ p }}
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-2">Kliknij placeholder, aby wstawić go w miejscu kursora w edytorze.</p>
          </div>
        </div>
      </section>

      <div class="flex items-center justify-between">
        <button type="button" class="text-red-600 hover:underline" @click="destroy">Usuń regułę</button>
        <div class="flex items-center gap-3">
          <Link href="/reminder-rules" class="text-gray-600 hover:underline">Anuluj</Link>
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz zmiany</loading-button>
        </div>
      </div>
    </form>
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
    channels: Object,
    immediateEvents: { type: Array, default: () => [] },
    eventFilters: { type: Object, default: () => ({}) },
    users: Array,
    placeholders: Object,
  },
  data() {
    return {
      addUserId: '',
      form: this.$inertia.form({
        name: this.rule.name,
        event: this.rule.event,
        event_filter: this.rule.event_filter || '',
        days_before: this.rule.days_before,
        recipients: [...(this.rule.recipients || [])],
        channels: [...((this.rule.channels && this.rule.channels.length) ? this.rule.channels : ['mail'])],
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
    channelTiles() {
      const descs = {
        mail: 'Wiadomość e-mail wysyłana z systemu.',
        webpush: 'Powiadomienie w przeglądarce — z dźwiękiem, nawet gdy aplikacja nie jest otwarta.',
        database: 'Czerwona kropka i wpis w dzwonku w nagłówku aplikacji.',
      }
      return Object.keys(this.channels).map(key => ({
        key,
        title: this.channels[key],
        desc: descs[key] || '',
      }))
    },
    recipientPills() {
      const base = [
        { key: 'opiekun', label: 'Opiekun klienta' },
        { key: 'opracowuje', label: 'Osoba opracowująca' },
      ]
      if (this.form.event === 'zadania_deadline' || this.form.event === 'zadania_utworzone') {
        base.push({ key: 'osoba_odpowiedzialna', label: 'Osoba odpowiedzialna (zadanie)' })
      }
      return base
    },
    isImmediate() {
      return this.immediateEvents.includes(this.form.event)
    },
    currentEventFilters() {
      return this.form.event && this.eventFilters[this.form.event] ? this.eventFilters[this.form.event] : null
    },
    daysHint() {
      const d = parseInt(this.form.days_before, 10)
      if (isNaN(d)) return ''
      if (d === 0) return 'wysyłka w dniu terminu'
      if (d === 1) return '1 dzień przed terminem'
      return `${d} dni przed terminem`
    },
    selectedUserIds() {
      return this.form.recipients
        .filter(r => r && r.startsWith('user:'))
        .map(r => parseInt(r.slice(5), 10))
    },
    selectedUserChips() {
      const ids = new Set(this.selectedUserIds)
      return this.users.filter(u => ids.has(u.id))
    },
    availableUsers() {
      const ids = new Set(this.selectedUserIds)
      return this.users.filter(u => !ids.has(u.id))
    },
  },
  watch: {
    isImmediate(now) {
      if (now) {
        this.form.days_before = 0
      }
    },
    'form.event'() {
      if (!this.currentEventFilters) {
        this.form.event_filter = ''
      }
    },
  },
  methods: {
    adjustDays(delta) {
      const next = Math.max(0, Math.min(365, (parseInt(this.form.days_before, 10) || 0) + delta))
      this.form.days_before = next
    },
    hasRecipient(key) {
      return this.form.recipients.includes(key)
    },
    toggleRecipient(key) {
      const idx = this.form.recipients.indexOf(key)
      if (idx === -1) this.form.recipients.push(key)
      else this.form.recipients.splice(idx, 1)
    },
    hasChannel(key) {
      return this.form.channels.includes(key)
    },
    toggleChannel(key) {
      const idx = this.form.channels.indexOf(key)
      if (idx === -1) this.form.channels.push(key)
      else this.form.channels.splice(idx, 1)
    },
    addUser() {
      const id = parseInt(this.addUserId, 10)
      if (!id) return
      if (!this.form.recipients.includes(`user:${id}`)) {
        this.form.recipients.push(`user:${id}`)
      }
      this.addUserId = ''
    },
    removeUser(id) {
      this.form.recipients = this.form.recipients.filter(r => r !== `user:${id}`)
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

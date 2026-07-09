<template>
  <div class="mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50 bg-gray-50/30">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
            <span class="text-lg">💬</span>
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-800">Notatki wewnętrzne</h2>
            <p class="text-[10px] text-gray-500">Widoczne tylko dla pracowników. Wpisz <code class="bg-gray-100 px-1 rounded">@Imię Nazwisko</code> żeby zawiadomić kolegę.</p>
          </div>
        </div>
        <span class="text-xs text-gray-400 font-medium">{{ notes.length }} {{ notes.length === 1 ? 'wpis' : 'wpisów' }}</span>
      </div>

      <!-- Formularz nowej notatki -->
      <div class="p-6 border-b border-gray-50 bg-gray-50/20">
        <div class="relative">
          <textarea
            ref="textarea"
            v-model="newBody"
            rows="3"
            placeholder="Napisz notatkę... użyj @ żeby wspomnieć kolegę"
            class="w-full text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 resize-none"
            @input="onInput"
            @keydown.down.prevent="handleKey('down', $event)"
            @keydown.up.prevent="handleKey('up', $event)"
            @keydown.enter="handleEnter($event)"
            @keydown.escape="closeMention"
          />
          <!-- Dropdown mention -->
          <div v-if="mentionOpen && filteredMentions.length" class="absolute left-0 mt-1 w-64 bg-white rounded-lg shadow-xl border border-gray-100 max-h-56 overflow-y-auto z-10">
            <button
              v-for="(u, i) in filteredMentions"
              :key="u.id"
              type="button"
              class="w-full text-left px-3 py-2 text-xs hover:bg-indigo-50 transition-colors flex items-center gap-2"
              :class="{ 'bg-indigo-50': i === mentionIndex }"
              @click="pickMention(u)"
              @mouseenter="mentionIndex = i"
            >
              <span class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-[10px]">{{ initials(u.label) }}</span>
              <span class="font-medium">{{ u.label }}</span>
            </button>
          </div>
        </div>
        <div class="flex items-center justify-end mt-3 gap-3">
          <span v-if="newBody.length > 9500" class="text-[10px] text-rose-500">{{ 10000 - newBody.length }} znaków pozostało</span>
          <button
            type="button"
            :disabled="!newBody.trim() || processing"
            class="btn-indigo text-sm px-5 py-2 rounded-lg shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
            @click="save"
          >
            {{ processing ? 'Zapisywanie...' : 'Dodaj notatkę' }}
          </button>
        </div>
      </div>

      <!-- Lista notatek -->
      <div class="divide-y divide-gray-50">
        <div v-if="notes.length === 0" class="p-8 text-center text-sm text-gray-400 italic">
          Brak notatek. Bądź pierwszy!
        </div>
        <div v-for="n in notes" :key="n.id" class="p-6 hover:bg-gray-50/30 transition-colors">
          <div class="flex items-start gap-3">
            <span class="flex-shrink-0 w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-xs">
              {{ initials(n.author ? `${n.author.first_name} ${n.author.last_name}` : '?') }}
            </span>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-bold text-gray-800">{{ n.author ? `${n.author.first_name} ${n.author.last_name}` : 'Nieznany' }}</span>
                <span class="text-[10px] text-gray-400">{{ n.created_at }}</span>
                <span v-if="n.updated_at !== n.created_at" class="text-[10px] text-gray-300 italic">(edytowano {{ n.updated_at }})</span>
              </div>
              <div v-if="editingId !== n.id" class="text-sm text-gray-700 whitespace-pre-wrap" v-html="renderMentions(n.body)" />
              <div v-else>
                <textarea v-model="editBody" rows="3" class="w-full text-sm border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 resize-none" />
                <div class="flex items-center justify-end gap-2 mt-2">
                  <button type="button" class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1" @click="cancelEdit">Anuluj</button>
                  <button type="button" class="btn-indigo text-xs px-3 py-1 rounded" @click="saveEdit(n)">Zapisz</button>
                </div>
              </div>
              <div v-if="editingId !== n.id && (n.can_edit || n.can_delete)" class="flex items-center gap-3 mt-2">
                <button v-if="n.can_edit" type="button" class="text-[10px] text-indigo-600 hover:underline" @click="startEdit(n)">Edytuj</button>
                <button v-if="n.can_delete" type="button" class="text-[10px] text-rose-600 hover:underline" @click="removeNote(n)">Usuń</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotesSection',
  props: {
    type: { type: String, required: true }, // 'zapytania' | 'oferta'
    notableId: { type: Number, required: true },
    notes: { type: Array, default: () => [] },
    mentionableUsers: { type: Array, default: () => [] },
  },
  data() {
    return {
      newBody: '',
      processing: false,
      mentionOpen: false,
      mentionQuery: '',
      mentionIndex: 0,
      mentionStart: -1,
      editingId: null,
      editBody: '',
    }
  },
  computed: {
    filteredMentions() {
      const q = this.mentionQuery.toLowerCase().trim()
      const list = q
        ? this.mentionableUsers.filter(u => u.label.toLowerCase().includes(q))
        : this.mentionableUsers
      return list.slice(0, 8)
    },
  },
  methods: {
    initials(name) {
      return (name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase()
    },
    onInput(e) {
      const el = e.target
      const value = el.value
      const cursor = el.selectionStart
      // Znajdz ostatnie @ przed kursorem, ktore nie ma spacji tuz przed
      const upToCursor = value.slice(0, cursor)
      const at = upToCursor.lastIndexOf('@')
      if (at === -1) return this.closeMention()
      // Sprawdz czy przed @ jest spacja / poczatek
      if (at > 0 && !/\s/.test(value[at - 1])) return this.closeMention()
      // Sprawdz czy odstep miedzy @ a kursorem to sensowny fragment (bez enter, max 30 znakow)
      const q = upToCursor.slice(at + 1)
      if (q.length > 30 || q.includes('\n')) return this.closeMention()
      this.mentionQuery = q
      this.mentionStart = at
      this.mentionOpen = true
      this.mentionIndex = 0
    },
    closeMention() {
      this.mentionOpen = false
      this.mentionQuery = ''
      this.mentionStart = -1
    },
    handleKey(dir) {
      if (!this.mentionOpen) return
      const n = this.filteredMentions.length
      if (n === 0) return
      if (dir === 'down') this.mentionIndex = (this.mentionIndex + 1) % n
      else this.mentionIndex = (this.mentionIndex - 1 + n) % n
    },
    handleEnter(e) {
      if (this.mentionOpen && this.filteredMentions.length) {
        e.preventDefault()
        this.pickMention(this.filteredMentions[this.mentionIndex])
      }
    },
    pickMention(u) {
      if (this.mentionStart === -1) return
      const before = this.newBody.slice(0, this.mentionStart)
      const rest = this.newBody.slice(this.mentionStart + 1 + this.mentionQuery.length)
      const canonical = `@[${u.label}](user:${u.id}) `
      this.newBody = before + canonical + rest
      this.closeMention()
      this.$nextTick(() => {
        const el = this.$refs.textarea
        if (el) {
          const pos = (before + canonical).length
          el.focus()
          el.setSelectionRange(pos, pos)
        }
      })
    },
    renderMentions(body) {
      // Zamien @[Imie Nazwisko](user:X) na wyroznione spany. Escape reszty.
      const esc = (s) => s.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]))
      return esc(body).replace(/@\[([^\]]+)\]\(user:(\d+)\)/g, '<span class="bg-indigo-50 text-indigo-700 font-semibold px-1 rounded">@$1</span>')
    },
    save() {
      if (!this.newBody.trim()) return
      this.processing = true
      this.$inertia.post('/notes', {
        type: this.type,
        notable_id: this.notableId,
        body: this.newBody,
      }, {
        preserveScroll: true,
        onSuccess: () => { this.newBody = '' },
        onFinish: () => { this.processing = false },
      })
    },
    startEdit(n) {
      this.editingId = n.id
      this.editBody = n.body
    },
    cancelEdit() {
      this.editingId = null
      this.editBody = ''
    },
    saveEdit(n) {
      if (!this.editBody.trim()) return
      this.$inertia.put(`/notes/${n.id}`, { body: this.editBody }, {
        preserveScroll: true,
        onSuccess: () => { this.editingId = null; this.editBody = '' },
      })
    },
    removeNote(n) {
      if (!confirm('Na pewno usunąć tę notatkę?')) return
      this.$inertia.delete(`/notes/${n.id}`, { preserveScroll: true })
    },
  },
}
</script>

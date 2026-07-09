<template>
  <div v-if="open" class="fixed inset-0 flex items-start justify-center p-4 md:pt-24 bg-black bg-opacity-50" style="z-index:100001" @click="close">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden" @click.stop>
      <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
        </svg>
        <input
          ref="input"
          v-model="query"
          type="text"
          placeholder="Szukaj klienta, zapytania, oferty, kontaktu..."
          class="flex-1 border-0 focus:ring-0 focus:outline-none text-sm text-gray-800 placeholder-gray-400 bg-transparent"
          @keydown.esc="close"
          @keydown.down.prevent="moveSelection(1)"
          @keydown.up.prevent="moveSelection(-1)"
          @keydown.enter.prevent="openSelected"
        />
        <span v-if="loading" class="text-xs text-gray-400">Szukam...</span>
        <kbd class="hidden md:inline-block text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded border border-gray-200">Esc</kbd>
      </div>

      <div class="max-h-[60vh] overflow-y-auto">
        <div v-if="query.length < 2" class="p-8 text-center text-sm text-gray-400">
          Wpisz przynajmniej 2 znaki. Użyj <kbd class="text-[10px] px-1 py-0.5 bg-gray-100 rounded border border-gray-200 mx-1">↑</kbd><kbd class="text-[10px] px-1 py-0.5 bg-gray-100 rounded border border-gray-200 mr-1">↓</kbd> do nawigacji, <kbd class="text-[10px] px-1 py-0.5 bg-gray-100 rounded border border-gray-200">Enter</kbd> aby wejść.
        </div>
        <div v-else-if="!loading && groups.length === 0" class="p-8 text-center text-sm text-gray-400">
          Brak wyników dla "{{ query }}".
        </div>
        <div v-for="(group, gi) in groups" :key="group.label" class="border-b border-gray-50 last:border-0">
          <div class="px-4 py-1.5 bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500 flex items-center gap-2">
            <span>{{ group.icon }}</span>
            <span>{{ group.label }}</span>
            <span class="text-gray-400 font-normal">({{ group.items.length }})</span>
          </div>
          <button
            v-for="(item, i) in group.items"
            :key="`${gi}-${i}`"
            type="button"
            class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition-colors flex items-center gap-3 border-b border-gray-50 last:border-0"
            :class="{ 'bg-indigo-50': isSelected(gi, i) }"
            @click="go(item)"
            @mouseenter="selected = { g: gi, i }"
          >
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-gray-800 truncate flex items-center gap-2">
                <span class="truncate">{{ item.title }}</span>
                <span v-if="item.archived" class="flex-shrink-0 text-[9px] font-bold uppercase text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">Archiwum</span>
              </div>
              <div v-if="item.subtitle" class="text-xs text-gray-500 truncate">{{ item.subtitle }}</div>
            </div>
            <svg class="flex-shrink-0 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>

      <div class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-[10px] text-gray-400 flex items-center justify-between">
        <span>Skrót: <kbd class="text-[10px] px-1 py-0.5 bg-white rounded border border-gray-200">Ctrl</kbd> + <kbd class="text-[10px] px-1 py-0.5 bg-white rounded border border-gray-200">K</kbd></span>
        <span>MKL CRM · szybkie wyszukiwanie</span>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

export default {
  name: 'GlobalSearch',
  data() {
    return {
      open: false,
      query: '',
      loading: false,
      groups: [],
      selected: { g: 0, i: 0 },
      debounceTimer: null,
      abortController: null,
    }
  },
  computed: {
    flatItems() {
      const list = []
      this.groups.forEach((g, gi) => g.items.forEach((it, i) => list.push({ item: it, g: gi, i })))
      return list
    },
  },
  watch: {
    query() {
      this.selected = { g: 0, i: 0 }
      if (this.debounceTimer) clearTimeout(this.debounceTimer)
      if (this.query.length < 2) {
        this.groups = []
        this.loading = false
        return
      }
      this.debounceTimer = setTimeout(() => this.fetchResults(), 200)
    },
  },
  mounted() {
    document.addEventListener('keydown', this.onGlobalKeydown)
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.onGlobalKeydown)
  },
  methods: {
    onGlobalKeydown(e) {
      // Ctrl+K / Cmd+K otwiera. Nie kolejaduje z formularzami (inputy nie sluchaja tego skrotu).
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault()
        this.openModal()
      }
    },
    openModal() {
      this.open = true
      this.$nextTick(() => this.$refs.input && this.$refs.input.focus())
    },
    close() {
      this.open = false
      this.query = ''
      this.groups = []
      if (this.abortController) this.abortController.abort()
    },
    async fetchResults() {
      if (this.abortController) this.abortController.abort()
      this.abortController = new AbortController()
      this.loading = true
      try {
        const { data } = await axios.get('/search', {
          params: { q: this.query },
          signal: this.abortController.signal,
        })
        this.groups = data.groups || []
      } catch (e) {
        if (e.name !== 'CanceledError' && e.name !== 'AbortError') {
          this.groups = []
        }
      } finally {
        this.loading = false
      }
    },
    isSelected(g, i) {
      return this.selected.g === g && this.selected.i === i
    },
    moveSelection(delta) {
      const flat = this.flatItems
      if (flat.length === 0) return
      const currentIdx = flat.findIndex(x => x.g === this.selected.g && x.i === this.selected.i)
      const nextIdx = Math.max(0, Math.min(flat.length - 1, (currentIdx === -1 ? 0 : currentIdx) + delta))
      this.selected = { g: flat[nextIdx].g, i: flat[nextIdx].i }
    },
    openSelected() {
      const flat = this.flatItems
      if (flat.length === 0) return
      const found = flat.find(x => x.g === this.selected.g && x.i === this.selected.i) || flat[0]
      this.go(found.item)
    },
    go(item) {
      this.close()
      Inertia.visit(item.link)
    },
  },
}
</script>

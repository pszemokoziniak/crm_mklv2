<template>
  <div class="relative">
    <button
      type="button"
      class="relative p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-gray-50 transition-colors focus:outline-none"
      @click="toggleDropdown"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      <span
        v-if="localCount > 0"
        class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full"
      >
        {{ localCount > 9 ? '9+' : localCount }}
      </span>
    </button>

    <div
      v-if="open"
      class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden"
    >
      <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-100">
        <span class="text-sm font-bold text-gray-700">Powiadomienia</span>
        <button
          v-if="notifications.length > 0"
          type="button"
          class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
          @click="markAllRead"
        >
          Oznacz wszystkie
        </button>
      </div>

      <div class="max-h-80 overflow-y-auto">
        <div v-if="loading" class="p-4 text-center text-gray-400 text-sm">
          Wczytywanie...
        </div>
        <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-400 text-sm">
          Brak powiadomień.
        </div>
        <div v-else>
          <button
            v-for="n in notifications"
            :key="n.id"
            type="button"
            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0"
            :class="{ 'bg-indigo-50/50': !n.read_at }"
            @click="goToNotification(n)"
          >
            <div class="text-sm text-gray-800" :class="{ 'font-semibold': !n.read_at }">
              {{ n.data.message }}
            </div>
            <div class="text-xs text-gray-400 mt-1">{{ n.created_at }}</div>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    count: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      open: false,
      loading: false,
      notifications: [],
      localCount: this.count,
      pollInterval: null,
    }
  },
  watch: {
    count(val) {
      this.localCount = val
    },
  },
  mounted() {
    this.pollInterval = setInterval(() => {
      this.fetchCount()
    }, 60000)

    document.addEventListener('click', this.closeOnOutsideClick)
  },
  beforeUnmount() {
    if (this.pollInterval) {
      clearInterval(this.pollInterval)
    }
    document.removeEventListener('click', this.closeOnOutsideClick)
  },
  methods: {
    closeOnOutsideClick(e) {
      if (this.open && !this.$el.contains(e.target)) {
        this.open = false
      }
    },
    async fetchCount() {
      try {
        const { data } = await axios.get('/notifications/count')
        this.localCount = data.count
      } catch (e) {
        // ignore
      }
    },
    async toggleDropdown() {
      this.open = !this.open
      if (this.open) {
        this.loading = true
        try {
          const { data } = await axios.get('/notifications')
          this.notifications = data
        } catch (e) {
          // ignore
        }
        this.loading = false
      }
    },
    async goToNotification(n) {
      if (!n.read_at) {
        await axios.put(`/notifications/${n.id}/read`)
        n.read_at = new Date().toISOString()
        this.localCount = Math.max(0, this.localCount - 1)
      }
      this.open = false
      if (n.data.url) {
        Inertia.visit(n.data.url)
      }
    },
    async markAllRead() {
      await axios.put('/notifications/read-all')
      this.notifications.forEach(n => { n.read_at = new Date().toISOString() })
      this.localCount = 0
    },
  },
}
</script>

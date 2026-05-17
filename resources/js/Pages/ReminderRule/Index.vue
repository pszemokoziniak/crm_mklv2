<template>
  <div>
    <Head title="Powiadomienia i przypomnienia" />
    <h1 class="mb-8 text-3xl font-bold">Powiadomienia i przypomnienia</h1>
    <div class="flex items-center justify-between mb-6">
      <Link class="btn-indigo" href="/reminder-rules/create">
        <span>Dodaj regułę</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Nazwa</th>
          <th class="pb-4 pt-6 px-6">Zdarzenie</th>
          <th class="pb-4 pt-6 px-6">Dni przed</th>
          <th class="pb-4 pt-6 px-6">Odbiorcy</th>
          <th class="pb-4 pt-6 px-6">Kanały</th>
          <th class="pb-4 pt-6 px-6">Status</th>
          <th class="pb-4 pt-6 px-6" />
        </tr>
        <tr v-for="rule in rules" :key="rule.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/reminder-rules/${rule.id}/edit`">
              {{ rule.name }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/reminder-rules/${rule.id}/edit`">
              {{ rule.event_label }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/reminder-rules/${rule.id}/edit`">
              {{ rule.days_before }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/reminder-rules/${rule.id}/edit`">
              {{ formatRecipients(rule.recipients) }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/reminder-rules/${rule.id}/edit`">
              <span v-for="ch in rule.channels" :key="ch" class="inline-block mr-1 px-2 py-1 rounded text-xs" :class="channelClass(ch)">{{ channelShortLabel(ch) }}</span>
            </Link>
          </td>
          <td class="border-t">
            <button
              class="px-3 py-1 rounded text-sm"
              :class="rule.active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700'"
              @click="toggle(rule)"
            >
              {{ rule.active ? 'Aktywna' : 'Wyłączona' }}
            </button>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4 py-4" :href="`/reminder-rules/${rule.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="rules.length === 0">
          <td class="px-6 py-4 border-t" colspan="7">Brak reguł — dodaj pierwszą, aby zacząć wysyłać powiadomienia.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'

export default {
  components: { Head, Link, Icon },
  layout: Layout,
  props: {
    rules: Array,
    events: Object,
    channels: Object,
  },
  methods: {
    formatRecipients(list) {
      if (!list || list.length === 0) return '—'
      return list.map(r => {
        if (r === 'opiekun') return 'Opiekun klienta'
        if (r === 'opracowuje') return 'Opracowujący'
        if (r === 'osoba_odpowiedzialna') return 'Osoba odpowiedzialna'
        if (r.startsWith('user:')) return `User #${r.slice(5)}`
        if (r.startsWith('role:')) return `Rola: ${r.slice(5)}`
        return r
      }).join(', ')
    },
    channelShortLabel(ch) {
      if (ch === 'mail') return 'Email'
      if (ch === 'webpush') return 'Push'
      if (ch === 'database') return 'Dzwonek'
      return ch
    },
    channelClass(ch) {
      if (ch === 'mail') return 'bg-blue-100 text-blue-800'
      if (ch === 'webpush') return 'bg-purple-100 text-purple-800'
      if (ch === 'database') return 'bg-yellow-100 text-yellow-800'
      return 'bg-gray-100 text-gray-800'
    },
    toggle(rule) {
      Inertia.put(`/reminder-rules/${rule.id}/toggle`)
    },
  },
}
</script>

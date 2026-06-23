<template>
  <div>
    <Head title="Statystyki" />
    <h1 class="mb-8 text-3xl font-bold">Statystyki</h1>
    <div class="flex flex-wrap -mb-8 -mr-6 pb-4">
      <text-input v-model="form.start" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Od" />
      <text-input v-model="form.end" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Do" />
    </div>

    <div class="bg-white rounded-md shadow overflow-x-auto p-4 space-y-6">
      <h2 class="text-3xl font-extrabold text-white text-center bg-indigo-600 py-4 rounded-lg shadow-sm">Klienci</h2>
      <p class="text-2xl font-medium text-gray-700 text-center">Ilość klientów: <span class="text-indigo-600 font-bold">{{ clientNumber }}</span></p>
      <users-add-clients :client-number="clientNumber" :client-number-by-user="clientNumberByUser" />
      <active-client :client-active="clientActive" />
      <increase-client :increase-clients="increaseClients" />
      <client-branza :client-branza="clientBranza" />
      <clients-zapytania-sum-amount :client-zapytania-sum-amount="clientZapytaniaSumAmount" />
      <clients-oferty-sum-amount :client-oferta-sum-amount="clientOfertaSumAmount" />

      <h2 class="text-3xl font-extrabold text-white text-center bg-indigo-600 py-4 rounded-lg shadow-sm mt-10">Zapytania</h2>
      <div class="p-3">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
          <div class="bg-gray-50 rounded-lg p-3 border">
            <p class="text-xs text-gray-500 mb-1">Wszystkie zapytania</p>
            <p class="text-xl font-bold text-gray-900">{{ quantityZapytania.count }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatPLN(quantityZapytania.sum) }}</p>
          </div>
          <div v-for="s in quantityZapytania.breakdown" :key="s.name" class="bg-gray-50 rounded-lg p-3 border">
            <p class="text-xs text-gray-500 mb-1">{{ s.name }}</p>
            <p class="text-xl font-bold" :class="zapytaniaColor(s.name)">{{ s.qty }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatPLN(s.total) }}</p>
          </div>
        </div>
      </div>
      <zapytania-oferta-sum-amount :zapytania-oferty-sum-amount="zapytaniaOfertySumAmount" />
      <zapytania-branze :zapytania-branze="zapytaniaBranze" />
      <zapytania-zakres :zapytania-zakres="zapytaniaZakres" />
      <zapytania-users :zapytania-users="zapytaniaUsers" />

      <h2 class="text-3xl font-extrabold text-white text-center bg-indigo-600 py-4 rounded-lg shadow-sm mt-10">Oferty</h2>
      <div class="p-3">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
          <div class="bg-gray-50 rounded-lg p-3 border">
            <p class="text-xs text-gray-500 mb-1">Wszystkie oferty</p>
            <p class="text-xl font-bold text-gray-900">{{ quantityOferta.count }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatPLN(quantityOferta.sum) }}</p>
          </div>
          <div v-for="s in quantityOferta.byStatus" :key="s.name" class="bg-gray-50 rounded-lg p-3 border">
            <p class="text-xs text-gray-500 mb-1">{{ s.name }}</p>
            <p class="text-xl font-bold" :class="statusColor(s.name)">{{ s.qty }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ formatPLN(s.total) }}</p>
          </div>
        </div>
      </div>
      <oferta-status :oferta-status="ofertaStatus" />
      <oferta-status-win :oferta-status-win="ofertaStatusWin" />
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import UsersAddClients from '@/Pages/Stats/UsersAddClients.vue'
import ActiveClient from '@/Pages/Stats/ActiveClient.vue'
import IncreaseClient from '@/Pages/Stats/IncreaseClient.vue'
import ZapytaniaOfertaSumAmount from '@/Pages/Stats/ZapytaniaOfertySumAmount.vue'
import ClientBranza from '@/Pages/Stats/ClientBranza.vue'
import ClientsZapytaniaSumAmount from '@/Pages/Stats/ClientsZapytaniaSumAmount.vue'
import ClientsOfertySumAmount from '@/Pages/Stats/ClientsOfertaSumAmount.vue'
import ZapytaniaBranze from '@/Pages/Stats/ZapytaniaBranze.vue'
import ZapytaniaZakres from '@/Pages/Stats/ZapytaniaZakres.vue'
import ZapytaniaUsers from '@/Pages/Stats/ZapytaniaUsers.vue'
import OfertaStatus from '@/Pages/Stats/OfertaStatus.vue'
import OfertaStatusWin from '@/Pages/Stats/OfertaStatusWin.vue'
import TextInput from '@/Shared/TextInput.vue'

export default {
  components: {
    TextInput,
    UsersAddClients,
    ActiveClient,
    Head,
    IncreaseClient,
    ClientBranza,
    ClientsZapytaniaSumAmount,
    ClientsOfertySumAmount,
    ZapytaniaOfertaSumAmount,
    ZapytaniaBranze,
    ZapytaniaZakres,
    ZapytaniaUsers,
    OfertaStatus,
    OfertaStatusWin,
  },
  layout: Layout,
  props: {
    clientNumber: Number,
    clientNumberByUser: Object,
    clientActive: Array,
    increaseClients: Array,
    clientBranza: Array,
    clientZapytaniaSumAmount: Array,
    clientOfertaSumAmount: Array,
    zapytaniaOfertySumAmount: Array,
    zapytaniaBranze: Array,
    zapytaniaZakres: Array,
    zapytaniaUsers: Array,
    quantityOferta: Object,
    ofertaStatus: Array,
    ofertaStatusWin: Array,
    start: Date,
    end: Date,
    quantityZapytania: Object,
    // filters: Object,
    // zapytanias: Object,
  },
  data() {
    return {
      form: {
        start: this.start,
        end: this.end,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/stats', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    formatPLN(value) {
      if (!value) return '0 PLN'
      return new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN', maximumFractionDigits: 0 }).format(value)
    },
    statusColor(name) {
      const n = (name || '').toLowerCase()
      if (n.includes('wygran')) return 'text-green-600'
      if (n.includes('przegran')) return 'text-red-600'
      if (n.includes('toczy')) return 'text-indigo-600'
      return 'text-gray-900'
    },
    zapytaniaColor(name) {
      if (name === 'Z ofertą') return 'text-green-600'
      if (name === 'Bez oferty') return 'text-orange-500'
      return 'text-gray-900'
    },
  },
}
</script>


<template>
  <div>
    <h1 class="text-xl font-bold text-center text-indigo-700 py-3 mt-8 border-b border-indigo-100">Lejek konwersji: Zapytanie → Oferta → Wygrana</h1>

    <!-- Kluczowe wskazniki -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4 mb-6">
      <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 mb-1">Konwersja end-to-end</p>
        <p class="text-2xl font-bold text-indigo-700">{{ funnel.pct_zapytania_to_wygrana }}%</p>
        <p class="text-[10px] text-gray-500 mt-1">zapytań → wygranych</p>
      </div>
      <div class="bg-green-50 rounded-lg p-4 border border-green-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-green-600 mb-1">Skuteczność ofert</p>
        <p class="text-2xl font-bold text-green-700">{{ funnel.pct_oferty_to_wygrana }}%</p>
        <p class="text-[10px] text-gray-500 mt-1">wygrane z ofert</p>
      </div>
      <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 mb-1">Wartość wygranych</p>
        <p class="text-2xl font-bold text-emerald-700">{{ formatPLN(funnel.suma_wygranych_pln) }}</p>
        <p class="text-[10px] text-gray-500 mt-1">suma kwotaPLN</p>
      </div>
      <div class="bg-rose-50 rounded-lg p-4 border border-rose-100">
        <p class="text-[10px] font-bold uppercase tracking-wider text-rose-600 mb-1">Przegrane</p>
        <p class="text-2xl font-bold text-rose-700">{{ funnel.pct_oferty_to_przegrana }}%</p>
        <p class="text-[10px] text-gray-500 mt-1">przegrane z ofert</p>
      </div>
    </div>

    <!-- Lejek wizualny (jednolita jednostka = zapytania) -->
    <div class="p-4">
      <p class="text-[10px] text-center text-gray-400 mb-4 italic">Lejek pokazuje ile <b>zapytań</b> przechodzi przez kolejne etapy. Jedno zapytanie może mieć wiele ofert (wznowienia, rewizje) — dlatego wolumen ofert jest osobno.</p>
      <div class="space-y-3 max-w-3xl mx-auto">
        <!-- Stage 1: Zapytania -->
        <div class="relative">
          <div class="flex items-center gap-3">
            <div class="w-44 flex-shrink-0 text-right">
              <div class="text-xs font-bold uppercase text-gray-500">Zapytania</div>
              <div class="text-[10px] text-gray-400">utworzone w okresie</div>
            </div>
            <div class="flex-1 relative h-16 rounded-lg overflow-hidden bg-gray-100 flex items-center">
              <div class="h-full flex items-center justify-center text-white font-bold text-lg shadow-inner"
                :style="{ width: barWidth(funnel.zapytania_total, funnel.zapytania_total) + '%', background: 'linear-gradient(90deg, #4f46e5, #6366f1)' }">
                {{ funnel.zapytania_total }}
              </div>
            </div>
            <div class="w-20 text-right">
              <div class="text-lg font-bold text-indigo-600">100%</div>
              <div class="text-[9px] text-gray-400">baza</div>
            </div>
          </div>
        </div>

        <div class="flex justify-center text-gray-300">↓</div>

        <!-- Stage 2: Zapytania z oferta -->
        <div class="relative">
          <div class="flex items-center gap-3">
            <div class="w-44 flex-shrink-0 text-right">
              <div class="text-xs font-bold uppercase text-gray-500">Zapytania z ofertą</div>
              <div class="text-[10px] text-gray-400">≥1 oferta wystawiona</div>
            </div>
            <div class="flex-1 relative h-14 rounded-lg overflow-hidden bg-gray-100 flex items-center">
              <div class="h-full flex items-center justify-center text-white font-bold shadow-inner"
                :style="{ width: barWidth(funnel.zapytania_with_oferta, funnel.zapytania_total) + '%', background: 'linear-gradient(90deg, #0891b2, #06b6d4)' }">
                {{ funnel.zapytania_with_oferta }}
              </div>
            </div>
            <div class="w-20 text-right">
              <div class="text-lg font-bold text-cyan-600">{{ funnel.pct_zapytania_to_oferta }}%</div>
              <div class="text-[9px] text-gray-400">z zapytań</div>
            </div>
          </div>
        </div>

        <div class="flex justify-center text-gray-300">↓</div>

        <!-- Stage 3: Zapytania z wygrana -->
        <div class="relative">
          <div class="flex items-center gap-3">
            <div class="w-44 flex-shrink-0 text-right">
              <div class="text-xs font-bold uppercase text-gray-500">Zapytania z wygraną</div>
              <div class="text-[10px] text-gray-400">≥1 wygrana oferta</div>
            </div>
            <div class="flex-1 relative h-14 rounded-lg overflow-hidden bg-gray-100 flex items-center">
              <div class="h-full flex items-center justify-center text-white font-bold shadow-inner"
                :style="{ width: barWidth(funnel.zapytania_with_wygrana, funnel.zapytania_total) + '%', background: 'linear-gradient(90deg, #059669, #10b981)' }">
                {{ funnel.zapytania_with_wygrana }}
              </div>
            </div>
            <div class="w-20 text-right">
              <div class="text-lg font-bold text-green-600">{{ funnel.pct_zapytania_to_wygrana }}%</div>
              <div class="text-[9px] text-gray-400">z zapytań</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Wolumen ofert - osobno -->
      <div class="max-w-3xl mx-auto mt-8 pt-6 border-t border-gray-100">
        <h3 class="text-xs font-bold uppercase text-gray-500 mb-3 text-center">Wolumen ofert (nie 1:1 z zapytaniami — wznowienia, rewizje)</h3>
        <div class="grid grid-cols-3 gap-3 text-center">
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="text-[10px] uppercase font-bold text-gray-500">Oferty złożone</div>
            <div class="text-xl font-bold text-gray-800">{{ funnel.oferty_total }}</div>
            <div class="text-[10px] text-gray-500">śr. {{ avgOfertPerZapytanie }}/zapytanie</div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="text-[10px] uppercase font-bold text-gray-500">Oferty wygrane</div>
            <div class="text-xl font-bold text-green-700">{{ funnel.oferty_wygrane }}</div>
            <div class="text-[10px] text-gray-500">{{ funnel.pct_oferty_to_wygrana }}% ofert</div>
          </div>
          <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="text-[10px] uppercase font-bold text-gray-500">Oferty przegrane</div>
            <div class="text-xl font-bold text-rose-700">{{ funnel.oferty_przegrane }}</div>
            <div class="text-[10px] text-gray-500">{{ funnel.pct_oferty_to_przegrana }}% ofert</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'ConversionFunnel',
  props: {
    funnel: {
      type: Object,
      required: true,
    },
  },
  computed: {
    pctOfertyToToczy() {
      if (!this.funnel.oferty_total) return 0
      return Math.round((this.funnel.oferty_toczy / this.funnel.oferty_total * 100) * 10) / 10
    },
    avgOfertPerZapytanie() {
      if (!this.funnel.zapytania_with_oferta) return '0'
      return (this.funnel.oferty_total / this.funnel.zapytania_with_oferta).toFixed(2)
    },
  },
  methods: {
    barWidth(value, base) {
      if (!base || base === 0) return 0
      const pct = value / base * 100
      // Minimalna szerokosc zeby liczba byla widoczna
      return Math.max(pct, value > 0 ? 8 : 0)
    },
    formatPLN(value) {
      if (!value) return '0 PLN'
      return new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN', maximumFractionDigits: 0 }).format(value)
    },
  },
}
</script>

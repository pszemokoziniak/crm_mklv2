<template>
  <div v-if="show" class="fixed inset-0 flex items-start justify-center p-4 md:pt-20 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" style="z-index:100001" @click="skip">
    <div class="utrata-modal bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.stop>
      <!-- Header -->
      <div class="flex items-start gap-3 px-6 py-5 border-b border-gray-100">
        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
          <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-bold text-gray-900 leading-tight">Dlaczego oferta została utracona?</h3>
          <p class="text-xs text-gray-500 mt-0.5">Kilka pytań, które pomogą analizować, dlaczego przegrywamy oferty.</p>
        </div>
      </div>

      <!-- Body -->
      <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
        <select-input v-model="form.powod_utraty_id" :error="form.errors.powod_utraty_id" class="w-full" label="Główny powód">
          <option :value="null">— wybierz —</option>
          <option v-for="p in powodyUtraty" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select-input>

        <select-input v-model="form.powod_utraty_dodatkowy_id" :error="form.errors.powod_utraty_dodatkowy_id" class="w-full" label="Powód dodatkowy (opcjonalnie)">
          <option :value="null">— brak —</option>
          <option v-for="p in powodyUtraty" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select-input>

        <select-input v-model="form.etap_utraty" :error="form.errors.etap_utraty" class="w-full" label="Na jakim etapie straciliśmy?">
          <option :value="null">— wybierz —</option>
          <option value="po_ofercie">Zaraz po wysłaniu oferty</option>
          <option value="po_negocjacjach">Po negocjacjach</option>
          <option value="po_wizji_lokalnej">Po wizji lokalnej</option>
          <option value="inny">Inny</option>
        </select-input>

        <text-input v-model="form.konkurent" :error="form.errors.konkurent" class="w-full" label="Kto wygrał (konkurent)" placeholder="np. nazwa firmy" />

        <div class="grid grid-cols-2 gap-4">
          <number-input v-model="form.cena_konkurenta" :error="form.errors.cena_konkurenta" label="Cena konkurencji" placeholder="0" />
          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" label="Waluta">
            <option :value="null">—</option>
            <option v-for="w in waluta" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select-input>
        </div>

        <label class="flex items-center gap-2.5 py-1 text-sm font-medium text-gray-700 cursor-pointer select-none">
          <input v-model="form.szansa_na_renegocjacje" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
          Była szansa na renegocjację
        </label>

        <text-area-input v-model="form.notatka" :error="form.errors.notatka" class="w-full" rows="3" label="Notatka własna" placeholder="Co poszło nie tak? Uwagi handlowca..." />
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-100">
        <button type="button" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors" @click="skip">
          Pomiń na razie
        </button>
        <loading-button :loading="form.processing" class="btn-indigo shadow-sm px-6" type="button" @click="save">
          Zapisz
        </loading-button>
      </div>
    </div>
  </div>
</template>

<script>
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import NumberInput from '@/Shared/NumberInput.vue'
import TextAreaInput from '@/Shared/TextareaInput.vue'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  name: 'OfertaUtrataModal',
  components: {
    SelectInput,
    TextInput,
    NumberInput,
    TextAreaInput,
    LoadingButton,
  },
  props: {
    show: { type: Boolean, default: false },
    ofertaId: { type: Number, default: null },
    powodyUtraty: { type: Array, default: () => [] },
    waluta: { type: Array, default: () => [] },
    initialData: { type: Object, default: null },
  },
  data() {
    return {
      form: this.$inertia.form(this.blankForm()),
    }
  },
  watch: {
    show(value) {
      if (value) {
        this.form = this.$inertia.form(this.blankForm())
      }
    },
  },
  methods: {
    blankForm() {
      const d = this.initialData || {}
      return {
        powod_utraty_id: d.powod_utraty_id || null,
        powod_utraty_dodatkowy_id: d.powod_utraty_dodatkowy_id || null,
        etap_utraty: d.etap_utraty || null,
        konkurent: d.konkurent || null,
        cena_konkurenta: d.cena_konkurenta || null,
        waluta_id: d.waluta_id || null,
        szansa_na_renegocjacje: !!d.szansa_na_renegocjacje,
        notatka: d.notatka || null,
      }
    },
    save() {
      this.form.post(`/oferta/${this.ofertaId}/utrata`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => this.$emit('saved'),
      })
    },
    skip() {
      this.$emit('close')
    },
  },
}
</script>

<style scoped>
/* Kompaktowe etykiety w modalu (globalny .form-label ma 16px, tu chcemy ciaśniej) */
.utrata-modal :deep(.form-label) {
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 500;
  color: #4b5563;
}

.utrata-modal :deep(.form-input),
.utrata-modal :deep(.form-select),
.utrata-modal :deep(.form-textarea) {
  font-size: 0.875rem;
  line-height: 1.25rem;
  border-color: #d1d5db;
  border-radius: 0.5rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>

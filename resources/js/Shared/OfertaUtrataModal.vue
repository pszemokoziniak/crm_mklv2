<template>
  <div v-if="show" class="fixed inset-0 flex items-start justify-center p-4 md:pt-16 bg-black bg-opacity-50 overflow-y-auto" style="z-index:100001" @click="skip">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl overflow-hidden" @click.stop>
      <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-lg font-bold text-gray-800">Dlaczego oferta została utracona?</h3>
        <p class="text-xs text-gray-500 mt-1">Kilka pytań, które pomogą nam analizować, dlaczego przegrywamy oferty.</p>
      </div>

      <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
        <select-input v-model="form.powod_utraty_id" :error="form.errors.powod_utraty_id" class="w-full" label="Główny powód">
          <option :value="null" />
          <option v-for="p in powodyUtraty" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select-input>

        <select-input v-model="form.powod_utraty_dodatkowy_id" :error="form.errors.powod_utraty_dodatkowy_id" class="w-full" label="Powód dodatkowy (opcjonalnie)">
          <option :value="null" />
          <option v-for="p in powodyUtraty" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select-input>

        <select-input v-model="form.etap_utraty" :error="form.errors.etap_utraty" class="w-full" label="Na jakim etapie straciliśmy?">
          <option :value="null" />
          <option value="po_ofercie">Zaraz po wysłaniu oferty</option>
          <option value="po_negocjacjach">Po negocjacjach</option>
          <option value="po_wizji_lokalnej">Po wizji lokalnej</option>
          <option value="inny">Inny</option>
        </select-input>

        <div class="flex flex-wrap -mb-4 -mr-4">
          <text-input v-model="form.konkurent" :error="form.errors.konkurent" class="pb-4 pr-4 w-full md:w-1/2" label="Kto wygrał (konkurent)" />
          <number-input v-model="form.cena_konkurenta" :error="form.errors.cena_konkurenta" class="pb-4 pr-4 w-full md:w-1/4" label="Cena konkurencji" />
          <select-input v-model="form.waluta_id" :error="form.errors.waluta_id" class="pb-4 pr-4 w-full md:w-1/4" label="Waluta">
            <option :value="null" />
            <option v-for="w in waluta" :key="w.id" :value="w.id">{{ w.name }}</option>
          </select-input>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input v-model="form.szansa_na_renegocjacje" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
          Była szansa na renegocjację
        </label>

        <text-area-input v-model="form.notatka" :error="form.errors.notatka" class="w-full" label="Notatka własna" />
      </div>

      <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-t border-gray-100">
        <button type="button" class="text-sm text-gray-500 hover:text-gray-700" @click="skip">
          Pomiń na razie
        </button>
        <loading-button :loading="form.processing" class="btn-indigo shadow-md px-6" type="button" @click="save">
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

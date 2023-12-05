<template>
  <div>
    <Head title="Kursy" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/kursy">Kursy</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa waluty">
            <option :value="null" />
            <option v-for="item in waluta" :key="item.id" :value="item.waluta">{{ item.waluta }}</option>
          </select-input>
          <number-input v-model="form.kurs" :error="form.errors.kurs" :step="0.0001" class="pb-8 pr-6 w-full lg:w-1/2" label="Kurs" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Dodaj</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextArea from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import NumberInput from "@/Shared/NumberInput.vue";

export default {
  components: {
    NumberInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextArea,
  },
  layout: Layout,
  props: {
    waluta: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        kurs: '',
      }),
    }
  },
  computed: {
    kurs: function () {
      return this.kurs + ' ' + this.kurs
    }
  },
  methods: {
    store() {
      this.form.kurs
      this.form.post('/kursy')
    },
    formatNumber00 (num) {
      return new Intl.NumberFormat('pl-PL',{
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(num)
    },
  },
}
</script>

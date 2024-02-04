<template>
  <div>
    <Head title="Wznowienie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="`/zapytania/${zapytania.id}/edit`">{{zapytania.nazwa_projektu}} {{ zapytania.id_zapyt }}</Link>
    </h1>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="storeWznowienie">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opisz powód wznowienia" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zapisz</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import NumberInput from '@/Shared/NumberInput.vue'
import TextAreaInput from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextArea from "@/Shared/TextareaInput.vue";
import Icon from "@/Shared/Icon.vue";

export default {
  components: {
    Icon,
    TextArea,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    TextAreaInput,
    NumberInput,
  },
  layout: Layout,
  props: {
    zapytania: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        zapytania_id: this.zapytania.id,
        description: '',
        user_id: this.$page.props.auth.user.id,
      }),
    }
  },
  methods: {
    storeWznowienie() {
      if (confirm('Czy chcesz wznowić te zapytanie?')) {

        this.form.post(`/zapytania/${this.zapytania.id}/storeWznowienie`)
      }
    },
    disableForm() {
      this.isActive=true
      let elems_input = document.getElementById('form').getElementsByTagName('input');
      for(let i = 0; i < elems_input.length; i++) {
        elems_input[i].disabled = false;
      }
      let elems_select = document.getElementById('form').getElementsByTagName('select');
      for(let i = 0; i < elems_select.length; i++) {
        elems_select[i].disabled = false;
      }
      let elems_text_area = document.getElementById('form').getElementsByTagName('textarea');
      for(let i = 0; i < elems_text_area.length; i++) {
        elems_text_area[i].disabled = false;
      }
    },
  },
}
</script>

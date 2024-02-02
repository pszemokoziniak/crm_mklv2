<template>
  <div>
    <Head title="Popraw zadanie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zadania">Zadania</Link>
      <span class="text-indigo-400 font-medium">/</span> Popraw
    </h1>
    <trashed-message v-if="zadanie.deleted_at" class="mb-6" @restore="restore"> Zadanie zostało zarchiwizowane </trashed-message>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update" :class=" (isActive) ? 'border-2 border-green-500' : ''">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <select-input v-model="form.responsible_person_id" :error="form.errors.responsible_person_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Osoba odpowiedzialna">
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
          </select-input>
          <text-input v-model="form.deadline" :error="form.errors.deadline" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wykonania" />
          <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full lg:w-1/1" label="Temat" />
          <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!zadanie.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Popraw</loading-button>
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
    zadanie: Object,
    users: Object,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      form: this.$inertia.form({
        id: this.zadanie.id,
        responsible_person_id: this.zadanie.responsible_person_id,
        subject: this.zadanie.subject,
        description: this.zadanie.description,
        deadline: this.zadanie.deadline,
        user_id: this.zadanie.user_id
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/zadania/${this.zadanie.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz archiwizować te zadanie?')) {
        this.$inertia.delete(`/zadania/${this.zadanie.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić zadanie?')) {
        this.$inertia.put(`/zadania/${this.zadanie.id}/restore`)
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

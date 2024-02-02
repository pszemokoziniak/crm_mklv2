<template>
  <div>
    <Head :title="`${form.first_name} ${form.last_name}`" />
    <div class="flex justify-start mb-8 max-w-3xl">
      <h1 class="text-3xl font-bold">
        <Link class="text-indigo-400 hover:text-indigo-600" href="/users">Użytkownik</Link>
        <span class="text-indigo-400 font-medium">/</span>
        {{ form.first_name }} {{ form.last_name }}
      </h1>
      <img v-if="user.photo" class="block ml-4 w-8 h-8 rounded-full" :src="user.photo" />
    </div>
    <trashed-message v-if="user.deleted_at" class="mb-6" @restore="restore"> Ten użytkownik został zarchiwizowany. </trashed-message>
    <div id="form" class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update" :class=" (isActive) ? 'border-2 border-green-500' : ''">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.first_name" :error="form.errors.first_name" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwisko" />
          <text-input v-model="form.last_name" :error="form.errors.last_name" :disabled="disable" class="pb-8  w-full lg:w-1/2" label="Imię" />
          <text-input v-model="form.email" :error="form.errors.email" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
          <text-input v-model="form.password" :error="form.errors.password" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Hasło" />
          <select-input v-model="form.owner" :error="form.errors.owner" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" label="Uprawnienia">
            <option v-for="item in uprawnienia" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <file-input v-model="form.photo" :error="form.errors.photo" :disabled="disable" class="pb-8 pr-6 w-full lg:w-1/2" type="file" accept="image/*" label="Zdjęcie" />
        </div>
        <hr>
        <div class="grid gap-1 grid-cols-3 p-5">
          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 cursor-default" @click="disableForm">
            <div class="group flex items-center py-3 cursor-pointer" @click="disableForm">
              <icon name="edit" class="mr-2 w-4 h-4 inline"/>
              <div class="">Edytuj dane</div>
            </div>
          </div>
          <div v-if="user.active===1" class="px-8 py-4 bg-gray-50 border-t border-gray-100" @click="blockActive">
            <Link class="group flex items-center py-3" href="#">
              <icon name="zablokuj" class="mr-2 w-4 h-4 inline"/>
              <div class="">Zablokuj</div>
            </Link>
          </div>
          <div v-if="user.active===0" class="px-8 py-4 bg-gray-50 border-t border-gray-100" @click="unblockActive">
            <Link class="group flex items-center py-3" href="#">
              <icon name="zablokuj" class="mr-2 w-4 h-4 inline"/>
              <div class="">Odblokuj konto</div>
            </Link>
          </div>
        </div>
<!--        <div class="grid gap-1 grid-cols-3 p-5">-->
<!--          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">-->
<!--            <icon name="zablokuj" class="mr-2 w-4 h-4 inline"/>-->
<!--            <button v-if="user.active===1" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="blockActive">Zablokuj konto </button>-->
<!--            <button v-if="user.active===0" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="unblockActive">Odblokuj konto</button>-->
<!--          </div>-->
<!--          <div class="px-8 py-4 bg-gray-50 border-t border-gray-100">-->
<!--            <icon name="edit" class="mr-2 w-4 h-4 inline"/>-->
<!--            <button v-if="!user.deleted_at" class="text-indigo-600 hover:underline ml-auto" tabindex="-1" type="button" @click="disableForm">Edytuj dane</button>-->
<!--          </div>-->
<!--        </div>-->
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!user.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
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
import FileInput from '@/Shared/FileInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import Icon from "@/Shared/Icon.vue";

export default {
  components: {
    FileInput,
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    Icon,
  },
  layout: Layout,
  props: {
    user: Object,
    uprawnienia: Object,
  },
  remember: 'form',
  data() {
    return {
      disable: true,
      isActive: false,
      form: this.$inertia.form({
        _method: 'put',
        first_name: this.user.first_name,
        last_name: this.user.last_name,
        email: this.user.email,
        password: '',
        owner: this.user.owner,
        photo: null,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/users/${this.user.id}`, {
        onSuccess: () => this.form.reset('password', 'photo'),
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this user?')) {
        this.$inertia.delete(`/users/${this.user.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this user?')) {
        this.$inertia.put(`/users/${this.user.id}/restore`)
      }
    },
    blockActive() {
      this.$inertia.post(`/users/${this.user.id}/block`)
    },
    unblockActive() {
      this.$inertia.post(`/users/${this.user.id}/unblock`)
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

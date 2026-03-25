<template>
  <div>
    <Head :title="`${form.name}`" />
    <h1 class="mb-8 font-bold text-3xl">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/uprawnienia">Uprawnienia</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Nazwa" />

          <div class="pb-8 pr-6 w-full lg:w-1/2">
            <label class="form-label">Przypisane Menu:</label>
            <select v-model="form.main_menu_ids" multiple size="5" class="form-select">
              <option v-for="menu in allMainMenus" :key="menu.id" :value="menu.id">
                {{ menu.name }}
              </option>
            </select>
            <div v-if="form.errors.main_menu_ids" class="form-error">{{ form.errors.main_menu_ids }}</div>
          </div>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Usuń Uprawnienie</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Aktualizuj Uprawnienie</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import LoadingButton from '@/Shared/LoadingButton'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    TextInput,
  },
  layout: Layout,
  props: {
    uprawnienia: Object,
    allMainMenus: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        id: this.uprawnienia.id,
        name: this.uprawnienia.name,
        main_menu_ids: this.uprawnienia.main_menus || [], // Initialize with assigned menu IDs
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/uprawnienia/${this.uprawnienia.id}`, {
        onSuccess: () => {
          this.$inertia.visit('/uprawnienia')
        },
      })
    },
    destroy() {
      if (confirm('Czy na pewno chcesz usunąć to uprawnienie?')) {
        this.$inertia.delete(`/uprawnienia/${this.uprawnienia.id}`)
      }
    },
  },
}
</script>

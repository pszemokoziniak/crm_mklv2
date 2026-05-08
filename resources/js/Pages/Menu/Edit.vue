<template>
  <div>
    <Head :title="form.name" />

    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/menu">Menu</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>

    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.route" :error="form.errors.route" class="pb-8 pr-6 w-full lg:w-1/2" label="Route" />
          <text-input v-model="form.icon" :error="form.errors.icon" class="pb-8 pr-6 w-full lg:w-1/2" label="Icon" />
          <text-input v-model="form.order" :error="form.errors.order" class="pb-8 pr-6 w-full lg:w-1/2" label="Order" type="number" />
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update Menu Item</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3' // Changed from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    TextInput,
  },
  layout: Layout,
  props: {
    menuItem: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        _method: 'put',
        name: this.menuItem.name,
        route: this.menuItem.route,
        icon: this.menuItem.icon,
        order: this.menuItem.order,
      }),
    }
  },
  methods: {
    update() {
      this.form.post(`/menu/${this.menuItem.id}`, {
        onSuccess: () => {
          // Optionally reset form or show success message
        },
      })
    },
    destroy() {
      if (confirm('Are you sure you want to delete this menu item?')) {
        this.$inertia.delete(`/menu/${this.menuItem.id}`)
      }
    },
  },
}
</script>

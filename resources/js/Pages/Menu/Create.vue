<template>
  <div>
    <Head title="Create Menu Item" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="route('menu')">Menu</Link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.route" :error="form.errors.route" class="pb-8 pr-6 w-full lg:w-1/2" label="Route" />
          <text-input v-model="form.icon" :error="form.errors.icon" class="pb-8 pr-6 w-full lg:w-1/2" label="Icon" />
          <text-input v-model="form.order" :error="form.errors.order" class="pb-8 pr-6 w-full lg:w-1/2" label="Order" type="number" />
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Menu Item</loading-button>
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
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: '',
        route: '',
        icon: '',
        order: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('menu.store'))
    },
  },
}
</script>

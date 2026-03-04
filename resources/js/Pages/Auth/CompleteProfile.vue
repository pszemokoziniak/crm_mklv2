<template>
  <div class="flex items-center justify-center min-h-screen bg-indigo-900 p-6">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-2 text-center">Uzupełnij swoje dane</h2>
      <p class="text-gray-600 mb-6 text-center text-sm">Aby kontynuować korzystanie z systemu, prosimy o podanie poprawnego imienia i nazwiska.</p>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Imię</label>
          <input v-model="form.first_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': form.errors.first_name }" />
          <div v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700">Nazwisko</label>
          <input v-model="form.last_name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" :class="{ 'border-red-500': form.errors.last_name }" />
          <div v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</div>
        </div>

        <button :disabled="form.processing" type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors font-bold">
          Zapisz i kontynuuj
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'

export default {
  props: {
    user: Object,
  },
  setup() {
    const form = useForm({
      first_name: '',
      last_name: '',
    })

    const submit = () => {
      form.post('/complete-profile')
    }

    return { form, submit }
  },
}
</script>

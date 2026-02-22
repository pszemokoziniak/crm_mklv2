<template>
  <div>
    <Head :title="`${form.nazwa}`" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/futureproject">Przyszłe projekty</Link>
      <span class="text-indigo-400 font-medium" />
    </h1>
    <trashed-message v-if="futureproject.deleted_at" class="mb-6" @restore="restore"> Zapytanie zostało usunięte </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden mb-12">
      <form :class=" (isActive) ? 'border-2 border-green-500' : ''" @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.nazwa" :error="form.errors.nazwa" class="pb-8 pr-6 w-full lg:w-1/1" label="Nazwa projektu" />
          <text-input v-model="form.miasto" :error="form.errors.miasto" class="pb-8 pr-6 w-full lg:w-1/2" label="Miejscowość" />
          <select-input v-model="form.kraj_id" :error="form.errors.kraj_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Kraj">
            <option :value="null" />
            <option v-for="item in krajs" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.objekt_id" :error="form.errors.objekt_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Rodzaj obiektu">
            <option :value="null" />
            <option v-for="item in objekt" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.client_id" :error="form.errors.client_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Generalny wykonawca">
            <option :value="null" />
            <option v-for="item in clients" :key="item.id" :value="item.id">{{ item.nazwa }}</option>
          </select-input>
          <text-input v-model="form.start" :error="form.errors.start" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Start projektu" />
          <text-input v-model="form.end" :error="form.errors.end" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Koniec projektu" />
          <text-area v-model="form.opis" :error="form.errors.opis" class="pb-8 pr-6 w-full lg:w-1/1" label="Opis" />
          <text-input v-model="form.inwestor" :error="form.errors.inwestor" class="pb-8 pr-6 w-full lg:w-1/1" label="Inwestor" />
          <text-area v-model="form.dane_kontaktowe" :error="form.errors.dane_kontaktowe" class="pb-8 pr-6 w-full lg:w-1/1" label="Dane kontaktowe" />
          <select-input v-model="form.faza_id" :error="form.errors.faza_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Faza projektu">
            <option :value="null" />
            <option v-for="item in faza" :key="item.id" :value="item.id">{{ item.name }}</option>
          </select-input>
          <select-input v-model="form.opiekun_id" :error="form.errors.opiekun_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Opiekun">
            <option :value="null" />
            <option v-for="item in users" :key="item.id" :value="item.id">{{ item.first_name }} {{ item.last_name }}</option>
          </select-input>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button v-if="!futureproject.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Popraw</loading-button>
        </div>
      </form>
    </div>

    <!-- Kontakty Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-12">
      <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50 bg-gray-50/30">
        <div class="flex items-center">
          <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
            <icon name="contact" class="w-6 h-6 fill-indigo-600" />
          </div>
          <h2 class="text-xl font-bold text-gray-800">Historia kontaktów</h2>
        </div>
        <Link :href="`/kontakt/create?client=${futureproject.client_id}&future_project_id=${futureproject.id}`" class="btn-indigo flex items-center px-6 py-3 rounded-lg shadow-md transition-all hover:shadow-lg active:scale-95">
          <icon name="plus" class="w-4 h-4 mr-2" />
          <span>Nowy kontakt</span>
        </Link>
      </div>

      <div class="p-8">
        <div v-if="kontakty && kontakty.length > 0" class="space-y-6">
          <div v-for="kontakt in kontakty" :key="kontakt.id" class="border border-gray-100 rounded-xl overflow-hidden shadow-sm">
            <!-- Główny wpis w wątku -->
            <div class="bg-gray-50/50 p-4 border-b border-gray-100 flex justify-between items-center">
              <div class="flex items-center gap-3">
                <span class="font-bold text-indigo-900">{{ kontakt.subject }}</span>
                <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-medium">Wątek</span>
              </div>
              <div class="flex items-center gap-4">
                <span class="text-xs text-gray-500">{{ kontakt.call_date }} {{ kontakt.call_time }}</span>
                <Link :href="`/kontakt/${kontakt.id}/edit`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold uppercase tracking-wider">Edytuj</Link>
              </div>
            </div>
            <div class="p-4 text-gray-700 whitespace-pre-wrap text-sm">
              <div class="mb-2 text-xs text-gray-500">
                Opiekun: <span v-if="kontakt.opiekun" class="font-semibold">{{ kontakt.opiekun.first_name }} {{ kontakt.opiekun.last_name }}</span>
                <span v-else-if="kontakt.kontaktperson" class="font-semibold">{{ kontakt.kontaktperson.first_name }} {{ kontakt.kontaktperson.last_name }}</span>
                <span v-else class="italic">Brak</span>
                • Przez: <span class="font-semibold">{{ kontakt.user.first_name }} {{ kontakt.user.last_name }}</span>
              </div>
              {{ kontakt.description }}
            </div>

            <!-- Odpowiedzi w wątku -->
            <div v-if="kontakt.children && kontakt.children.length > 0" class="bg-white border-t border-gray-50">
              <div v-for="reply in kontakt.children" :key="reply.id" class="p-4 border-b border-gray-50 last:border-0 ml-8 border-l-2">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-xs font-bold text-gray-600">{{ reply.user.first_name }} {{ reply.user.last_name }}</span>
                  <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ reply.call_date }} {{ reply.call_time }}</span>
                    <Link :href="`/kontakt/${reply.id}/edit`" class="text-indigo-400 hover:text-indigo-600 text-xs">Edytuj</Link>
                  </div>
                </div>
                <div class="text-sm text-gray-600 whitespace-pre-wrap">
                  <div v-if="reply.opiekun" class="mb-1 text-[10px] text-gray-400">
                    Opiekun: <span class="font-medium">{{ reply.opiekun.first_name }} {{ reply.opiekun.last_name }}</span>
                  </div>
                  {{ reply.description }}
                </div>
              </div>
            </div>

            <!-- Przycisk odpowiedzi -->
            <div class="bg-gray-50/30 p-3 text-right">
              <Link :href="`/kontakt/create?parent_id=${kontakt.id}&future_project_id=${futureproject.id}`" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center justify-end">
                <icon name="plus" class="w-3 h-3 mr-1" />
                Dodaj odpowiedź w tym wątku
              </Link>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 text-gray-400">
          <icon name="contact" class="w-12 h-12 mx-auto mb-3 opacity-20" />
          <p>Brak zarejestrowanych kontaktów dla tego projektu.</p>
        </div>
      </div>
    </div>

    <!-- Activity Log Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-12">
      <div class="flex items-center px-8 py-6 border-b border-gray-50 bg-gray-50/30">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
          <icon name="activity" class="w-6 h-6 fill-blue-600" />
        </div>
        <h2 class="text-xl font-bold text-gray-800">Historia zmian</h2>
      </div>
      <div class="p-8">
        <div v-if="activities && activities.length > 0" class="space-y-4">
          <div v-for="activity in activities" :key="activity.id" class="border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
            <div class="flex items-center justify-between text-sm text-gray-500 mb-1">
              <span>{{ activity.user }}</span>
              <span>{{ activity.created_at }}</span>
            </div>
            <p class="text-gray-700">{{ activity.description }}</p>
            <div v-if="activity.changes && activity.changes.attributes" class="mt-2 text-xs text-gray-600">
              <span class="font-semibold">Zmieniono:</span>
              <ul class="list-disc list-inside ml-2">
                <li v-for="(value, key) in activity.changes.attributes" :key="key">
                  {{ key }}: <span class="font-medium">{{ value }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-12 text-gray-400">
          <icon name="activity" class="w-12 h-12 mx-auto mb-3 opacity-20" />
          <p>Brak zarejestrowanych zmian dla tego projektu.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import TextArea from '@/Shared/TextareaInput.vue'
import Icon from '@/Shared/Icon.vue'

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
  },
  layout: Layout,
  props: {
    futureproject: Object,
    clients: Object,
    faza: Object,
    objekt: Object,
    krajs: Object,
    users: Object,
    kontakty: Array,
    activities: Array, // Added activities prop
  },
  remember: 'form',
  data() {
    return {
      isActive: false,
      form: this.$inertia.form({
        id: this.futureproject.id,
        nazwa: this.futureproject.nazwa,
        miasto: this.futureproject.miasto,
        kraj_id: this.futureproject.kraj_id,
        objekt_id: this.futureproject.objekt_id,
        client_id: this.futureproject.client_id,
        start: this.futureproject.start,
        end: this.futureproject.end,
        opis: this.futureproject.opis,
        inwestor: this.futureproject.inwestor,
        dane_kontaktowe: this.futureproject.dane_kontaktowe,
        data_kontakt: this.futureproject.data_kontakt,
        faza_id: this.futureproject.faza_id,
        user_id: this.futureproject.user_id,
        opiekun_id: this.futureproject.opiekun_id,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/futureproject/${this.futureproject.id}`)
    },
    destroy() {
      if (confirm('Czy chcesz usunąć te zapytanie?')) {
        this.$inertia.delete(`/futureproject/${this.futureproject.id}`)
      }
    },
    restore() {
      if (confirm('Chcesz przywrócić zapytanie?')) {
        this.$inertia.put(`/futureproject/${this.futureproject.id}/restore`)
      }
    },
  },
}
</script>

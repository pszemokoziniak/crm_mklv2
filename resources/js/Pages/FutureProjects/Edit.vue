<template>
  <div>
    <Head :title="`${form.nazwa}`" />

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-900">
        <Link class="text-indigo-500 hover:text-indigo-700 transition-colors" href="/futureproject">Przyszłe projekty</Link>
        <span class="text-gray-300 font-light mx-2">/</span>
        <span class="text-gray-600">{{ form.nazwa }}</span>
      </h1>
    </div>

    <trashed-message v-if="futureproject.deleted_at" class="mb-6 shadow-sm" @restore="restore"> Zapytanie zostało usunięte </trashed-message>

    <div class="w-full mb-8">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all" :class="{ 'ring-2 ring-green-500 ring-opacity-50 shadow-lg': isActive }">
        <form @submit.prevent="update">
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
            <text-input v-model="form.kwota" :error="form.errors.kwota" class="pb-8 pr-6 w-full lg:w-1/2" label="Kwota" />
          </div>
          <div class="flex items-center justify-between px-8 py-4 bg-gray-50 border-t border-gray-100">
            <button v-if="!futureproject.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
            <loading-button :loading="form.processing" class="btn-indigo ml-auto shadow-md" type="submit">Popraw</loading-button>
          </div>
        </form>
      </div>
    </div>

    <!-- Kontakty Section -->
    <div class="mt-12">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
    </div>

    <!-- Activity Log Section -->
    <div class="mt-12 mb-12">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-gray-50/30 cursor-pointer hover:bg-gray-100/50 transition-colors" @click="isHistoryVisible = !isHistoryVisible">
          <div class="flex items-center">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-4">
              <icon name="printer" class="w-6 h-6 fill-amber-600" />
            </div>
            <h2 class="text-xl font-bold text-gray-800">Historia zmian</h2>
          </div>
          <icon name="cheveron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': isHistoryVisible }" />
        </div>
        <div v-if="isHistoryVisible" class="p-8">
          <div v-if="activities && activities.length > 0" class="flow-root">
            <ul role="list" class="-mb-8">
              <li v-for="(activity, index) in activities" :key="activity.id">
                <div class="relative pb-8">
                  <span v-if="index !== activities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                  <div class="relative flex space-x-3">
                    <div>
                      <span
                        :class="[
                          activity.description === 'deleted' ? 'bg-red-500' : 'bg-blue-500',
                          'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white'
                        ]"
                      >
                        <icon name="edit" class="w-4 h-4 text-white" />
                      </span>
                    </div>
                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                      <div>
                        <p class="text-sm text-gray-500">
                          <span class="font-medium text-gray-900">{{ activity.user }}</span>
                          {{ activity.description }}
                        </p>
                        <div v-if="activity.changes && activity.changes.attributes" class="mt-2 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100">
                          <div v-for="(val, key) in activity.changes.attributes" :key="key" class="mb-1 last:mb-0">
                            <span class="font-bold text-gray-600">{{ key }}:</span>
                            <span v-if="activity.changes.old && activity.changes.old[key]" class="text-red-500 line-through mx-1">{{ activity.changes.old[key] }}</span>
                            <span class="text-green-600 font-medium">{{ val }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="whitespace-nowrap text-right text-sm text-gray-500">
                        <time :datetime="activity.created_at">{{ activity.created_at }}</time>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
          <div v-else class="text-center py-6 text-gray-400 italic">
            Brak zarejestrowanych zmian.
          </div>
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
    activities: Array,
  },
  remember: 'form',
  data() {
    return {
      isActive: false,
      isHistoryVisible: false,
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
        kwota: this.futureproject.kwota,
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

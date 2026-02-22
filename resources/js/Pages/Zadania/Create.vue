<template>
  <div>
    <Head title="Utwórz zadanie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zadania">Zadania</Link>
      <span class="text-indigo-400 font-medium">/</span> Utwórz
    </h1>
    <div class="max-w-5xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="p-8">
          <div class="flex flex-wrap -mb-8 -mr-6">
            <select-input v-model="form.responsible_person_id" :error="form.errors.responsible_person_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Osoba odpowiedzialna">
              <option :value="null" />
              <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
            </select-input>
            <text-input v-model="form.deadline" :error="form.errors.deadline" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wykonania" />
            <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full" label="Temat" />
            <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full" label="Opis" />
          </div>

          <!-- Sekcja Kamieni Milowych -->
          <div class="mt-8 border-t pt-8">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-bold text-gray-800">Kamienie milowe</h2>
              <button type="button" class="btn-indigo text-sm" @click="addMilestone">+ Dodaj kamień milowy</button>
            </div>

            <div v-for="(milestone, mIndex) in form.milestones" :key="mIndex" class="mb-10 bg-gray-50 p-6 rounded-lg border border-gray-200">
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-4">
                  <text-input v-model="milestone.name" label="Nazwa kamienia milowego" placeholder="np. Faza Projektowa" />
                  <text-input v-model="milestone.deadline" type="date" label="Termin końcowy kamienia" />
                </div>
                <button type="button" class="ml-4 text-red-500 hover:text-red-700 p-2" @click="removeMilestone(mIndex)">
                  <icon name="trash" class="w-6 h-6" />
                </button>
              </div>

              <!-- Pod-sekcja Etapów wewnątrz Kamienia -->
              <div class="ml-8 mt-4 border-l-4 border-indigo-200 pl-6">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="text-lg font-semibold text-gray-700">Etapy w tym kamieniu</h3>
                  <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" @click="addStage(mIndex)">+ Dodaj etap</button>
                </div>

                <div v-for="(stage, sIndex) in milestone.stages" :key="sIndex" class="flex items-center space-x-4 mb-3 bg-white p-3 rounded shadow-sm">
                  <div class="flex-1">
                    <text-input v-model="stage.name" placeholder="Nazwa etapu (np. Projekt graficzny)" />
                  </div>
                  <div class="w-40">
                    <select-input v-model="stage.status">
                      <option value="pending">Oczekuje</option>
                      <option value="in_progress">W trakcie</option>
                      <option value="completed">Zakończone</option>
                    </select-input>
                  </div>
                  <button type="button" class="text-red-400 hover:text-red-600" @click="removeStage(mIndex, sIndex)">
                    <icon name="trash" class="w-4 h-4" />
                  </button>
                </div>
                <div v-if="!milestone.stages.length" class="text-gray-400 text-sm italic">Brak etapów dla tego kamienia.</div>
              </div>
            </div>
            <div v-if="!form.milestones.length" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-gray-500">
              Nie dodano jeszcze żadnych kamieni milowych.
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Zapisz zadanie</loading-button>
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
import Icon from '@/Shared/Icon'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TextArea,
    Icon,
  },
  layout: Layout,
  props: {
    users: Array,
  },
  data() {
    return {
      form: this.$inertia.form({
        responsible_person_id: null,
        subject: '',
        description: '',
        deadline: '',
        user_id: this.$page.props.auth.user.id,
        milestones: [],
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/zadania')
    },
    addMilestone() {
      this.form.milestones.push({
        name: '',
        deadline: '',
        stages: [],
      })
    },
    removeMilestone(index) {
      this.form.milestones.splice(index, 1)
    },
    addStage(milestoneIndex) {
      this.form.milestones[milestoneIndex].stages.push({
        name: '',
        status: 'pending',
        order: this.form.milestones[milestoneIndex].stages.length + 1,
      })
    },
    removeStage(milestoneIndex, stageIndex) {
      this.form.milestones[milestoneIndex].stages.splice(stageIndex, 1)
    },
  },
}
</script>

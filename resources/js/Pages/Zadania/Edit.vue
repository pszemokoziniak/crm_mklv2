<template>
  <div>
    <Head title="Popraw zadanie" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zadania">Zadania</Link>
      <span class="text-indigo-400 font-medium">/</span> Popraw
    </h1>
    <trashed-message v-if="zadanie.deleted_at" class="mb-6" @restore="restore"> Zadanie zostało zarchiwizowane </trashed-message>

    <!-- Status banner -->
    <div v-if="zadanie.status === 'do_akceptacji'" class="mb-6 rounded-md bg-yellow-50 border border-yellow-200 p-4">
      <div class="flex items-center">
        <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium text-yellow-800">Zadanie oczekuje na akceptację zamknięcia</span>
      </div>
      <div v-if="isAdmin" class="mt-3 flex space-x-3">
        <button type="button" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700" @click="approveClosure">
          Zatwierdź zamknięcie
        </button>
        <button type="button" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded shadow-sm text-gray-700 bg-white hover:bg-gray-50" @click="rejectClosure">
          Odrzuć
        </button>
      </div>
    </div>

    <div v-if="zadanie.status === 'zamkniete'" class="mb-6 rounded-md bg-gray-100 border border-gray-200 p-4">
      <div class="flex items-center">
        <svg class="h-5 w-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span class="text-sm font-medium text-gray-600">
          Zadanie zamknięte
          <template v-if="zadanie.closed_by">
            przez {{ zadanie.closed_by.first_name }} {{ zadanie.closed_by.last_name }}
          </template>
          <template v-if="zadanie.closed_at">
            dnia {{ zadanie.closed_at }}
          </template>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <div id="form" class="bg-white rounded-md shadow overflow-hidden">
          <fieldset :disabled="zadanie.status === 'zamkniete'">
            <form @submit.prevent="update">
              <div class="p-8">
                <div class="flex flex-wrap -mb-8 -mr-6">
                  <select-input v-model="form.responsible_person_id" :error="form.errors.responsible_person_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Osoba odpowiedzialna">
                    <option v-for="item in users" :key="item.id" :value="item.id">{{ item.last_name }} {{ item.first_name }}</option>
                  </select-input>
                  <text-input v-model="form.deadline" :error="form.errors.deadline" type="date" class="pb-8 pr-6 w-full lg:w-1/2" label="Data wykonania" />
                  <text-input v-model="form.subject" :error="form.errors.subject" class="pb-8 pr-6 w-full" label="Temat" />
                  <text-area v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full" label="Opis" />
                </div>

                <div class="mt-8 border-t pt-8">
                  <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Kamienie milowe</h2>
                    <button type="button" class="btn-indigo text-sm" @click="addMilestone">+ Dodaj kamień milowy</button>
                  </div>

                  <div v-for="(milestone, mIndex) in form.milestones" :key="mIndex" class="mb-10 bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex items-start justify-between mb-4">
                      <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <text-input v-model="milestone.name" label="Nazwa kamienia milowego" />
                        <text-input v-model="milestone.deadline" type="date" label="Termin końcowy" />
                      </div>
                      <button type="button" class="ml-4 text-red-500 hover:text-red-700 p-2" @click="removeMilestone(mIndex)">
                        <icon name="trash" class="w-6 h-6" />
                      </button>
                    </div>

                    <div class="ml-8 mt-4 border-l-4 border-indigo-200 pl-6">
                      <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-semibold text-gray-700">Etapy w tym kamieniu</h3>
                        <button type="button" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" @click="addStage(mIndex)">+ Dodaj etap</button>
                      </div>

                      <div v-for="(stage, sIndex) in milestone.stages" :key="sIndex" class="flex items-center space-x-4 mb-3 bg-white p-3 rounded shadow-sm">
                        <div class="flex-1">
                          <text-input v-model="stage.name" placeholder="Nazwa etapu" />
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
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                <button v-if="!zadanie.deleted_at && zadanie.status !== 'zamkniete'" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Archiwizuj</button>
                <button
                  v-if="canRequestClosure"
                  type="button"
                  class="ml-4 inline-flex items-center px-3 py-1.5 border border-orange-300 text-xs font-medium rounded shadow-sm text-orange-700 bg-orange-50 hover:bg-orange-100"
                  @click="requestClosure"
                >
                  Zgłoś do zamknięcia
                </button>
                <loading-button v-if="zadanie.status !== 'zamkniete'" :loading="form.processing" class="btn-indigo ml-auto" type="submit">Zapisz zmiany</loading-button>
              </div>
            </form>
          </fieldset>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div class="bg-white rounded-md shadow overflow-hidden">
          <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold">Historia opiekunów</h2>
          </div>
          <div class="p-6">
            <div v-if="zadanie.assignments && zadanie.assignments.length" class="flow-root">
              <ul role="list" class="-mb-8">
                <li v-for="(assignment, index) in zadanie.assignments" :key="assignment.id">
                  <div class="relative pb-8">
                    <span v-if="index !== zadanie.assignments.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true" />
                    <div class="relative flex space-x-3">
                      <div>
                        <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                          <icon name="users" class="w-4 h-4 text-white" />
                        </span>
                      </div>
                      <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                        <div>
                          <p class="text-sm text-gray-500">
                            Opiekun: <span v-if="assignment.assigned_user" class="font-medium text-gray-900">{{ assignment.assigned_user.first_name }} {{ assignment.assigned_user.last_name }}</span>
                            <span v-else class="italic text-gray-400">Nieznany</span>
                          </p>
                          <p class="text-xs text-gray-400 mt-1">
                            Przez: <span v-if="assignment.assigner">{{ assignment.assigner.first_name }} {{ assignment.assigner.last_name }}</span>
                            <span v-else class="italic">System</span>
                          </p>
                        </div>
                        <div class="whitespace-nowrap text-right text-xs text-gray-500">
                          {{ assignment.assigned_at ? new Date(assignment.assigned_at).toLocaleDateString() : '-' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div v-else class="text-center py-4 text-gray-400 italic">
              Brak historii przypisań.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link, usePage } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TextArea from '@/Shared/TextareaInput.vue'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
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
    zadanie: Object,
    users: Array,
  },
  data() {
    return {
      form: this.$inertia.form({
        id: this.zadanie.id,
        responsible_person_id: this.zadanie.responsible_person_id,
        subject: this.zadanie.subject,
        description: this.zadanie.description,
        // WYMUSZENIE FORMATU: Jeśli data zawiera czas, zostanie on odcięty
        deadline: this.zadanie.deadline ? this.zadanie.deadline.split('T')[0] : '',
        user_id: this.zadanie.user_id,
        // Mapowanie kamieni milowych, aby ich daty też działały
        milestones: (this.zadanie.milestones || []).map(m => ({
          ...m,
          deadline: m.deadline ? m.deadline.split('T')[0] : '',
        })),
      }),
    }
  },
  computed: {
    authUser() {
      return usePage().props.value.auth.user
    },
    isAdmin() {
      return this.authUser && (this.authUser.is_super_admin || (this.authUser.roles && this.authUser.roles.includes('administrator')))
    },
    isResponsiblePerson() {
      return this.authUser && Number(this.zadanie.responsible_person_id) === Number(this.authUser.id)
    },
    canRequestClosure() {
      return (this.isResponsiblePerson || this.isAdmin) && this.zadanie.status === 'aktywne' && !this.zadanie.deleted_at
    },
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
    requestClosure() {
      if (confirm('Czy na pewno chcesz zgłosić to zadanie do zamknięcia?')) {
        this.$inertia.put(`/zadania/${this.zadanie.id}/request-closure`)
      }
    },
    approveClosure() {
      if (confirm('Czy na pewno chcesz zatwierdzić zamknięcie tego zadania?')) {
        this.$inertia.put(`/zadania/${this.zadanie.id}/approve-closure`)
      }
    },
    rejectClosure() {
      if (confirm('Czy na pewno chcesz odrzucić zamknięcie tego zadania?')) {
        this.$inertia.put(`/zadania/${this.zadanie.id}/reject-closure`)
      }
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

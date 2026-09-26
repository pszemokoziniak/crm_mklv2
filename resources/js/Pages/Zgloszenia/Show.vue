<template>
  <div>
    <Head :title="zgloszenie.title" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/zgloszenia">Zgłoszenia</Link>
      <span class="font-medium text-indigo-400">/</span> {{ zgloszenie.title }}
    </h1>

    <trashed-message v-if="zgloszenie.deleted_at" class="mb-6" @restore="restore">
      To zgłoszenie jest zarchiwizowane.
    </trashed-message>

    <div class="max-w-4xl space-y-6">
      <!-- Nagłówek zgłoszenia -->
      <div class="bg-white rounded-md shadow-sm overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 bg-gray-50 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <select
              v-if="can.updateStatus"
              :value="zgloszenie.status"
              class="form-select text-sm font-medium"
              @change="changeStatus($event.target.value)"
            >
              <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
            </select>
            <span v-else class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full border" :class="statusClass(zgloszenie.status)">
              {{ zgloszenie.status_label }}
            </span>
            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-sm" :class="priorityClass(zgloszenie.priority)">
              {{ priorityLabel(zgloszenie.priority) }}
            </span>
          </div>
          <Link v-if="can.update" class="btn-indigo text-sm" :href="`/zgloszenia/${zgloszenie.id}/edit`">Popraw</Link>
        </div>

        <div class="p-6 space-y-4">
          <div v-if="zgloszenie.url">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Testowana strona</div>
            <a :href="zgloszenie.url" target="_blank" class="text-sm text-indigo-600 break-all hover:underline">{{ zgloszenie.url }}</a>
          </div>

          <div v-if="zgloszenie.description">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Opis</div>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ zgloszenie.description }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-2 text-sm sm:grid-cols-4">
            <div>
              <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Zgłosił</div>
              <div class="text-gray-700">{{ zgloszenie.reporter ? zgloszenie.reporter.name : '-' }}</div>
            </div>
            <div>
              <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Przypisane</div>
              <div class="text-gray-700">{{ zgloszenie.assignee ? zgloszenie.assignee.name : 'nieprzypisane' }}</div>
            </div>
            <div>
              <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Termin</div>
              <div :class="overdue ? 'text-red-600 font-medium' : 'text-gray-700'">{{ zgloszenie.deadline || '-' }}</div>
            </div>
            <div>
              <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Utworzono</div>
              <div class="text-gray-700">{{ zgloszenie.created_at }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Print screeny -->
      <div class="bg-white rounded-md shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-100">
          <h2 class="text-lg font-bold text-gray-800">Print screeny</h2>
          <span class="text-xs font-medium text-gray-400">{{ zgloszenie.screenshots.length }}</span>
        </div>

        <div class="p-6">
          <div v-if="zgloszenie.screenshots.length" class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div v-for="file in zgloszenie.screenshots" :key="file.id" class="relative group">
              <a :href="file.url" target="_blank" :title="file.name">
                <img v-if="file.is_image" :src="file.url" :alt="file.name" class="w-full h-28 object-cover rounded-sm border border-gray-200 hover:opacity-75 transition-opacity" />
                <div v-else class="flex items-center justify-center px-2 w-full h-28 text-xs text-center text-gray-500 bg-gray-50 rounded-sm border border-gray-200">
                  {{ file.name }}
                </div>
              </a>
              <button
                v-if="can.update"
                type="button"
                class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full opacity-90 hover:bg-red-600"
                title="Usuń załącznik"
                @click="removeFile(file)"
              >
                ×
              </button>
            </div>
          </div>
          <p v-else class="mb-4 text-sm text-gray-400 italic">Brak print screenów.</p>

          <div v-if="can.update">
            <screenshot-uploader ref="uploader" v-model="fileForm.screenshots" />
            <div class="flex items-center justify-end mt-3">
              <loading-button
                :loading="fileForm.processing"
                class="btn-indigo text-sm"
                type="button"
                :disabled="fileForm.screenshots.length === 0"
                @click="uploadFiles"
              >
                Dodaj print screeny
              </loading-button>
            </div>
          </div>
        </div>
      </div>

      <!-- Dyskusja -->
      <zgloszenia-dyskusja
        :notable-id="zgloszenie.id"
        :notes="notes"
        :mentionable-users="mentionableUsers"
        :can-comment="can.comment"
      />
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import ScreenshotUploader from '@/Shared/ScreenshotUploader'
import TrashedMessage from '@/Shared/TrashedMessage'
import ZgloszeniaDyskusja from '@/Shared/ZgloszeniaDyskusja'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    ScreenshotUploader,
    TrashedMessage,
    ZgloszeniaDyskusja,
  },
  layout: Layout,
  props: {
    zgloszenie: Object,
    notes: Array,
    statuses: Array,
    priorities: Array,
    mentionableUsers: Array,
    can: Object,
  },
  data() {
    return {
      fileForm: this.$inertia.form({
        screenshots: [],
      }),
    }
  },
  computed: {
    overdue() {
      return this.zgloszenie.deadline
        && this.zgloszenie.status !== 'zrobione'
        && this.zgloszenie.deadline < new Date().toISOString().substr(0, 10)
    },
  },
  methods: {
    changeStatus(status) {
      this.$inertia.put(`/zgloszenia/${this.zgloszenie.id}/status`, { status }, { preserveScroll: true })
    },
    uploadFiles() {
      if (this.fileForm.screenshots.length === 0) {
        return
      }

      this.fileForm.post(`/zgloszenia/${this.zgloszenie.id}/files`, {
        preserveScroll: true,
        onSuccess: () => this.fileForm.reset(),
      })
    },
    removeFile(file) {
      if (!confirm('Usunąć ten załącznik?')) {
        return
      }

      this.$inertia.delete(`/zgloszenia/${this.zgloszenie.id}/files/${file.id}`, { preserveScroll: true })
    },
    restore() {
      this.$inertia.put(`/zgloszenia/${this.zgloszenie.id}/restore`)
    },
    statusClass(status) {
      return {
        do_zrobienia: 'bg-gray-100 text-gray-800 border-gray-200',
        w_toku: 'bg-indigo-100 text-indigo-800 border-indigo-200',
        test: 'bg-yellow-100 text-yellow-800 border-yellow-200',
        zrobione: 'bg-green-100 text-green-800 border-green-200',
      }[status] || 'bg-gray-100 text-gray-800 border-gray-200'
    },
    priorityClass(priority) {
      return {
        wysoki: 'bg-red-100 text-red-700',
        normalny: 'bg-gray-100 text-gray-600',
        niski: 'bg-gray-50 text-gray-400',
      }[priority] || 'bg-gray-100 text-gray-600'
    },
    priorityLabel(priority) {
      return this.priorities.find((item) => item.value === priority)?.label || priority
    },
  },
}
</script>

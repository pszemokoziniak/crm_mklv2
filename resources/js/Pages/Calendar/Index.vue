<template>
  <div>
    <Head title="Kalendarz" />
    <h1 class="mb-8 text-3xl font-bold">Kalendarz</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter-simple v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset"></search-filter-simple>
    </div>
    <div class="flex flex-wrap -mb-8 -mr-6 pb-4">
      <text-input v-model="form.start" :value="start" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Od" />
      <text-input v-model="form.end" :value="end" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Do" />
    </div>
    <div class="p-2">
      Od: {{ start }} - Do: {{ end }}
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <td v-for="(item, index) in months" :key="item" :colspan="item" class="border-solid border-2 border-indigo-600 p-2">{{ index }}</td>
        </tr>
        <tr class="text-left font-bold">
          <td v-for="item in days" :key="item" colspan=1 class="border-solid border-2 border-indigo-600 p-1">{{ item }}</td>
        </tr>
        <tr v-for="item in zapytanias" :key="item" class="text-left font-bold">
          <td v-for="(col, index) in item.colSpan" :key="col" :colspan="col" :style="{backgroundColor: randomColor(item.id, item.colSpan[index][1])}" class="border-solid border-2 border-indigo-600 p-1"><Link :href="`/zapytania/${item.id}/edit`"><span v-show="item.colSpan[index][1]===1">{{ item.id_zapyt }} - {{ item.nazwa_projektu }} - {{ item.client.nazwa }} - {{ item.start }} - {{ item.end }}</span></Link></td>
        </tr>
        <tr v-if="months.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">Brak</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from "@/Shared/SearchFilter.vue";
import mapValues from "lodash/mapValues";
import throttle from "lodash/throttle";
import pickBy from "lodash/pickBy";
import TextInput from "@/Shared/TextInput.vue";
import SearchFilterSimple from "@/Shared/SearchFilterSimple.vue";

export default {
  components: {
    SearchFilterSimple,
    TextInput,
    SearchFilter,
    Head,
    Icon,
    Link,
    Pagination,
  },
  layout: Layout,
  props: {
    months: Object,
    days: Array,
    zapytanias: Array,
    daysN: Number,
    start: Date,
    end: Date,
    filters: Object,
  },
  data() {
    return {
      colorCache: {},
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
        start: this.filters.start,
        end: this.filters.end,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/calendar', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    randomColor(id, flag) {
      if (flag===1) {
        const r = () => Math.floor(256 * Math.random());

        return this.colorCache[id] || (this.colorCache[id] = `rgb(${r()}, ${r()}, ${r()})`);
      }
    },
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  }
}
</script>

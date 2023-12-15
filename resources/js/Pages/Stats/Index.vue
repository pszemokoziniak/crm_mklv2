<template>
  <div>
    <Head title="Statystyki" />
    <h1 class="mb-8 text-3xl font-bold">Statystyki</h1>
      <div class="flex flex-wrap -mb-8 -mr-6 pb-4">
        <text-input v-model="form.start" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Od" />
        <text-input v-model="form.end" type="date" class="pb-8 pr-6 w-full lg:w-1/4" label="Do" />
      </div>

    <div class="bg-white rounded-md shadow overflow-x-auto">
      <h2 class="flex items-center text-3xl font-extrabold dark:text-white p-1">Klienci</h2>
      <hr>
      <p class="text-2xl p-1">Ilość klientów - {{clientNumber}}</p>
      <users-add-clients :clientNumber="clientNumber" :clientNumberByUser="clientNumberByUser"/>
      <active-client :clientActive="clientActive"/>
      <increase-client :increaseClients="increaseClients"/>
      <client-branza :clientBranza="clientBranza"/>

    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'
import UsersAddClients from "@/Pages/Stats/UsersAddClients.vue";
import ActiveClient from "@/Pages/Stats/ActiveClient.vue";
import IncreaseClient from "@/Pages/Stats/IncreaseClient.vue";
import ClientBranza from "@/Pages/Stats/ClientBranza.vue";
import TextInput from "@/Shared/TextInput.vue";

export default {
  components: {
    TextInput,
    UsersAddClients,
    ActiveClient,
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
    IncreaseClient,
    ClientBranza,
  },
  layout: Layout,
  props: {
    clientNumber: Number,
    clientNumberByUser: Object,
    clientActive: Array,
    increaseClients: Array,
    clientBranza: Array,
    start: Date,
    end: Date,
    // filters: Object,
    // zapytanias: Object,
  },
  data() {
    return {
      form: {
        start: this.start,
        end: this.end,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/stats', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>


<template>
  <div>
    <h1 class="text-2xl font-bold p-1">Pracownicy którzy dodali</h1>
    <p v-for="item in clientNumberByNumber" :key="item.id" class="p-1">{{ item.last_name }} {{ item.first_name }} dodał <span class="text-red-700">{{ item.count }}</span> klientów | procentowo: {{ parseFloat(item.count/clientNumber*100).toFixed(2) }}%  </p>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  components: {
    Icon,
    Link,
    SearchFilter,
  },
  props: {
    clientNumber: Number,
    clientNumberByNumber: Object,
  },
  data() {
    return {
      form: {
        // search: this.filters.search,
        // trashed: this.filters.trashed,
      },
    }
  },
}
</script>


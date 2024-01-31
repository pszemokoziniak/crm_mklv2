<template>
  <div>
    <Head title="Do zrobienia" />
    <Historia :historia="historia"/>
    <hr class="my-5">
    <h1 class="mb-8 text-3xl font-bold">Do zrobienia</h1>
    <div class="grid grid-cols-5 gap-4">
      <div>
        <p class="text-center w-full p-2 text-indigo-600">Klienci kontakty</p>
        <div v-for="item in kontakts" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100 border-2 rounded p-2 mb-2">
          <Link class="" :href="`/kontakt/${item.id}/edit`">
            <p class="p-1">{{item.client.nazwa}}</p>
            <p class="p-1">{{item.kontaktperson.last_name}} {{item.kontaktperson.first_name}}</p>
            <p class="p-1">{{item.subject}}</p>
            <p class="p-1">{{item.call_time}}</p>
            <p class="p-1 text-red-600">{{item.user.last_name}} {{item.user.first_name}}</p>
          </Link>
        </div>
      </div>
      <div>
        <p class="text-center w-full p-2 text-indigo-600">Zapytania</p>
        <div v-for="item in zapytanias" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100 border-2 rounded p-2 mb-2">
          <Link class="" :href="`/zapytania/${item.id}/edit`">
            <p v-if="item.wznowienie===2" class="p-1 text-red-600">Wznowienie</p>
            <p class="p-1">{{item.id_zapyt}}</p>
            <p class="p-1">{{item.client.nazwa}}</p>
            <p class="p-1">{{item.nazwa_projektu}}</p>
            <p class="p-1">{{item.data_zlozenia}}</p>
            <p class="p-1 text-red-600">{{item.user.last_name}} {{item.user.first_name}}</p>
          </Link>
        </div>
      </div>
      <div>
        <p class="text-center w-full p-2 text-indigo-600">Oferty</p>
        <div v-for="item in ofertas.data" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100 border-2 rounded p-2 mb-2">
          <Link class="" :href="`/oferta/${item.id}/edit`">
            <p class="p-1">{{item.zapytania.nazwa_projektu}}</p>
            <p class="p-1">{{item.client.nazwa}}</p>
            <p class="p-1">{{item.data_kontakt}}</p>
            <p class="p-1 text-red-600">{{item.user.last_name}} {{item.user.first_name}}</p>
          </Link>
        </div>
      </div>
      <div>
        <p class="text-center w-full p-2 text-indigo-600">Przyszłe projekty</p>
        <div v-for="item in futureProjects" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100 border-2 rounded p-2 mb-2">
          <Link class="" :href="`/futureproject/${item.id}/edit`">
            <p class="p-1">{{item.nazwa}}</p>
            <p class="p-1">{{item.client.nazwa}}</p>
            <p class="p-1">{{item.data_kontakt}}</p>
            <p class="p-1 text-red-600">{{item.user.last_name}} {{item.user.first_name}}</p>
          </Link>
        </div>
      </div>
      <div>
        <p class="text-center w-full p-2 text-indigo-600">Zadania</p>
        <div v-for="item in zadania" :key="item.id" class="hover:bg-gray-100 focus-within:bg-gray-100 border-2 rounded p-2 mb-2">
          <Link class="" :href="`/zadania/${item.id}/edit`">
            <p class="p-1">{{item.users.last_name}} {{item.users.first_name}}</p>
            <p class="p-1">{{item.subject}}</p>
            <p class="p-1">{{item.deadline}}</p>
<!--            <p class="p-1 text-red-600">{{item.user.last_name}} {{item.user.first_name}}</p>-->
          </Link>
        </div>
      </div>
    </div>

<!--    <p class="mb-8 leading-normal">Hey there! Welcome to Ping CRM, a demo app designed to help illustrate how <a class="text-indigo-500 hover:text-orange-600 underline" href="https://inertiajs.com">Inertia.js</a> works.</p>-->
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import Historia from '@/Pages/ActivityLog/Index.vue'

export default {
  components: {
    Head,
    Link,
    Historia,
  },
  layout: Layout,
  props: {
    kontakts: Object,
    zapytanias: Object,
    ofertas: Object,
    futureProjects: Object,
    zadania: Object,
    historia: Object,
  }
}
</script>

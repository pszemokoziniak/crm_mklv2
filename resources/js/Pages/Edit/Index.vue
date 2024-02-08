<template>
<!--  <div class="md:flex md:flex-grow md:overflow-hidden">-->
<!--    <edit-menu class="hidden flex-shrink-0 p-12 w-56 bg-indigo-800 overflow-y-auto md:block" />-->
<!--    <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto" scroll-region>-->
<!--      <flash-messages />-->
<!--      <slot />-->
<!--    </div>-->
<!--  </div>-->

  <div class="w-full">
    <Head title="Edit" />
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 md:gap-1 lg:gap-3 md:mx-10 lg:mx-20 lg:my-auto">
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/branza/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Branże</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/kraj/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Kraj</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/zakres/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Zakres</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/ofertastatus/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Oferty Status</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/kursy/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Kursy</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/faza/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Faza projektu</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/objekt/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Objekt projektu</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/waluta/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Waluta</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/uprawnienia/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Uprawnienia</div>
          </div>
        </div>
      </Link>
      <Link class="block mt-4 lg:inline-block lg:mt-0" :href="`/email/`">
        <div class="h-50 lg:max-w-md sm:mx-10 md:mx-5 lg:mx-2 rounded border border-greyMf-200 overflow-hidden shadow-lg">
          <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 flex justify-center">Email</div>
          </div>
        </div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import Layout from '@/Shared/Layout'
import EditMenu from '@/Shared/EditMenu'
import MainMenu from "@/Shared/MainMenu.vue";
import FlashMessages from "@/Shared/FlashMessages.vue";
// import Icon from '@/Shared/Icon'

export default {
  components: {
    FlashMessages, MainMenu,
    Head,
    EditMenu,
    Link
  },
  layout: Layout,
}
</script>

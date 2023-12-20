<template>
  <div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/branza">
        <div :class="isUrl('branza') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Branża</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/kraj">
        <div :class="isUrl('kraj') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Kraj</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/zakres">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('zakres') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('zakres') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Zakres</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/ofertastatus">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('ofertastatus') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('ofertastatus') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Oferty Status</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/kursy">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('kursy') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('kursy') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Kursy</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/faza">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('faza') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('faza') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Faza projektu</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/objekt">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('objekt') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('objekt') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Objekt projektu</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/reports">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Statystyki</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/reports">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Edit</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/reports">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Użytkownikcy</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/reports">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Historia</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/reports">
        <icon name="printer" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">BackUp</div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
    Link,
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>

<template>
  <div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/">
        <icon name="home" class="mr-2 w-4 h-4" :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Home</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/clients">
        <icon name="clients" class="mr-2 w-4 h-4" :class="isUrl('clients') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('clients') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Klienci</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/zapytania">
        <icon name="zapytania" class="mr-2 w-4 h-4" :class="isUrl('contacts') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('zapytania') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Zapytania</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/oferta">
        <icon name="oferty" class="mr-2 w-4 h-4" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Oferty</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/calendar">
        <icon name="calendar" class="mr-2 w-4 h-4" :class="isUrl('calendar') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('calendar') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Kalendarz</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/futureproject">
        <icon name="future" class="mr-2 w-4 h-4" :class="isUrl('futureproject') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('futureproject') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Przyszłe projekty</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/linkedin">
        <icon name="linkedin" class="mr-2 w-4 h-4" :class="isUrl('linkedin') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('linkedin') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">LinkedIn</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/stronywww">
        <icon name="www" class="mr-2 w-4 h-4" :class="isUrl('stronywww') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('stronywww') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Linki www</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/stats">
        <icon name="statystyki" class="mr-2 w-4 h-4" :class="isUrl('stats') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('stats') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Statystyki</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/edit">
        <icon name="edit" class="mr-2 w-4 h-4" :class="isUrl('edit') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('edit') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Edit</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/users">
        <icon name="users" class="mr-2 w-4 h-4" :class="isUrl('users') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('users') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Użytkownikcy</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/activity">
        <icon name="historia" class="mr-2 w-4 h-4" :class="isUrl('activity') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('activity') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Historia</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/backup">
        <icon name="backup" class="mr-2 w-4 h-4" :class="isUrl('backup') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('backup') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">BackUp</div>
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

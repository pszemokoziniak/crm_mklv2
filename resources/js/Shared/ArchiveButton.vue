<template>
  <button class="flex items-center text-red-600 hover:text-red-800 transition-colors" type="button">
    <svg class="mr-2 w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path d="M7 11H17V13H7V11Z" />
      <path fill-rule="evenodd" clip-rule="evenodd" d="M5 2C3.34315 2 2 3.34315 2 5V7C2 8.65685 3.34315 10 5 10V19C5 20.6569 6.34315 22 8 22H16C17.6569 22 19 20.6569 19 19V10C20.6569 10 22 8.65685 22 7V5C22 3.34315 20.6569 2 19 2H5ZM5 4H19C19.5523 4 20 4.44772 20 5V7C20 7.55228 19.5523 8 19 8H5C4.44772 8 4 7.55228 4 7V5C4 4.44772 4.44772 4 5 4ZM7 10V19C7 19.5523 7.44772 20 8 20H16C16.5523 20 17 19.5523 17 19V10H7Z" />
    </svg>
    <slot>Archiwizuj</slot>
  </button>
</template>

<script>
export default {
  name: 'ArchiveButton',
}
</script>

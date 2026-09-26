import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import DateFilter from './Filters/DateFilter' // Import the DateFilter
// Usunięto import ZiggyVue, ponieważ pakiet nie może być zainstalowany

createInertiaApp({
  resolve: name => {
    const page = require(`./Pages/${name}.vue`)
    return page.default || page
  },
  // Wbudowany wskaźnik postępu (dawniej osobny @inertiajs/progress) — te same ustawienia.
  progress: { color: '#29d', delay: 250 },
  title: title => {
    const baseTitle = 'CRM'
    return title ? `${title} - ${baseTitle}` : baseTitle
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(DateFilter) // Register the DateFilter plugin
    app.config.globalProperties.route = window.route
    app.mount(el)
  },
})

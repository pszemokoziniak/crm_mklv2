import { createApp, h } from 'vue'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp } from '@inertiajs/inertia-vue3'

InertiaProgress.init()

createInertiaApp({
  resolve: name => {
    const page = require(`./Pages/${name}.vue`)
    return page.default || page
  },
  title: title => {
    const baseTitle = 'CRM'
    return title ? `${title} - ${baseTitle}` : baseTitle
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el)
  },
})

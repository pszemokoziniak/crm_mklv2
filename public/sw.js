self.addEventListener('push', function (event) {
  if (!event.data) return

  var data = event.data.json()
  var title = data.title || 'MKL CRM'
  var options = {
    body: data.body || '',
    icon: data.icon || '/favicon.svg',
    data: data.data || {},
    actions: data.actions || [],
  }

  event.waitUntil(
    self.registration.showNotification(title, options)
  )
})

self.addEventListener('notificationclick', function (event) {
  event.notification.close()

  var url = '/'
  if (event.notification.data && event.notification.data.url) {
    url = event.notification.data.url
  } else if (event.action) {
    url = event.action
  }

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
      for (var i = 0; i < clientList.length; i++) {
        var client = clientList[i]
        if (client.url.indexOf(url) !== -1 && 'focus' in client) {
          return client.focus()
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url)
      }
    })
  )
})

import axios from 'axios'

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = window.atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}

export async function initPushNotifications(vapidPublicKey) {
  if (!vapidPublicKey || !('serviceWorker' in navigator) || !('PushManager' in window)) {
    return
  }

  try {
    const registration = await navigator.serviceWorker.register('/sw.js')

    const existingSubscription = await registration.pushManager.getSubscription()
    if (existingSubscription) {
      return
    }

    const permission = await Notification.requestPermission()
    if (permission !== 'granted') {
      return
    }

    const subscription = await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
    })

    const subJson = subscription.toJSON()

    await axios.post('/push-subscriptions', {
      endpoint: subJson.endpoint,
      keys: {
        auth: subJson.keys.auth,
        p256dh: subJson.keys.p256dh,
      },
      content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
    })
  } catch (e) {
    console.warn('Push notification registration failed:', e)
  }
}

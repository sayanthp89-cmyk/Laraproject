import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useNotificationStore = defineStore('notifications', () => {
  const message = ref('')
  const type = ref<'success' | 'error'>('success')
  let timeout: ReturnType<typeof setTimeout> | undefined

  function show(text: string, kind: 'success' | 'error' = 'success') {
    message.value = text
    type.value = kind
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => { message.value = '' }, 4000)
  }

  return { message, type, show }
})

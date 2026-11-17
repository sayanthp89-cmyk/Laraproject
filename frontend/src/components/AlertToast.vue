<script setup lang="ts">
import { useNotificationStore } from '../stores/notifications'

const notifications = useNotificationStore()
</script>

<template>
  <Transition name="toast">
    <div v-if="notifications.message" class="fixed right-5 top-5 z-50 flex max-w-sm items-center gap-3 rounded-2xl px-5 py-4 text-sm font-medium text-white shadow-xl"
      :class="notifications.type === 'success' ? 'bg-slate-900' : 'bg-rose-600'" role="status">
      <span class="grid size-7 place-items-center rounded-full bg-white/15">{{ notifications.type === 'success' ? '✓' : '!' }}</span>
      {{ notifications.message }}
    </div>
  </Transition>
</template>

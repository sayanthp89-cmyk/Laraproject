<script setup lang="ts">
defineProps<{ open: boolean; title: string; description?: string }>()
defineEmits<{ close: []; confirm: [] }>()
</script>

<template>
  <div v-if="open" class="fixed inset-0 z-40 grid place-items-center bg-slate-950/40 p-4" @click.self="$emit('close')">
    <section class="w-full max-w-md rounded-3xl bg-white p-7 shadow-2xl">
      <div class="mb-6 flex items-start justify-between gap-4">
        <div><h2 class="text-xl font-bold text-slate-900">{{ title }}</h2><p v-if="description" class="mt-2 text-sm leading-6 text-slate-500">{{ description }}</p></div>
        <button class="rounded-lg px-2 text-xl text-slate-400 hover:bg-slate-100" aria-label="Close dialog" @click="$emit('close')">×</button>
      </div>
      <slot />
    </section>
  </div>
</template>

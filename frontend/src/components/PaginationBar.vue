<script setup lang="ts">
import type { PageMeta } from '../types'

defineProps<{ meta: PageMeta | null }>()
defineEmits<{ change: [page: number] }>()
</script>

<template>
  <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between border-t border-slate-100 px-5 py-4 text-sm">
    <span class="text-slate-500">Page {{ meta.current_page }} of {{ meta.last_page }} · {{ meta.total }} total</span>
    <div class="flex gap-2">
      <button class="rounded-xl border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-40" :disabled="meta.current_page <= 1" @click="$emit('change', meta.current_page - 1)">Previous</button>
      <button class="rounded-xl border border-slate-200 px-3 py-2 text-slate-600 disabled:opacity-40" :disabled="meta.current_page >= meta.last_page" @click="$emit('change', meta.current_page + 1)">Next</button>
    </div>
  </div>
</template>

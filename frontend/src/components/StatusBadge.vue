<script setup lang="ts">
import type { TaskPriority, TaskStatus } from '../types'

const props = defineProps<{ value: TaskStatus | TaskPriority }>()
const labels: Record<string, string> = { pending: 'Pending', in_progress: 'In progress', completed: 'Completed', low: 'Low', medium: 'Medium', high: 'High' }
const classes: Record<string, string> = {
  pending: 'bg-amber-50 text-amber-700', in_progress: 'bg-violet-50 text-violet-700', completed: 'bg-emerald-50 text-emerald-700',
  low: 'bg-slate-100 text-slate-600', medium: 'bg-blue-50 text-blue-700', high: 'bg-rose-50 text-rose-700',
}
</script>

<template><span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold" :class="classes[props.value]">{{ labels[props.value] }}</span></template>

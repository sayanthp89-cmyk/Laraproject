<script setup lang="ts">
import type { Task, TaskStatus } from '../types'
import StatusBadge from './StatusBadge.vue'

defineProps<{ tasks: Task[]; currentUserId: number | undefined }>()
const emit = defineEmits<{ edit: [task: Task]; remove: [task: Task]; statusChange: [task: Task, status: TaskStatus] }>()

function changeStatus(task: Task, event: Event) {
  emit('statusChange', task, (event.target as HTMLSelectElement).value as TaskStatus)
}

function prettyDate(date: string | null) {
  if (!date) return 'No due date'
  return new Intl.DateTimeFormat('en', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' }).format(new Date(`${date}T00:00:00Z`))
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[760px] text-left">
      <thead><tr class="border-b border-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-400">
        <th class="px-5 py-4">Task</th><th class="px-4 py-4">Assignee</th><th class="px-4 py-4">Due date</th><th class="px-4 py-4">Priority</th><th class="px-4 py-4">Status</th><th class="px-5 py-4 text-right">Actions</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        <tr v-for="task in tasks" :key="task.id" class="transition hover:bg-slate-50/80">
          <td class="max-w-[280px] px-5 py-4"><p class="truncate font-semibold text-slate-800">{{ task.title }}</p><p v-if="task.description" class="mt-1 truncate text-xs text-slate-400">{{ task.description }}</p></td>
          <td class="px-4 py-4"><div class="flex items-center gap-2"><span class="grid size-8 shrink-0 place-items-center rounded-full bg-violet-100 text-xs font-bold text-violet-700">{{ (task.assignee?.name || '—').slice(0, 1).toUpperCase() }}</span><span class="max-w-28 truncate text-sm text-slate-600">{{ task.assignee?.name || 'Unassigned' }}</span></div></td>
          <td class="px-4 py-4 text-sm" :class="task.is_overdue ? 'font-semibold text-rose-600' : 'text-slate-500'">{{ prettyDate(task.due_date) }}<span v-if="task.is_overdue" class="ml-1 text-[10px] uppercase">Late</span></td>
          <td class="px-4 py-4"><StatusBadge :value="task.priority" /></td><td class="px-4 py-4"><select v-if="task.assigned_to === currentUserId" :value="task.status" :aria-label="`Status for ${task.title}`" class="rounded-full border-0 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-violet-300" @change="changeStatus(task, $event)"><option value="pending">Pending</option><option value="in_progress">In progress</option><option value="completed">Completed</option></select><StatusBadge v-else :value="task.status" /></td>
          <td class="px-5 py-4"><div v-if="task.created_by === currentUserId" class="flex justify-end gap-1"><button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-violet-700 hover:bg-violet-50" @click="$emit('edit', task)">Edit</button><button class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50" @click="$emit('remove', task)">Delete</button></div><span v-else class="block text-right text-xs text-slate-400">Assigned to you</span></td>
        </tr>
        <tr v-if="tasks.length === 0"><td colspan="6" class="px-5 py-14 text-center"><div class="mx-auto mb-3 grid size-12 place-items-center rounded-2xl bg-slate-100 text-xl text-slate-400">✓</div><p class="font-semibold text-slate-700">Nothing here yet</p><p class="mt-1 text-sm text-slate-400">Try changing your filters or add a new task.</p></td></tr>
      </tbody>
    </table>
  </div>
</template>

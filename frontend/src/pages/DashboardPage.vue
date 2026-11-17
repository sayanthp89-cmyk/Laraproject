<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api, { errorMessage } from '../services/api'
import type { Task } from '../types'
import { useNotificationStore } from '../stores/notifications'
import StatusBadge from '../components/StatusBadge.vue'

interface DashboardData {
  stats: { total: number; pending: number; in_progress: number; completed: number; overdue: number }
  recent_tasks: Task[]
}

const dashboard = ref<DashboardData | null>(null)
const loading = ref(true)
const notifications = useNotificationStore()

onMounted(async () => {
  try {
    const { data } = await api.get<DashboardData>('/dashboard')
    dashboard.value = data
  } catch (error) {
    notifications.show(errorMessage(error), 'error')
  } finally { loading.value = false }
})

const cards = [
  { key: 'total', label: 'Total tasks', icon: '▤', color: 'bg-violet-50 text-violet-700' },
  { key: 'pending', label: 'Pending', icon: '◷', color: 'bg-amber-50 text-amber-700' },
  { key: 'in_progress', label: 'In progress', icon: '↗', color: 'bg-blue-50 text-blue-700' },
  { key: 'completed', label: 'Completed', icon: '✓', color: 'bg-emerald-50 text-emerald-700' },
  { key: 'overdue', label: 'Overdue', icon: '!', color: 'bg-rose-50 text-rose-700' },
] as const

function dateLabel(date: string | null) {
  if (!date) return 'No due date'
  return new Intl.DateTimeFormat('en', { month: 'short', day: 'numeric', timeZone: 'UTC' }).format(new Date(`${date}T00:00:00Z`))
}
</script>

<template>
  <div>
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div><p class="text-sm font-semibold text-violet-600">YOUR WORKSPACE</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">A clear view of your work.</h1><p class="mt-2 text-slate-500">A little progress adds up. Here's where things stand today.</p></div>
      <RouterLink to="/tasks" class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet-200 hover:bg-violet-700">＋ Create a task</RouterLink>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
      <article v-for="card in cards" :key="card.key" class="rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm shadow-slate-100/70">
        <div class="flex items-center justify-between"><span class="text-sm font-medium text-slate-500">{{ card.label }}</span><span class="grid size-9 place-items-center rounded-xl text-lg" :class="card.color">{{ card.icon }}</span></div>
        <p class="mt-5 text-3xl font-bold tracking-tight text-slate-900">{{ loading ? '—' : dashboard?.stats[card.key] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-400">{{ card.key === 'overdue' ? 'Needs a little attention' : 'Tasks in your workspace' }}</p>
      </article>
    </div>
    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm shadow-slate-100/70">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-7"><div><h2 class="font-bold text-slate-900">Recently updated</h2><p class="mt-1 text-xs text-slate-400">The latest work across your workspace</p></div><RouterLink to="/tasks" class="text-sm font-semibold text-violet-700 hover:text-violet-900">All tasks →</RouterLink></div>
      <div v-if="loading" class="space-y-4 p-6"><div v-for="n in 4" :key="n" class="h-12 animate-pulse rounded-xl bg-slate-100"></div></div>
      <div v-else-if="!dashboard?.recent_tasks.length" class="py-16 text-center"><div class="mx-auto grid size-12 place-items-center rounded-2xl bg-violet-50 text-xl text-violet-600">✦</div><p class="mt-3 font-semibold text-slate-800">Your next great thing starts here.</p><RouterLink to="/tasks" class="mt-2 inline-block text-sm font-semibold text-violet-700">Create your first task →</RouterLink></div>
      <div v-else class="divide-y divide-slate-100">
        <RouterLink v-for="task in dashboard.recent_tasks" :key="task.id" to="/tasks" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 transition hover:bg-slate-50 sm:px-7">
          <div class="min-w-[220px] flex-1"><p class="font-semibold text-slate-800">{{ task.title }}</p><p class="mt-1 text-xs text-slate-400">Assigned to {{ task.assignee?.name || 'no one' }} · due {{ dateLabel(task.due_date) }}</p></div>
          <div class="flex items-center gap-3"><StatusBadge :value="task.priority" /><StatusBadge :value="task.status" /></div>
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { errorMessage } from '../services/api'
import { fetchUser } from '../services/users'
import type { Task, User } from '../types'
import { useNotificationStore } from '../stores/notifications'
import StatusBadge from '../components/StatusBadge.vue'

const route = useRoute()
const notifications = useNotificationStore()
const person = ref<User | null>(null)
const recentTasks = ref<Task[]>([])
const loading = ref(true)

async function load() {
  loading.value = true
  try {
    const result = await fetchUser(Number(route.params.id))
    person.value = result.user
    recentTasks.value = result.recent_tasks
  } catch (error) { notifications.show(errorMessage(error), 'error') }
  finally { loading.value = false }
}

onMounted(() => { void load() })
watch(() => route.params.id, () => { void load() })

function dateLabel(date: string | null) {
  if (!date) return 'No date'
  return new Intl.DateTimeFormat('en', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'UTC' }).format(new Date(`${date}T00:00:00Z`))
}
</script>

<template>
  <div>
    <RouterLink to="/users" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-violet-700">← All people</RouterLink>
    <div v-if="loading" class="h-64 animate-pulse rounded-2xl bg-white"></div>
    <template v-else-if="person">
      <section class="rounded-2xl border border-slate-200/70 bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center gap-5"><span class="grid size-20 place-items-center rounded-3xl bg-violet-100 text-3xl font-bold text-violet-700">{{ person.name.slice(0, 1).toUpperCase() }}</span><div><p class="text-sm font-semibold text-violet-600">TEAMMATE</p><h1 class="mt-1 text-3xl font-bold text-slate-900">{{ person.name }}</h1><p class="mt-1 text-slate-500">{{ person.email }}</p></div></div>
        <div class="mt-8 grid max-w-md grid-cols-2 gap-4"><div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-bold text-slate-900">{{ person.created_tasks_count ?? 0 }}</p><p class="mt-1 text-xs text-slate-500">Tasks created</p></div><div class="rounded-xl bg-slate-50 p-4"><p class="text-2xl font-bold text-slate-900">{{ person.assigned_tasks_count ?? 0 }}</p><p class="mt-1 text-xs text-slate-500">Tasks assigned</p></div></div>
      </section>
      <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5"><h2 class="font-bold text-slate-900">Recently created tasks</h2><p class="mt-1 text-xs text-slate-400">A glimpse of the work {{ person.name.split(' ')[0] }} has been moving forward.</p></div>
        <div v-if="!recentTasks.length" class="py-12 text-center text-sm text-slate-400">No tasks created yet.</div>
        <div v-else class="divide-y divide-slate-100">
          <div v-for="task in recentTasks" :key="task.id" class="flex flex-wrap items-center justify-between gap-3 px-6 py-4"><div><p class="font-semibold text-slate-800">{{ task.title }}</p><p class="mt-1 text-xs text-slate-400">Due {{ dateLabel(task.due_date) }} · {{ task.assignee?.name || 'Unassigned' }}</p></div><div class="flex gap-2"><StatusBadge :value="task.priority" /><StatusBadge :value="task.status" /></div></div>
        </div>
      </section>
    </template>
  </div>
</template>

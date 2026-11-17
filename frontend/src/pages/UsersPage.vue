<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { errorMessage } from '../services/api'
import { fetchUsers } from '../services/users'
import type { PageMeta, User } from '../types'
import { useNotificationStore } from '../stores/notifications'

const people = ref<User[]>([])
const meta = ref<PageMeta | null>(null)
const search = ref('')
const loading = ref(true)
const notifications = useNotificationStore()
let timer: ReturnType<typeof setTimeout> | undefined

async function loadPeople() {
  loading.value = true
  try {
    const response = await fetchUsers(search.value)
    people.value = response.data
    meta.value = response.meta
  } catch (error) { notifications.show(errorMessage(error), 'error') }
  finally { loading.value = false }
}

onMounted(() => { void loadPeople() })
watch(search, () => {
  if (timer) clearTimeout(timer)
  timer = setTimeout(() => { void loadPeople() }, 300)
})
</script>

<template>
  <div>
    <div class="mb-7"><p class="text-sm font-semibold text-violet-600">GOOD THINGS TAKE A TEAM</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">People</h1><p class="mt-2 text-slate-500">The thoughtful humans behind the work.</p></div>
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm shadow-slate-100/70"><div><p class="font-bold text-slate-800">Your teammates</p><p class="mt-1 text-xs text-slate-400">{{ meta?.total ?? 0 }} people in your workspace</p></div><input v-model="search" placeholder="Search people…" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100 sm:max-w-xs" /></div>
    <div v-if="loading" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"><div v-for="n in 6" :key="n" class="h-52 animate-pulse rounded-2xl bg-white"></div></div>
    <div v-else-if="!people.length" class="rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-500">No teammates match that search.</div>
    <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <RouterLink v-for="person in people" :key="person.id" :to="`/users/${person.id}`" class="group rounded-2xl border border-slate-200/70 bg-white p-5 shadow-sm shadow-slate-100/70 transition hover:-translate-y-0.5 hover:border-violet-200 hover:shadow-md">
        <div class="flex items-center gap-3"><span class="grid size-12 place-items-center rounded-2xl bg-violet-100 text-lg font-bold text-violet-700">{{ person.name.slice(0, 1).toUpperCase() }}</span><div class="min-w-0"><p class="truncate font-bold text-slate-800">{{ person.name }}</p><p class="truncate text-sm text-slate-400">{{ person.email }}</p></div></div>
        <div class="mt-6 grid grid-cols-2 gap-3"><div class="rounded-xl bg-slate-50 px-3 py-3"><p class="text-xl font-bold text-slate-800">{{ person.created_tasks_count ?? 0 }}</p><p class="mt-1 text-xs text-slate-400">Created</p></div><div class="rounded-xl bg-slate-50 px-3 py-3"><p class="text-xl font-bold text-slate-800">{{ person.assigned_tasks_count ?? 0 }}</p><p class="mt-1 text-xs text-slate-400">Assigned</p></div></div>
        <p class="mt-4 text-sm font-semibold text-violet-700 opacity-0 transition group-hover:opacity-100">View teammate →</p>
      </RouterLink>
    </div>
  </div>
</template>

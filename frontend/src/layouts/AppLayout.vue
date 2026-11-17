<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const menuOpen = ref(false)

onMounted(() => { void auth.loadProfile() })

async function signOut() {
  await auth.logout()
  await router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen bg-[#f5f6fa]">
    <aside class="fixed inset-y-0 left-0 z-30 hidden w-[248px] flex-col border-r border-slate-200/80 bg-white px-5 py-7 lg:flex">
      <RouterLink to="/" class="mb-12 flex items-center gap-3 px-2">
        <span class="grid size-10 place-items-center rounded-2xl bg-violet-600 text-lg font-black text-white shadow-lg shadow-violet-200">t</span>
        <span><span class="block text-lg font-extrabold tracking-tight text-slate-900">taskflow</span><span class="block text-[10px] font-semibold uppercase tracking-[.2em] text-slate-400">Work, in focus</span></span>
      </RouterLink>
      <p class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[.18em] text-slate-400">Workspace</p>
      <nav class="space-y-1">
        <RouterLink to="/" exact-active-class="!bg-violet-50 !text-violet-700" class="nav-link"><span class="nav-icon">⌂</span>Overview</RouterLink>
        <RouterLink to="/tasks" active-class="!bg-violet-50 !text-violet-700" class="nav-link"><span class="nav-icon">▤</span>My tasks</RouterLink>
        <RouterLink to="/users" active-class="!bg-violet-50 !text-violet-700" class="nav-link"><span class="nav-icon">◎</span>People</RouterLink>
      </nav>
      <div class="mt-auto rounded-2xl bg-slate-900 p-4 text-white">
        <div class="mb-3 grid size-9 place-items-center rounded-xl bg-white/10 text-violet-200">✦</div>
        <p class="text-sm font-bold">Make space for focus.</p><p class="mt-1 text-xs leading-5 text-slate-400">One clear next step is all it takes to keep moving.</p>
      </div>
    </aside>
    <div class="lg:pl-[248px]">
      <header class="sticky top-0 z-20 flex h-[76px] items-center justify-between border-b border-slate-200/70 bg-[#f5f6fa]/90 px-5 backdrop-blur-xl sm:px-8 lg:px-10">
        <div><p class="text-xs font-medium text-slate-400">Good to see you,</p><p class="text-sm font-bold text-slate-800">{{ auth.user?.name || 'Your workspace' }}</p></div>
        <div class="flex items-center gap-3">
          <RouterLink to="/tasks" class="hidden rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet-200 hover:bg-violet-700 sm:inline-flex">＋ New task</RouterLink>
          <div class="relative">
            <button class="flex items-center gap-2 rounded-full border border-slate-200 bg-white p-1.5 pr-3" @click="menuOpen = !menuOpen"><span class="grid size-8 place-items-center rounded-full bg-violet-100 text-xs font-bold text-violet-700">{{ (auth.user?.name || 'U').slice(0, 1).toUpperCase() }}</span><span class="hidden text-sm font-semibold text-slate-600 sm:block">{{ auth.user?.name?.split(' ')[0] }}</span><span class="text-xs text-slate-400">⌄</span></button>
            <div v-if="menuOpen" class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-100 bg-white p-2 shadow-lg"><p class="px-3 py-2 text-xs text-slate-400">{{ auth.user?.email }}</p><button class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50" @click="signOut">Sign out</button></div>
          </div>
        </div>
      </header>
      <nav class="flex gap-2 overflow-x-auto border-b border-slate-200/70 bg-white px-5 py-2 lg:hidden">
        <RouterLink to="/" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600" active-class="bg-violet-50 text-violet-700">Overview</RouterLink>
        <RouterLink to="/tasks" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600" active-class="bg-violet-50 text-violet-700">My tasks</RouterLink>
        <RouterLink to="/users" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600" active-class="bg-violet-50 text-violet-700">People</RouterLink>
      </nav>
      <main class="mx-auto max-w-[1440px] px-5 py-8 sm:px-8 lg:px-10"><RouterView /></main>
    </div>
  </div>
</template>

<style scoped>
.nav-link { display:flex; align-items:center; gap:12px; border-radius:12px; padding:11px 12px; color:#64748b; font-size:14px; font-weight:600; transition:all .15s }
.nav-link:hover { background:#f8fafc; color:#334155 }
.nav-icon { display:grid; width:24px; height:24px; place-items:center; font-size:18px }
</style>

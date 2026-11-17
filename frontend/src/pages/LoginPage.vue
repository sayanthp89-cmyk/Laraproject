<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { errorMessage } from '../services/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const busy = ref(false)

async function submit() {
  busy.value = true
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    await router.push('/')
  } catch (reason) { error.value = errorMessage(reason) }
  finally { busy.value = false }
}
</script>

<template>
  <main class="grid min-h-screen bg-white lg:grid-cols-[1.05fr_.95fr]">
    <section class="relative hidden overflow-hidden bg-[#18152b] p-14 text-white lg:flex lg:flex-col lg:justify-between">
      <div class="absolute -left-24 top-32 size-96 rounded-full bg-violet-600/25 blur-3xl"></div><div class="absolute -bottom-28 right-0 size-96 rounded-full bg-indigo-400/20 blur-3xl"></div>
      <RouterLink to="/" class="relative flex items-center gap-3 text-xl font-extrabold"><span class="grid size-10 place-items-center rounded-2xl bg-violet-500">t</span>taskflow</RouterLink>
      <div class="relative max-w-lg"><p class="mb-6 inline-flex rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold text-violet-200">A calmer way to get things done</p><h1 class="text-5xl font-bold leading-[1.1] tracking-tight">Big plans.<br><span class="text-violet-300">Small next steps.</span></h1><p class="mt-6 max-w-md text-base leading-7 text-slate-300">Bring your team and your to-dos together. Keep the important things moving, one task at a time.</p><div class="mt-10 flex -space-x-2"><span v-for="person in ['M','A','J','S']" :key="person" class="grid size-9 place-items-center rounded-full border-2 border-[#18152b] bg-violet-200 text-xs font-bold text-violet-800">{{ person }}</span><span class="ml-4 self-center pl-4 text-xs text-slate-400">A little more focus, every day.</span></div></div>
      <p class="relative text-xs text-slate-500">© 2026 TaskFlow. Make work feel lighter.</p>
    </section>
    <section class="flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-[390px]">
        <RouterLink to="/" class="mb-14 flex items-center gap-2 text-lg font-extrabold text-slate-900 lg:hidden"><span class="grid size-9 place-items-center rounded-xl bg-violet-600 text-white">t</span>taskflow</RouterLink>
        <p class="text-sm font-semibold text-violet-600">WELCOME BACK</p><h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Sign in to your space</h2><p class="mt-2 text-sm text-slate-500">Pick up right where you left off.</p>
        <form class="mt-9 space-y-5" @submit.prevent="submit">
          <div v-if="error" class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ error }}</div>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Email address</span><input v-model="email" type="email" autocomplete="email" required placeholder="you@company.com" class="w-full rounded-xl border border-slate-200 px-4 py-3.5 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Password</span><input v-model="password" type="password" autocomplete="current-password" required placeholder="Enter your password" class="w-full rounded-xl border border-slate-200 px-4 py-3.5 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <button :disabled="busy" class="w-full rounded-xl bg-violet-600 py-3.5 font-semibold text-white shadow-md shadow-violet-200 hover:bg-violet-700 disabled:opacity-60">{{ busy ? 'Signing in…' : 'Sign in' }}</button>
        </form>
        <p class="mt-7 text-center text-sm text-slate-500">New to TaskFlow? <RouterLink to="/register" class="font-semibold text-violet-700 hover:text-violet-900">Create an account</RouterLink></p>
      </div>
    </section>
  </main>
</template>

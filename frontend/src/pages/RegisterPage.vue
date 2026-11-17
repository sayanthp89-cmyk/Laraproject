<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { errorMessage } from '../services/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const confirmation = ref('')
const error = ref('')
const busy = ref(false)

async function submit() {
  error.value = ''
  if (password.value !== confirmation.value) { error.value = 'Your passwords do not match.'; return }
  busy.value = true
  try {
    await auth.register(name.value, email.value, password.value, confirmation.value)
    await router.push('/')
  } catch (reason) { error.value = errorMessage(reason) }
  finally { busy.value = false }
}
</script>

<template>
  <main class="grid min-h-screen bg-white lg:grid-cols-[1.05fr_.95fr]">
    <section class="hidden flex-col justify-between bg-[#18152b] p-14 text-white lg:flex">
      <RouterLink to="/" class="flex items-center gap-3 text-xl font-extrabold"><span class="grid size-10 place-items-center rounded-2xl bg-violet-500">t</span>taskflow</RouterLink>
      <div class="max-w-lg"><p class="mb-6 text-sm font-semibold text-violet-300">YOUR TEAM'S NEXT CHAPTER</p><h1 class="text-5xl font-bold leading-tight">A little more<br>clarity. <span class="text-violet-300">A lot more<br>momentum.</span></h1><p class="mt-6 max-w-md leading-7 text-slate-300">A shared space to see what matters, find your next step, and celebrate the progress along the way.</p></div>
      <p class="text-xs text-slate-500">© 2026 TaskFlow. Make work feel lighter.</p>
    </section>
    <section class="flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-[390px]"><RouterLink to="/" class="mb-10 flex items-center gap-2 text-lg font-extrabold text-slate-900 lg:hidden"><span class="grid size-9 place-items-center rounded-xl bg-violet-600 text-white">t</span>taskflow</RouterLink>
        <p class="text-sm font-semibold text-violet-600">GET STARTED</p><h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">Create your account</h2><p class="mt-2 text-sm text-slate-500">Your team's next step starts here.</p>
        <form class="mt-8 space-y-4" @submit.prevent="submit">
          <div v-if="error" class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ error }}</div>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Full name</span><input v-model="name" required maxlength="120" autocomplete="name" placeholder="Your name" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Email address</span><input v-model="email" required type="email" autocomplete="email" placeholder="you@company.com" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Password</span><input v-model="password" required type="password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Confirm password</span><input v-model="confirmation" required type="password" minlength="8" autocomplete="new-password" placeholder="Enter your password again" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
          <button :disabled="busy" class="w-full rounded-xl bg-violet-600 py-3.5 font-semibold text-white shadow-md shadow-violet-200 hover:bg-violet-700 disabled:opacity-60">{{ busy ? 'Creating account…' : 'Create account' }}</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-500">Already have an account? <RouterLink to="/login" class="font-semibold text-violet-700">Sign in</RouterLink></p>
      </div>
    </section>
  </main>
</template>

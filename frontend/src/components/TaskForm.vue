<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import type { Task, TaskInput, User } from '../types'

const props = defineProps<{ task: Task | null; users: User[]; saving: boolean }>()
const emit = defineEmits<{ submit: [input: TaskInput]; cancel: [] }>()
const errors = ref<Record<string, string>>({})
const form = ref<TaskInput>(emptyForm())

function emptyForm(): TaskInput {
  return { title: '', description: '', due_date: null, priority: 'medium', status: 'pending', assigned_to: null }
}

watch(() => props.task, (task) => {
  form.value = task ? {
    title: task.title, description: task.description || '', due_date: task.due_date,
    priority: task.priority, status: task.status, assigned_to: task.assigned_to,
  } : emptyForm()
  errors.value = {}
}, { immediate: true })

const titleCount = computed(() => form.value.title.length)

function submit() {
  errors.value = {}
  if (!form.value.title.trim()) errors.value.title = 'Give your task a title.'
  if (titleCount.value > 180) errors.value.title = 'Keep the title under 180 characters.'
  if (Object.keys(errors.value).length) return
  emit('submit', { ...form.value, title: form.value.title.trim() })
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="submit">
    <label class="block">
      <span class="mb-2 block text-sm font-semibold text-slate-700">Task title</span>
      <input v-model="form.title" maxlength="180" required placeholder="What needs to get done?" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-violet-400 focus:ring-4 focus:ring-violet-100" />
      <span class="mt-1 flex justify-between text-xs" :class="errors.title ? 'text-rose-600' : 'text-slate-400'"><span>{{ errors.title }}</span><span>{{ titleCount }}/180</span></span>
    </label>
    <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Description <span class="font-normal text-slate-400">· optional</span></span>
      <textarea v-model="form.description" rows="3" maxlength="10000" placeholder="Add a few helpful details…" class="w-full resize-none rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" />
    </label>
    <div class="grid gap-4 sm:grid-cols-2">
      <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Due date</span><input v-model="form.due_date" type="date" class="w-full rounded-xl border border-slate-200 px-3 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" /></label>
      <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Assignee</span><select v-model="form.assigned_to" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100"><option :value="null">Unassigned</option><option v-for="person in users" :key="person.id" :value="person.id">{{ person.name }}</option></select></label>
      <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Priority</span><select v-model="form.priority" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select></label>
      <label class="block"><span class="mb-2 block text-sm font-semibold text-slate-700">Status</span><select v-model="form.status" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100"><option value="pending">Pending</option><option value="in_progress">In progress</option><option value="completed">Completed</option></select></label>
    </div>
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
      <button type="button" class="rounded-xl px-4 py-2.5 font-semibold text-slate-600 hover:bg-slate-100" @click="emit('cancel')">Cancel</button>
      <button type="submit" :disabled="saving" class="rounded-xl bg-violet-600 px-5 py-2.5 font-semibold text-white shadow-sm shadow-violet-200 transition hover:bg-violet-700 disabled:opacity-60">{{ saving ? 'Saving…' : task ? 'Save changes' : 'Create task' }}</button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { errorMessage } from '../services/api'
import { createTask, deleteTask, fetchTasks, updateTask, updateTaskStatus } from '../services/tasks'
import { fetchUsers } from '../services/users'
import type { PageMeta, Task, TaskInput, User } from '../types'
import { useNotificationStore } from '../stores/notifications'
import ModalDialog from '../components/ModalDialog.vue'
import PaginationBar from '../components/PaginationBar.vue'
import TaskForm from '../components/TaskForm.vue'
import TaskTable from '../components/TaskTable.vue'

const auth = useAuthStore()
const notifications = useNotificationStore()
const tasks = ref<Task[]>([])
const users = ref<User[]>([])
const meta = ref<PageMeta | null>(null)
const loading = ref(true)
const saving = ref(false)
const formOpen = ref(false)
const editing = ref<Task | null>(null)
const removing = ref<Task | null>(null)
const filters = reactive({
  search: '', status: '', priority: '', due_date_from: '', due_date_to: '',
  sort_by: 'created_at', sort_direction: 'desc', page: 1,
})

async function loadTasks() {
  loading.value = true
  try {
    const response = await fetchTasks({ ...filters })
    tasks.value = response.data
    meta.value = response.meta
  } catch (error) { notifications.show(errorMessage(error), 'error') }
  finally { loading.value = false }
}

onMounted(async () => {
  try {
    const response = await fetchUsers()
    users.value = response.data
  } catch (error) { notifications.show(errorMessage(error), 'error') }
  await loadTasks()
})

function applyFilters() {
  filters.page = 1
  void loadTasks()
}

function openCreate() {
  editing.value = null
  formOpen.value = true
}

function openEdit(task: Task) {
  editing.value = task
  formOpen.value = true
}

async function saveTask(input: TaskInput) {
  saving.value = true
  try {
    if (editing.value) await updateTask(editing.value.id, input)
    else await createTask(input)
    formOpen.value = false
    notifications.show(editing.value ? 'Your task has been updated.' : 'Your new task is ready.')
    await loadTasks()
  } catch (error) { notifications.show(errorMessage(error), 'error') }
  finally { saving.value = false }
}

async function confirmDelete() {
  if (!removing.value) return
  try {
    await deleteTask(removing.value.id)
    removing.value = null
    notifications.show('Task deleted.')
    await loadTasks()
  } catch (error) { notifications.show(errorMessage(error), 'error') }
}

async function changeStatus(task: Task, status: Task['status']) {
  try {
    await updateTaskStatus(task.id, status)
    notifications.show('Task status updated.')
    await loadTasks()
  } catch (error) {
    notifications.show(errorMessage(error), 'error')
    await loadTasks()
  }
}

function changePage(page: number) {
  filters.page = page
  void loadTasks()
}
</script>

<template>
  <div>
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4">
      <div><p class="text-sm font-semibold text-violet-600">THE WORK, AT A GLANCE</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Tasks</h1><p class="mt-2 text-slate-500">Make a little progress on what matters.</p></div>
      <button class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet-200 hover:bg-violet-700" @click="openCreate">＋ New task</button>
    </div>
    <section class="mb-5 rounded-2xl border border-slate-200/70 bg-white p-4 shadow-sm shadow-slate-100/70 sm:p-5">
      <form class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(190px,1.4fr)_repeat(4,minmax(130px,1fr))_auto]" @submit.prevent="applyFilters">
        <input v-model="filters.search" placeholder="Search task titles…" class="rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-100" />
        <select v-model="filters.status" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-600 outline-none focus:border-violet-400"><option value="">All statuses</option><option value="pending">Pending</option><option value="in_progress">In progress</option><option value="completed">Completed</option></select>
        <select v-model="filters.priority" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-600 outline-none focus:border-violet-400"><option value="">All priorities</option><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
        <input v-model="filters.due_date_from" type="date" aria-label="Due date from" class="min-w-0 rounded-xl border border-slate-200 px-2.5 py-2.5 text-sm text-slate-600 outline-none focus:border-violet-400" />
        <input v-model="filters.due_date_to" type="date" aria-label="Due date to" class="min-w-0 rounded-xl border border-slate-200 px-2.5 py-2.5 text-sm text-slate-600 outline-none focus:border-violet-400" />
        <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Filter</button>
      </form>
      <div class="mt-3 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3">
        <p class="text-xs text-slate-400">Filter by due date range · Sort results</p>
        <div class="flex items-center gap-2 text-xs text-slate-500"><span>Sort by</span><select v-model="filters.sort_by" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5" @change="applyFilters"><option value="created_at">Recently added</option><option value="due_date">Due date</option><option value="priority">Priority</option><option value="status">Status</option><option value="title">Title</option></select><button class="rounded-lg border border-slate-200 px-2 py-1.5" aria-label="Toggle sort direction" @click="filters.sort_direction = filters.sort_direction === 'asc' ? 'desc' : 'asc'; applyFilters()">{{ filters.sort_direction === 'asc' ? '↑' : '↓' }}</button></div>
      </div>
    </section>
    <section class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-sm shadow-slate-100/70">
      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div><h2 class="font-bold text-slate-800">Your task list</h2><p class="mt-1 text-xs text-slate-400">Tasks you created or have been assigned</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ meta?.total ?? 0 }} tasks</span></div>
      <div v-if="loading" class="space-y-3 p-5"><div v-for="n in 5" :key="n" class="h-14 animate-pulse rounded-xl bg-slate-100"></div></div>
      <TaskTable v-else :tasks="tasks" :current-user-id="auth.user?.id" @edit="openEdit" @remove="removing = $event" @status-change="changeStatus" />
      <PaginationBar :meta="meta" @change="changePage" />
    </section>
    <ModalDialog :open="formOpen" :title="editing ? 'Edit task' : 'Create a task'" description="A clear title makes a great place to start." @close="formOpen = false">
      <TaskForm :task="editing" :users="users" :saving="saving" @submit="saveTask" @cancel="formOpen = false" />
    </ModalDialog>
    <ModalDialog :open="Boolean(removing)" title="Delete this task?" description="This action can't be undone. The task will be removed from everyone's workspace." @close="removing = null">
      <div class="flex justify-end gap-3"><button class="rounded-xl px-4 py-2.5 font-semibold text-slate-600 hover:bg-slate-100" @click="removing = null">Keep task</button><button class="rounded-xl bg-rose-600 px-4 py-2.5 font-semibold text-white hover:bg-rose-700" @click="confirmDelete">Delete task</button></div>
    </ModalDialog>
  </div>
</template>

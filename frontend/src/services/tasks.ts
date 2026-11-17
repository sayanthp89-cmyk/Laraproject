import api from './api'
import type { Paginated, Task, TaskInput } from '../types'

export interface TaskFilters {
  search?: string
  status?: string
  priority?: string
  due_date_from?: string
  due_date_to?: string
  sort_by?: string
  sort_direction?: string
  page?: number
}

export async function fetchTasks(filters: TaskFilters = {}) {
  const { data } = await api.get<Paginated<Task>>('/tasks', { params: { per_page: 10, ...filters } })
  return data
}

export async function createTask(input: TaskInput) {
  const { data } = await api.post<{ data: Task }>('/tasks', input)
  return data.data
}

export async function updateTask(id: number, input: TaskInput) {
  const { data } = await api.put<{ data: Task }>(`/tasks/${id}`, input)
  return data.data
}

export async function updateTaskStatus(id: number, status: Task['status']) {
  const { data } = await api.patch<{ data: Task }>(`/tasks/${id}/status`, { status })
  return data.data
}

export async function deleteTask(id: number) {
  await api.delete(`/tasks/${id}`)
}

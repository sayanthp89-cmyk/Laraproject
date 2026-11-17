import api from './api'
import type { Paginated, Task, User } from '../types'

export async function fetchUsers(search = '') {
  const { data } = await api.get<Paginated<User>>('/users', { params: { search, per_page: 50 } })
  return data
}

export async function fetchUser(id: number) {
  const { data } = await api.get<{ user: User; recent_tasks: Task[] }>(`/users/${id}`)
  return data
}

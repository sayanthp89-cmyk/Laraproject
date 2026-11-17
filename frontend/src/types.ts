export type TaskStatus = 'pending' | 'in_progress' | 'completed'
export type TaskPriority = 'low' | 'medium' | 'high'

export interface User {
  id: number
  name: string
  email: string
  created_at?: string
  created_tasks_count?: number
  assigned_tasks_count?: number
}

export interface Task {
  id: number
  title: string
  description: string | null
  due_date: string | null
  priority: TaskPriority
  status: TaskStatus
  assigned_to: number | null
  created_by: number
  assignee?: Pick<User, 'id' | 'name' | 'email'> | null
  creator?: Pick<User, 'id' | 'name'>
  created_at: string
  updated_at: string
  is_overdue: boolean
}

export interface PageMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface Paginated<T> {
  data: T[]
  meta: PageMeta
  links: { first: string | null; last: string | null; prev: string | null; next: string | null }
}

export interface TaskInput {
  title: string
  description: string
  due_date: string | null
  priority: TaskPriority
  status: TaskStatus
  assigned_to: number | null
}

import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { User } from '../types'

interface AuthResponse { user: User; token: string }

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref(localStorage.getItem('taskflow_token'))
  const isAuthenticated = ref(Boolean(token.value))

  function acceptSession(session: AuthResponse) {
    user.value = session.user
    token.value = session.token
    isAuthenticated.value = true
    localStorage.setItem('taskflow_token', session.token)
  }

  async function login(email: string, password: string) {
    const { data } = await api.post<AuthResponse>('/login', { email, password })
    acceptSession(data)
  }

  async function register(name: string, email: string, password: string, passwordConfirmation: string) {
    const { data } = await api.post<AuthResponse>('/register', {
      name, email, password, password_confirmation: passwordConfirmation,
    })
    acceptSession(data)
  }

  async function loadProfile() {
    if (!token.value) return
    const { data } = await api.get<{ data: User }>('/me')
    user.value = data.data
    isAuthenticated.value = true
  }

  async function logout() {
    try {
      if (token.value) await api.post('/logout')
    } finally {
      user.value = null
      token.value = null
      isAuthenticated.value = false
      localStorage.removeItem('taskflow_token')
    }
  }

  return { user, token, isAuthenticated, login, register, loadProfile, logout }
})

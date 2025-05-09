import { ref } from 'vue'

interface User {
  id: number
  name: string
}

interface AuthResponse {
  data: {
    token: string
    token_type: string
    expires_in: number
    name: string
    id: string
  }
  user: User
}

const token = ref<string | null>(
  document.cookie.replace(/(?:(?:^|.*;\s*)token\s*\=\s*([^;]*).*$)|^.*$/, '$1') || null,
)
const name = ref<string | null>(
  document.cookie.replace(/(?:(?:^|.*;\s*)name\s*\=\s*([^;]*).*$)|^.*$/, '$1') || null,
)
const id = ref<number | null>(
  document.cookie.replace(/(?:(?:^|.*;\s*)id\s*\=\s*([^;]*).*$)|^.*$/, '$1')
    ? parseInt(document.cookie.replace(/(?:(?:^|.*;\s*)id\s*\=\s*([^;]*).*$)|^.*$/, '$1'), 10) ||
        null
    : null,
)

export const useAuth = () => {
  const setAuth = (authData: AuthResponse) => {
    token.value = authData.data.token
    name.value = authData.user.name
    id.value = authData.user.id

    // Set cookie to expire in 1 hour
    const expires = new Date(Date.now() + authData.data.expires_in * 1000).toUTCString()
    document.cookie = `token=${authData.data.token}; expires=${expires}; path=/`
    document.cookie = `name=${authData.user.name}; expires=${expires}; path=/`
    document.cookie = `id=${authData.user.id}; expires=${expires}; path=/`
  }

  const logout = () => {
    token.value = null
    name.value = null
    id.value = null
    document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
    document.cookie = 'name=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
    document.cookie = 'id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
  }

  const isAuthenticated = () => !!token.value

  return {
    token,
    name,
    id,
    setAuth,
    logout,
    isAuthenticated,
  }
}

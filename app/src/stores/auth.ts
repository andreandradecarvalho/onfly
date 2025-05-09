import { ref } from 'vue'

interface User {
  id: number;
  name: string;
}

interface AuthResponse {
  data: {
    token: string;
    token_type: string;
    expires_in: number;
  };
  user: User;
}

const token = ref<string | null>(document.cookie.replace(/(?:(?:^|.*;\s*)token\s*\=\s*([^;]*).*$)|^.*$/, "$1") || null);
const user = ref<User | null>(null);

export const useAuth = () => {
  const setAuth = (authData: AuthResponse) => {
    token.value = authData.data.token;
    user.value = authData.user;

    // Set cookie to expire in 1 hour
    const expires = new Date(Date.now() + authData.data.expires_in * 1000).toUTCString();
    document.cookie = `token=${authData.data.token}; expires=${expires}; path=/`;
  };

  const logout = () => {
    token.value = null;
    user.value = null;
    document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
  };

  const isAuthenticated = () => !!token.value;

  return {
    token,
    user,
    setAuth,
    logout,
    isAuthenticated
  };
};

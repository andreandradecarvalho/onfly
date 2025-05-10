import { ref } from 'vue'

// Define the structure of the User object based on your API response
interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  is_super_admin: boolean;
  is_admin: boolean;
  company_name: string;
  company_id: number;
  position_name: string;
  position_id: number;
}

interface AuthResponse {
  data: {
    token: string;
    token_type: string;
    expires_in: number;
    user: User; // User object is nested within data
  };
  // No user property directly under AuthResponse root
}

// Helper function to get cookie by name
const getCookie = (name: string): string | null => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop()?.split(';').shift() || null;
  return null;
}

// Initialize token from cookie
const token = ref<string | null>(getCookie('token'))

// Initialize userData from cookie
const initialUserDataString = getCookie('userData')
const userData = ref<User | null>(initialUserDataString ? JSON.parse(initialUserDataString) : null)

export const useAuth = () => {
  const setAuth = (authData: AuthResponse) => {
    token.value = authData.data.token
    userData.value = authData.data.user // Access user from authData.data.user

    const expires = new Date(Date.now() + authData.data.expires_in * 1000).toUTCString()
    
    // Store token in cookie
    document.cookie = `token=${authData.data.token}; expires=${expires}; path=/; SameSite=Lax`
    
    // Store entire user object in another cookie
    if (authData.data.user) {
      document.cookie = `userData=${JSON.stringify(authData.data.user)}; expires=${expires}; path=/; SameSite=Lax`
    } else {
      // Clear userData cookie if no user data is provided
      document.cookie = 'userData=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
    }
  }

  const logout = () => {
    token.value = null
    userData.value = null // Clear the user data state
    
    // Clear cookies
    document.cookie = 'token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
    document.cookie = 'userData=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'
  }

  const isAuthenticated = () => !!token.value

  return {
    token,
    userData, // Expose the full user data object
    setAuth,
    logout,
    isAuthenticated,
  }
}

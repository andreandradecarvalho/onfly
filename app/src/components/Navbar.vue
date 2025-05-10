<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useAuth } from '../stores/auth';
import { useRouter } from 'vue-router';
const router = useRouter();
const isMenuOpen = ref(false);
const auth = useAuth();

const logout = async () => {
  try {
    console.log(auth.token.value);

    await axios.post(`${import.meta.env.VITE_API_URL}/auth/logout`, {}, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`
      }
    });

    // Clear authentication state
    auth.logout();

    // Redirect to login page
    router.push('/');
  } catch (err: any) {
    console.error('Logout failed:', err.response?.data || err.message);
  }
}

</script>

<template>
  <nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <router-link to="/" v-if="!auth.isAuthenticated()" class="text-2xl font-bold text-blue-600">Onfly</router-link>
          <router-link to="/dashboard" v-if="auth.isAuthenticated()" class="text-2xl font-bold text-blue-600">Onfly</router-link>
        </div>

        <!-- Desktop Menu -->
        <div class="hidden md:flex items-center space-x-8">
          <router-link to="/login" class="text-gray-700 hover:text-blue-600" v-if="!auth.isAuthenticated()">Login</router-link>
          <router-link to="/register" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700" v-if="!auth.isAuthenticated()">Cadastrar</router-link>
          <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" v-if="auth.isAuthenticated()">Logout</button>
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center">
          <button @click="isMenuOpen = !isMenuOpen" class="text-gray-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="isMenuOpen" class="md:hidden">
      <div class="px-2 pt-2 pb-3 space-y-1">
        <router-link to="/login" class="block px-3 py-2 text-gray-700 hover:text-blue-600" v-if="!auth.isAuthenticated()">Login</router-link>
        <router-link to="/register" class="block px-3 py-2 text-gray-700 hover:text-blue-600" v-if="!auth.isAuthenticated()">Cadastrar</router-link>
        <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" v-if="auth.isAuthenticated()">Logout</button>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuth } from '../stores/auth';

const router = useRouter();
const auth = useAuth();
const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);

const login = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/auth/login`, {
      email: email.value,
      password: password.value
    });

    auth.setAuth(response.data);
    router.push('/dashboard');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Ocorreu um erro ao tentar realizar o login';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Entre na sua conta</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          ou
          <router-link to="/" class="font-medium text-blue-600 hover:text-blue-500">voltar a home</router-link>
        </p>
      </div>
      <div v-if="error" class="rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{ error }}</p>
          </div>
        </div>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="login">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Qual é o seu email?</label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Qual é a senha?</label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <div>
          <button type="submit" :disabled="loading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
            <span v-if="loading">Logando...</span>
            <span v-else>Logar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

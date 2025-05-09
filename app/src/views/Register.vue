<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const errors = ref<Record<string, string[]>>({});
const loading = ref(false);
const successMessage = ref('');

const register = async () => {
  loading.value = true;
  errors.value = {};
  successMessage.value = '';

  try {
    const response = await axios.post(`${import.meta.env.VITE_API_URL}/auth/register`, {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    });

    if (response.status === 201) {
      successMessage.value = 'Cadastro realizado com sucesso! Redirecionando para login...';
      setTimeout(() => {
        router.push('/login');
      }, 2000);
    }
  } catch (err: any) {
    if (err.response?.data?.erros) {
      errors.value = err.response.data.erros;
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Realizar cadastro</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          ou
          <router-link to="/" class="font-medium text-blue-600 hover:text-blue-500">
            voltar para a home
          </router-link>
        </p>
      </div>
      <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
          </div>
        </div>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="register">
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Qual o seu nome?</label>
            <input
              id="name"
              v-model="name"
              type="text"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
            <div v-if="errors.name" class="mt-1 text-sm text-red-600">
              {{ errors.name[0] }}
            </div>
          </div>
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Qual o seu e-mail?</label>
            <input
              id="email"
              v-model="email"
              type="email"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
            <div v-if="errors.email" class="mt-1 text-sm text-red-600">
              {{ errors.email[0] }}
            </div>
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
            <div v-if="errors.password" class="mt-1 text-sm text-red-600">
              {{ errors.password[0] }}
            </div>
          </div>
          <div>
            <label for="password-confirmation" class="block text-sm font-medium text-gray-700">Qual é a senha mesmo?</label>
            <input
              id="password-confirmation"
              v-model="passwordConfirmation"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            />
            <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
              {{ errors.password_confirmation[0] }}
            </div>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            <span v-if="loading">Cadastrando...</span>
            <span v-else>Cadastrar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

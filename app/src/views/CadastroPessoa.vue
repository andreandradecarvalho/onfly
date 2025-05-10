<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'

const auth = useAuth()
const router = useRouter()

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

const pessoa = ref({
  nome: '',
  email: '',
  empresa: '',
  cargo: '',
  endereco: ''
})

const submitForm = () => {
  // TODO: Implement actual submission logic
  console.log('Pessoa cadastrada:', pessoa.value)
  // You would typically call an API here to save the person data
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Cadastro de Pessoas</h1>
    <form @submit.prevent="submitForm" class="max-w-lg mx-auto">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="nome">
          Nome
        </label>
        <input 
          v-model="pessoa.nome" 
          type="text" 
          id="nome" 
          required 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
          Email
        </label>
        <input 
          v-model="pessoa.email" 
          type="email" 
          id="email" 
          required 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="empresa">
          Empresa
        </label>
        <input 
          v-model="pessoa.empresa" 
          type="text" 
          id="empresa" 
          required 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="cargo">
          Cargo
        </label>
        <input 
          v-model="pessoa.cargo" 
          type="text" 
          id="cargo" 
          required 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="endereco">
          Endereço
        </label>
        <input 
          v-model="pessoa.endereco" 
          type="text" 
          id="endereco" 
          required 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
      </div>
      <div class="flex items-center justify-between">
        <button 
          type="submit" 
          class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        >
          Cadastrar Pessoa
        </button>
      </div>
    </form>
  </div>
</template>

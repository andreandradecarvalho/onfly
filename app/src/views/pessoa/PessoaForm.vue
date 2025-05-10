<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'

const auth = useAuth()
const router = useRouter()
const route = useRoute()

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

// Determine if this is a new or edit form
const isNewForm = route.name === 'PessoaNew'

// Reactive state for the pessoa
const pessoa = ref({
  id: null as number | null,
  nome: '',
  email: '',
  empresa: '',
  cargo: '',
  endereco: ''
})

// If editing, load existing pessoa data
onMounted(() => {
  if (!isNewForm) {
    // TODO: Replace with actual API call to fetch pessoa by ID
    const mockPessoa = {
      id: Number(route.params.id),
      nome: 'João Silva',
      email: 'joao.silva@exemplo.com',
      empresa: 'Empresa Exemplo 1',
      cargo: 'Gerente',
      endereco: 'Rua A, 123'
    }
    pessoa.value = mockPessoa
  }
})

const submitForm = () => {
  // TODO: Implement actual submission logic (create or update)
  if (isNewForm) {
    console.log('Criando nova pessoa:', pessoa.value)
    // API call to create new pessoa
  } else {
    console.log('Atualizando pessoa:', pessoa.value)
    // API call to update existing pessoa
  }
  
  // Navigate back to pessoas list
  router.push('/pessoas')
}

const cancelForm = () => {
  // Navigate back to pessoas list
  router.push('/pessoas')
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">
      {{ isNewForm ? 'Nova Pessoa' : 'Editar Pessoa' }}
    </h1>
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
      <div class="flex items-center justify-between space-x-4">
        <button 
          type="submit" 
          class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        >
          {{ isNewForm ? 'Criar Pessoa' : 'Atualizar Pessoa' }}
        </button>
        <button 
          type="button"
          @click="cancelForm"
          class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        >
          Cancelar
        </button>
      </div>
    </form>
  </div>
</template>

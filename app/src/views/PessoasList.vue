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

// Define interface for pessoa
interface Pessoa {
  id: number
  nome: string
  email: string
  empresa: string
  cargo: string
  endereco: string
}

// Mock data - replace with actual API calls
const pessoas = ref<Pessoa[]>([
  { 
    id: 1, 
    nome: 'João Silva', 
    email: 'joao.silva@exemplo.com', 
    empresa: 'Empresa Exemplo 1', 
    cargo: 'Gerente', 
    endereco: 'Rua A, 123' 
  },
  { 
    id: 2, 
    nome: 'Maria Souza', 
    email: 'maria.souza@exemplo.com', 
    empresa: 'Empresa Exemplo 2', 
    cargo: 'Analista', 
    endereco: 'Avenida B, 456' 
  }
])

const deletePessoa = (id: number) => {
  // TODO: Implement actual delete API call
  pessoas.value = pessoas.value.filter(pessoa => pessoa.id !== id)
  console.log(`Deletando pessoa com ID: ${id}`)
}

const editPessoa = (pessoa: Pessoa) => {
  // Navigate to edit page
  router.push({ 
    name: 'PessoaEdit', 
    params: { id: pessoa.id } 
  })
}

const createPessoa = () => {
  // Navigate to create page
  router.push('/pessoas/novo')
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Pessoas</h1>
      <button 
        @click="createPessoa"
        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
      >
        Nova Pessoa
      </button>
    </div>

    <div v-if="pessoas.length === 0" class="text-center text-gray-500">
      Nenhuma pessoa cadastrada.
    </div>

    <div v-else class="grid gap-4">
      <div 
        v-for="pessoa in pessoas" 
        :key="pessoa.id" 
        class="bg-white shadow rounded-lg p-6 flex justify-between items-center"
      >
        <div>
          <h2 class="text-xl font-semibold">{{ pessoa.nome }}</h2>
          <p class="text-gray-600">Email: {{ pessoa.email }}</p>
          <p class="text-gray-600">Empresa: {{ pessoa.empresa }}</p>
          <p class="text-gray-600">Cargo: {{ pessoa.cargo }}</p>
        </div>
        <div class="flex space-x-2">
          <button 
            @click="editPessoa(pessoa)"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
          >
            Editar
          </button>
          <button 
            @click="deletePessoa(pessoa.id)"
            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
          >
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

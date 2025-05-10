<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '../../components/Navbar.vue'
import Footer from '../../components/Footer.vue'

const auth = useAuth()
const router = useRouter()

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

// Define interface for empresa
interface Empresa {
  id: number
  nome: string
  cnpj: string
  endereco: string
  email: string
  responsavel: string
}

// Mock data - replace with actual API calls
const empresas = ref<Empresa[]>([
  {
    id: 1,
    nome: 'Empresa Exemplo 1',
    cnpj: '12.345.678/0001-90',
    endereco: 'Rua Exemplo, 123',
    email: 'empresa1@exemplo.com',
    responsavel: 'João Silva'
  },
  {
    id: 2,
    nome: 'Empresa Exemplo 2',
    cnpj: '98.765.432/0001-10',
    endereco: 'Avenida Teste, 456',
    email: 'empresa2@exemplo.com',
    responsavel: 'Maria Souza'
  }
])

const deleteEmpresa = (id: number) => {
  // TODO: Implement actual delete API call
  empresas.value = empresas.value.filter(empresa => empresa.id !== id)
  console.log(`Deletando empresa com ID: ${id}`)
}

const editEmpresa = (empresa: Empresa) => {
  // Navigate to edit page
  router.push({
    name: 'EmpresaEdit',
    params: { id: empresa.id }
  })
}

const createEmpresa = () => {
  // Navigate to create page
  router.push('/empresas/novo')
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
      <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Empresas</h1>
          <button @click="createEmpresa" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Nova Empresa</button>
        </div>
        <div v-if="empresas.length === 0" class="text-center text-gray-500">Nenhuma empresa cadastrada.</div>
        <div v-else class="grid gap-4">
          <div v-for="empresa in empresas" :key="empresa.id" class="bg-white shadow rounded-lg p-6 flex justify-between items-center">
            <div>
              <h2 class="text-xl font-semibold">{{ empresa.nome }}</h2>
              <p class="text-gray-600">CNPJ: {{ empresa.cnpj }}</p>
              <p class="text-gray-600">Email: {{ empresa.email }}</p>
            </div>
            <div class="flex space-x-2">
              <button @click="editEmpresa(empresa)" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Editar</button>
              <button @click="deleteEmpresa(empresa.id)" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">Excluir</button>
            </div>
          </div>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

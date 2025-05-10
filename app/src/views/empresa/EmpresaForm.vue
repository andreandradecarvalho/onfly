<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '../../components/Navbar.vue'

const auth = useAuth()
const router = useRouter()
const route = useRoute()

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

// Determine if this is a new or edit form
const isNewForm = route.name === 'EmpresaNew'

// Reactive state for the empresa
const empresa = ref({
  id: null as number | null,
  nome: '',
  cnpj: '',
  endereco: '',
  email: '',
  responsavel: ''
})

// If editing, load existing empresa data
onMounted(() => {
  if (!isNewForm) {
    // TODO: Replace with actual API call to fetch empresa by ID
    const mockEmpresa = {
      id: Number(route.params.id),
      nome: 'Empresa Exemplo',
      cnpj: '12.345.678/0001-90',
      endereco: 'Rua Exemplo, 123',
      email: 'empresa@exemplo.com',
      responsavel: 'João Silva'
    }
    empresa.value = mockEmpresa
  }
})

const submitForm = () => {
  // TODO: Implement actual submission logic (create or update)
  if (isNewForm) {
    console.log('Criando nova empresa:', empresa.value)
    // API call to create new empresa
  } else {
    console.log('Atualizando empresa:', empresa.value)
    // API call to update existing empresa
  }

  // Navigate back to empresas list
  router.push('/empresas')
}

const cancelForm = () => {
  // Navigate back to empresas list
  router.push('/empresas')
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
      <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
          <div class="mb-6 flex space-x-4">
            <router-link to="/empresas" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Empresas</router-link>
            <router-link to="/pessoas" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Pessoas</router-link>
          </div>
          <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <form @submit.prevent="submitForm" class="mx-auto">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="nome"> Nome da Empresa</label>
                <input v-model="empresa.nome" type="text" id="nome" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="cnpj">CNPJ</label>
                <input v-model="empresa.cnpj" type="text" id="cnpj" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="endereco">Endereço </label>
                <input v-model="empresa.endereco" type="text" id="endereco" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email </label>
                <input v-model="empresa.email" type="email" id="email" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" >
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1" for="responsavel">Responsável</label>
                <input v-model="empresa.responsavel" type="text" id="responsavel" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" >
              </div>
              <div class="flex items-center justify-between space-x-4">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" >{{ isNewForm ? 'Criar Empresa' : 'Atualizar Empresa' }}</button>
                <button type="button" @click="cancelForm" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancelar</button>
              </div>
              </div>
            </form>
        </div>
      </main>
    <Footer />
  </div>
</template>

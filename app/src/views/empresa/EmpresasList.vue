<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
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
  name: string
  document: string
  address: string
  email: string
  responsibility: string
  created_at: string
  updated_at: string
}

const error = ref('')
const loading = ref(true)
const empresas = ref<Empresa[]>([])
const isSuperAdmin = ref(false)

onMounted(async () => {
  try {
    error.value = ''
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/companies`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })

    empresas.value = response.data.data
    isSuperAdmin.value = response.data.is_super_admin
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Ocorreu um erro ao buscar as empresas';
    error.value = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || errorMessage;
  } finally {
    loading.value = false
  }
})

const deleteEmpresa = async (id: number) => {
  try {
    loading.value = true;
    await axios.delete(`${import.meta.env.VITE_API_URL}/companies/${id}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`
      }
    });
    empresas.value = empresas.value.filter(empresa => empresa.id !== id);
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao excluir empresa';
    error.value = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || errorMessage;
  } finally {
    loading.value = false;
  }
}

const editEmpresa = (empresa: Empresa) => {
  // Navigate to edit page
  router.push({
    name: 'EmpresaEdit',
    params: { id: empresa.id.toString() }
  })
}

const createEmpresa = () => {
  // Navigate to create page
  router.push({ name: 'EmpresaCriar' })
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
      <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Empresas</h1>
          <button
            @click="createEmpresa"
            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
          >
            Nova Empresa
          </button>
        </div>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div v-if="loading" class="text-center py-8 text-gray-500">Carregando empresas...</div>
          <div v-else-if="error" class="text-center py-8 text-red-500">{{ error }}</div>
          <table v-else-if="empresas.length > 0" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsável</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado em</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="empresa in empresas" :key="empresa.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.document }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.responsibility }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ new Date(empresa.created_at).toLocaleDateString() }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button @click="editEmpresa(empresa)" class="text-blue-600 hover:text-blue-900">Editar</button>
                    <button @click="deleteEmpresa(empresa.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div v-else class="text-center py-8">
            <p class="text-gray-500">Nenhuma empresa cadastrada.</p>
          </div>
        </div>
      </div>
    </main>
    <Footer />
  </div>
</template>

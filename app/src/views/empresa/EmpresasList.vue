<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuth } from '@/stores/auth'
import Navbar from '../../components/Navbar.vue'
import Footer from '../../components/Footer.vue'

const error = ref<string | null>(null)
const loading = ref(false)
const auth = useAuth()
const router = useRouter()

// State for delete confirmation modal
const showDeleteModal = ref(false)
const empresaIdToDelete = ref<number | null>(null)

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

const empresas = ref<Empresa[]>([])
const isSuperAdmin = ref(false)

// Fetch empresas on component mount
onMounted(async () => {
  if (auth.isAuthenticated()) {
    await fetchEmpresas()
  }
})

const fetchEmpresas = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/companies`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    empresas.value = response.data.data // Assuming API returns data in a 'data' property
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar empresas'
    error.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      errorMessage
  } finally {
    loading.value = false
  }
}

const editEmpresa = (empresa: Empresa) => {
  // Navigate to edit page
  router.push({
    name: 'EmpresaEdit',
    params: { id: empresa.id.toString() },
  })
}

const createEmpresa = () => {
  // Navigate to create page
  router.push({ name: 'EmpresaNew' })
}

// Functions for delete confirmation modal
const promptDeleteEmpresa = (id: number) => {
  empresaIdToDelete.value = id
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  empresaIdToDelete.value = null
}

const confirmDelete = async () => {
  if (empresaIdToDelete.value === null) return

  loading.value = true
  error.value = null
  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/companies/${empresaIdToDelete.value}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    empresas.value = empresas.value.filter((empresa) => empresa.id !== empresaIdToDelete.value)
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao excluir empresa'
    error.value =
      (err as { response?: { data?: { message?: string } } })?.response?.data?.message ||
      errorMessage
  } finally {
    loading.value = false
    cancelDelete() // Hide modal and reset ID
  }
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
        <div class="bg-white shadow overflow-auto sm:rounded-lg">
          <div v-if="loading" class="text-center py-8 text-gray-500">Carregando empresas...</div>
          <div v-else-if="error" class="text-center py-8 text-red-500">{{ error }}</div>
          <table v-else-if="empresas.length > 0" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  ID
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Nome
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  CNPJ
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Email
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Responsável
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Criado em
                </th>
                <th
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                  Ações
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="empresa in empresas" :key="empresa.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ empresa.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ empresa.name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ empresa.document }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ empresa.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ empresa.responsibility }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ new Date(empresa.created_at).toLocaleDateString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button
                      @click="editEmpresa(empresa)"
                      class="text-indigo-600 hover:text-indigo-900 mr-2"
                    >
                      Editar
                    </button>
                    <button
                      @click="promptDeleteEmpresa(empresa.id)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Excluir
                    </button>
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

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      @click.self="cancelDelete"
    >
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm mx-auto">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Confirmar Exclusão</h3>
        <p class="text-sm text-gray-500 mb-6">
          Tem certeza de que deseja excluir esta empresa? Esta ação não pode ser desfeita.
        </p>
        <div class="flex justify-end space-x-3">
          <button
            @click="cancelDelete"
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Cancelar
          </button>
          <button
            @click="confirmDelete"
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Excluir
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

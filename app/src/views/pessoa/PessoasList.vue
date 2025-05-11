<script setup lang="ts">
import { ref, onMounted, watch } from 'vue' // Added onMounted and watch
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import axios from 'axios'

// import axios from 'axios' // Uncomment if you implement API calls

const auth = useAuth()
const router = useRouter()

// Loading and error states
const loading = ref(false) // Added loading state
const error = ref<string | null>(null) // Added error state

// State for delete confirmation modal
const showDeleteModal = ref(false)
const pessoaIdToDelete = ref<number | null>(null)

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

// Define interface for pessoa
interface Pessoa {
  id: number
  nome: string
  email: string
  empresa?: string // Made optional as it might not always be present or needed in list view
  cargo?: string // Made optional
  // endereco: string // Consider if needed in the main list or only in detail/edit view
}

const pessoas = ref<Pessoa[]>([])

// Estados para os filtros
const filtroEmpresa = ref('')
const filtroTipo = ref({
  superAdmin: false,
  admin: false,
})

// Mock data fetching function (replace with actual API call)
const fetchPessoas = async () => {
  loading.value = true
  error.value = null
  try {
    // Replace with actual API call: e.g., await axios.get(...)
    const params: Record<string, any> = {}
    if (filtroEmpresa.value) {
      params.company_name = filtroEmpresa.value
    }
    if (filtroTipo.value.superAdmin) {
      params.is_super_admin = true
    }
    if (filtroTipo.value.admin) {
      params.is_admin = true
    }

    const response = await axios.get(`${import.meta.env.VITE_API_URL}/users`, {
      params, // Adiciona os parâmetros de filtro na requisição
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    pessoas.value = response.data
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar pessoas'
    error.value = errorMessage
  } finally {
    loading.value = false
  }
}

// Fetch pessoas on component mount
onMounted(async () => {
  if (auth.isAuthenticated()) {
    await fetchPessoas()
  }
})

// Watchers para aplicar os filtros automaticamente
watch([filtroEmpresa, filtroTipo], fetchPessoas, { deep: true })

const editPessoa = (pessoa: Pessoa) => {
  router.push({ name: 'PessoaEdit', params: { id: pessoa.id.toString() } })
}

const createPessoa = () => {
  router.push('/pessoas/novo')
}

// Functions for delete confirmation modal
const promptDeletePessoa = (id: number) => {
  pessoaIdToDelete.value = id
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  pessoaIdToDelete.value = null
}

const confirmDeletePessoa = async () => {
  if (pessoaIdToDelete.value === null) return

  loading.value = true // Or a specific deleting state
  error.value = null
  try {
    await axios.delete(`${import.meta.env.VITE_API_URL}/users/${pessoaIdToDelete.value}`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    pessoas.value = pessoas.value.filter((pessoa) => pessoa.id !== pessoaIdToDelete.value)
    // Optionally, re-fetch or show a success message
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao excluir pessoa'
    error.value = errorMessage
    // error.value = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || errorMessage
  } finally {
    loading.value = false
    cancelDelete() // Hide modal and reset ID
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Assuming you might want Navbar and Footer like in EmpresasList -->
    <Navbar />
    <main class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pessoas</h1>
        <button
          @click="createPessoa"
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
        >
          Nova Pessoa
        </button>
      </div>
      <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Filtros</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="filtroEmpresa" class="block text-sm font-medium text-gray-700 mb-1"
              >Pessoa ou Empresa</label
            >
            <input
              v-model="filtroEmpresa"
              @input="realizarBusca"
              id="filtroEmpresa"
              type="text"
              placeholder="Nome da pessoa ou empresa"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuário</label>
            <div class="flex items-center space-x-4 mt-2 pt-1">
              <label class="flex items-center space-x-2">
                <input
                  v-model="filtroTipo.superAdmin"
                  type="checkbox"
                  class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-sm text-gray-700">Super Admin</span>
              </label>
              <label class="flex items-center space-x-2">
                <input
                  v-model="filtroTipo.admin"
                  type="checkbox"
                  class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300"
                />
                <span class="text-sm text-gray-700">Admin</span>
              </label>
            </div>
          </div>
          <!-- Espaço reservado para um terceiro filtro, se necessário -->
          <div></div>
        </div>
      </div>

      <div class="bg-white shadow overflow-auto sm:rounded-lg">
        <div v-if="loading" class="text-center py-8 text-gray-500">Carregando pessoas...</div>
        <div v-else-if="error" class="text-center py-8 text-red-500">{{ error }}</div>
        <table v-else-if="pessoas.length > 0" class="min-w-full divide-y divide-gray-200">
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
                Email
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Empresa
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Tipo
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Cargo
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
              >
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="pessoa in pessoas" :key="pessoa.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ pessoa.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ pessoa.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ pessoa.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ pessoa.company_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex items-center gap-2">
                <span v-if="pessoa.is_super_admin" title="Super Admin" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-yellow-500 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 3l2.09 6.26L21 9.27l-5 4.87L17.18 21 12 17.77 6.82 21 8 14.14l-5-4.87 6.91-1.01z"
                    />
                  </svg>
                  Super Admin
                </span>
                <span v-else-if="pessoa.is_admin" title="Admin" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-blue-500 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <circle
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="2"
                      fill="currentColor"
                      class="text-blue-200"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3"
                    />
                  </svg>
                  Admin
                </span>
                <span v-else title="Usuário" class="flex items-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                  </svg>
                  Usuário
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ pessoa.position_name || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                <button @click="editPessoa(pessoa)" class="text-indigo-600 hover:text-indigo-900">
                  Editar
                </button>
                <button
                  @click="promptDeletePessoa(pessoa.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Excluir
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="text-center py-8 text-gray-500">Nenhuma pessoa cadastrada.</div>
      </div>
    </main>
    <Footer />

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"
          >&#8203;</span
        >
        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div
                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"
              >
                <!-- Heroicon name: outline/exclamation -->
                <svg
                  class="h-6 w-6 text-red-600"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                  />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Confirmar Exclusão
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Tem certeza de que deseja excluir esta pessoa? Esta ação não pode ser desfeita.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="confirmDeletePessoa"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Excluir
            </button>
            <button
              @click="cancelDelete"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

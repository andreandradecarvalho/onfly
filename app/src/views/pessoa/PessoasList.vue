<script setup lang="ts">
import { ref, onMounted } from 'vue' // Added onMounted
import { useRouter } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'

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

// Mock data fetching function (replace with actual API call)
const fetchPessoas = async () => {
  loading.value = true
  error.value = null
  try {
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 500))
    // Replace with actual API call: e.g., await axios.get(...)
    pessoas.value = [
      {
        id: 1,
        nome: 'João Silva',
        email: 'joao.silva@exemplo.com',
        empresa: 'Empresa Exemplo 1',
        cargo: 'Gerente',
      },
      {
        id: 2,
        nome: 'Maria Souza',
        email: 'maria.souza@exemplo.com',
        empresa: 'Empresa Exemplo 2',
        cargo: 'Analista',
      },
    ]
  } catch (err: unknown) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao carregar pessoas'
    error.value = errorMessage
    // For actual API calls, you might use similar error handling as in EmpresasList.vue:
    // error.value = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || errorMessage
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
    // TODO: Implement actual delete API call here
    // await axios.delete(`${import.meta.env.VITE_API_URL}/pessoas/${pessoaIdToDelete.value}`, { headers: { Authorization: `Bearer ${auth.token.value}` } })
    console.log(`Simulating delete for pessoa ID: ${pessoaIdToDelete.value}`)
    await new Promise((resolve) => setTimeout(resolve, 500)) // Simulate API call
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
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ pessoa.nome }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ pessoa.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ pessoa.empresa || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ pessoa.cargo || 'N/A' }}
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

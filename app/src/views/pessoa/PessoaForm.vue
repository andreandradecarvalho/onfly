<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '../../components/Navbar.vue'
import axios from 'axios'

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
  endereco: '',
  cpf: '',
})

// Reactive state for CPF error
const cpfError = ref('')

// Reactive state for notification
const showNotification = ref(false)
const notificationType = ref<'success' | 'error'>('success')
const notificationMessage = ref('')

// Adiciona estado de loading
const isLoading = ref(false)

// Adiciona interface
interface Empresa {
  id: number
  name: string
}

interface Position {
  id: number
  name: string
}

// Estado para lista de cargos
const positions = ref<Array<Position>>([])

// Adiciona estado para lista de empresas
const empresas = ref<Array<Empresa>>([])

// Function to validate CPF
const validateCPF = (value: string) => {
  // Remove non-numeric characters
  const cleaned = value.replace(/\D/g, '')

  // Basic length validation
  if (cleaned.length !== 11) {
    cpfError.value = 'CPF deve ter 11 dígitos'
    return
  }

  // Check for known invalid patterns
  if (/^(\d)\1{10}$/.test(cleaned)) {
    cpfError.value = 'CPF inválido'
    return
  }

  // Validate check digits
  let sum = 0
  let remainder: number

  // First check digit
  for (let i = 0; i < 9; i++) {
    sum += parseInt(cleaned.charAt(i)) * (10 - i)
  }
  remainder = 11 - (sum % 11)
  if (remainder === 10 || remainder === 11) remainder = 0
  if (remainder !== parseInt(cleaned.charAt(9))) {
    cpfError.value = 'CPF inválido'
    return
  }

  // Second check digit
  sum = 0
  for (let i = 0; i < 10; i++) {
    sum += parseInt(cleaned.charAt(i)) * (11 - i)
  }
  remainder = 11 - (sum % 11)
  if (remainder === 10 || remainder === 11) remainder = 0
  if (remainder !== parseInt(cleaned.charAt(10))) {
    cpfError.value = 'CPF inválido'
    return
  }

  // Clear error if valid
  cpfError.value = ''
}

onMounted(async () => {
  try {
    // Carrega empresas
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/companies`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    empresas.value = response.data.data.map((e: Empresa) => ({ id: e.id, name: e.name }))
    // Carrega cargos
    const respPositions = await axios.get(`${import.meta.env.VITE_API_URL}/positions`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    positions.value = respPositions.data.map((p: Position) => ({ id: p.id, name: p.name }))
  } catch (error) {
    console.error('Falha ao carregar empresas ou cargos:', error)
  }

  if (!isNewForm && route.params.id) {
    try {
      isLoading.value = true

      const userId = Number(route.params.id)
      if (isNaN(userId)) throw new Error('ID inválido')

      const response = await axios.get(`${import.meta.env.VITE_API_URL}/users/${userId}`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })

      // Valida estrutura básica dos dados
      if (!response.data?.id) {
        throw new Error('Dados recebidos são inválidos')
      }

      pessoa.value = {
        id: response.data.id,
        nome: response.data.name || '',
        email: response.data.email || '',
        empresa: response.data.company_id ? String(response.data.company_id) : '',
        cargo: response.data.position_id ? String(response.data.position_id) : '',
        endereco: response.data.address || '',
        cpf: response.data.cpf || '',
      }
    } catch (error) {
      console.error('Falha:', error)
      showNotification.value = true
      notificationType.value = 'error'
      // Força navegação após 3 segundos
      setTimeout(() => router.push('/pessoas'), 3000)
    } finally {
      isLoading.value = false
    }
  }
})

const submitForm = () => {
  // Validate CPF
  validateCPF(pessoa.value.cpf)

  // TODO: Implement actual submission logic (create or update)
  if (isNewForm) {
    // Monta o payload conforme especificado
    const payload = {
      name: pessoa.value.nome,
      email: pessoa.value.email,
      password: pessoa.value.password || '12345678', // valor padrão se não houver campo
      position_company_id: pessoa.value.cargo,
      company_id: pessoa.value.empresa,
    }
    axios
      .post(`${import.meta.env.VITE_API_URL}/users`, payload, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })
      .then(() => {
        showNotification.value = true
        notificationType.value = 'success'
        notificationMessage.value = 'Pessoa criada com sucesso!'
        setTimeout(() => {
          router.push('/pessoas')
        }, 1500)
      })
      .catch((error) => {
        console.error('Erro ao criar pessoa:', error)
        showNotification.value = true
        notificationType.value = 'error'
        notificationMessage.value =
          error?.response?.data?.message || error.message || 'Erro desconhecido.'
      })
    return // Evita navegação dupla
  } else {
    // Atualiza pessoa existente
    const payload = {
      name: pessoa.value.nome,
      email: pessoa.value.email,
      password: pessoa.value.password || '12345678',
      position_company_id: pessoa.value.cargo,
      company_id: pessoa.value.empresa,
    }
    axios
      .put(`${import.meta.env.VITE_API_URL}/users/${pessoa.value.id}`, payload, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })
      .then(() => {
        showNotification.value = true
        notificationType.value = 'success'
        notificationMessage.value = 'Pessoa atualizada com sucesso!'
        setTimeout(() => {
          router.push('/pessoas')
        }, 1500)
      })
      .catch((error) => {
        console.error('Erro ao atualizar pessoa:', error)
        showNotification.value = true
        notificationType.value = 'error'
        notificationMessage.value = error?.response?.data?.message || error.message || 'Erro desconhecido.'
      })
    return // Evita navegação dupla
  }
}


const cancelForm = () => {
  // Navigate back to pessoas list
  router.push('/pessoas')
}
</script>

<template>
  <div v-if="isLoading" class="min-h-screen flex items-center justify-center">
    <svg
      class="animate-spin h-8 w-8 text-blue-500"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
  </div>
  <div v-else class="min-h-screen bg-gray-100">
    <Navbar />
    <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
      <div class="mb-6 flex space-x-4">
        <router-link
          to="/pessoas"
          class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
          >Pessoas</router-link
        >
        <router-link
          to="/dashboard"
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
          >Dashboard</router-link
        >
      </div>
      <div class="bg-white shadow-md rounded-lg p-6 mb-6 overflow-auto">
        <!-- Área de notificação -->
        <div v-if="showNotification" class="mb-4">
          <div
            v-if="notificationType === 'success'"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
            role="alert"
          >
            <span class="block sm:inline">{{ notificationMessage }}</span>
          </div>
          <div
            v-else-if="notificationType === 'error'"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
            role="alert"
          >
            <span class="block sm:inline">{{ notificationMessage }}</span>
          </div>
        </div>

        <!-- Formulário estruturado em grid -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Campos do formulário com validação -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="nome"> Nome </label>
              <input
                v-model="pessoa.nome"
                type="text"
                id="nome"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="email">
                Email
              </label>
              <input
                v-model="pessoa.email"
                type="email"
                id="email"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="empresa">
                Empresa
              </label>
              <select
                v-model="pessoa.empresa"
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Selecione uma empresa</option>
                <option v-for="empresa in empresas" :key="empresa.id" :value="empresa.id">
                  {{ empresa.name }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="cargo">
                Cargo
              </label>
              <select
                v-model="pessoa.cargo"
                id="cargo"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="">Selecione um cargo</option>
                <option v-for="position in positions" :key="position.id" :value="position.id">
                  {{ position.name }}
                </option>
              </select>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="endereco">
                Endereço
              </label>
              <input
                v-model="pessoa.endereco"
                type="text"
                id="endereco"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="cpf"> CPF </label>
              <input
                v-model="pessoa.cpf"
                type="text"
                id="cpf"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <div v-if="cpfError" class="text-red-500 text-xs">{{ cpfError }}</div>
            </div>
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
    </main>
  </div>
</template>

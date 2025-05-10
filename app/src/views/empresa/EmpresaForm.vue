<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/stores/auth'
import Navbar from '../../components/Navbar.vue'

const auth = useAuth()
const router = useRouter()
const route = useRoute()

// Notification state
const showNotification = ref(false)
const notificationMessage = ref('')
const notificationType = ref('') // 'success' or 'error'

// Form validation state
const emailError = ref('')

// Redirect if not authenticated
if (!auth.isAuthenticated()) {
  router.push('/login')
}

// Determine if this is a new or edit form
const isNewForm = route.name === 'EmpresaNew'

// Reactive state for the empresa
const empresa = ref({
  id: null as number | null,
  name: '', // Changed from nome
  document: '', // Changed from cnpj
  address: '', // Changed from endereco
  email: '',
  responsibility: '', // Changed from responsavel
})

// If editing, load existing empresa data
onMounted(async () => {
  if (!isNewForm && route.params.id) {
    try {
      const companyId = Number(route.params.id)
      const response = await axios.get(`${import.meta.env.VITE_API_URL}/companies/${companyId}`, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })
      empresa.value = { ...empresa.value, ...response.data.data } // Accessing the nested data object
    } catch (error) {
      console.error('Falha ao carregar dados da empresa:', error)
    }
  }
})

const submitForm = async () => {
  // Made async
  try {
    if (isNewForm) {
      console.log('Criando nova empresa:', empresa.value)
      await axios.post(`${import.meta.env.VITE_API_URL}/companies`, empresa.value, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })
      // Display success message for creation
      notificationMessage.value = 'Empresa criada com sucesso!' // Or use a message from response if available
      notificationType.value = 'success'
      showNotification.value = true
      setTimeout(() => {
        router.push('/empresas')
        showNotification.value = false // Hide after redirecting
      }, 2000) // Wait 2 seconds before redirecting
    } else {
      console.log('Atualizando empresa:', empresa.value)
      if (!empresa.value.id) {
        console.error('Error: Company ID is missing for update.')
        // Optionally, show an error message to the user
        return // Prevent API call if ID is missing
      }
      const response = await axios.put(
        `${import.meta.env.VITE_API_URL}/companies/${empresa.value.id}`,
        empresa.value,
        {
          headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${auth.token.value}`,
          },
        },
      )
      // Display success message
      notificationMessage.value =
        response.data && response.data.message
          ? response.data.message
          : 'Empresa atualizada com sucesso!'
      notificationType.value = 'success'
      showNotification.value = true
      setTimeout(() => {
        router.push('/empresas')
        showNotification.value = false // Hide after redirecting
      }, 2000) // Wait 2 seconds before redirecting
    }
    // Navigate back to empresas list only on successful create/update - Moved into setTimeout for success
  } catch (error) {
    console.error('Falha ao salvar empresa:', error)
    // Display error message
    notificationMessage.value =
      error.response && error.response.data && error.response.data.message
        ? error.response.data.message
        : 'Falha ao salvar empresa. Tente novamente.'
    notificationType.value = 'error'
    showNotification.value = true
  }
}

const cancelForm = () => {
  // Navigate back to empresas list
  router.push('/empresas')
}

const formatCNPJ = (event: Event) => {
  const input = event.target as HTMLInputElement
  let value = input.value.replace(/\D/g, '') // Remove all non-digits

  if (value.length > 14) {
    value = value.substring(0, 14)
  }

  if (value.length <= 2) {
    // XX
    empresa.value.document = value
  } else if (value.length <= 5) {
    // XX.XXX
    empresa.value.document = `${value.substring(0, 2)}.${value.substring(2)}`
  } else if (value.length <= 8) {
    // XX.XXX.XXX
    empresa.value.document = `${value.substring(0, 2)}.${value.substring(2, 5)}.${value.substring(5)}`
  } else if (value.length <= 12) {
    // XX.XXX.XXX/XXXX
    empresa.value.document = `${value.substring(0, 2)}.${value.substring(2, 5)}.${value.substring(5, 8)}/${value.substring(8)}`
  } else {
    // XX.XXX.XXX/XXXX-XX
    empresa.value.document = `${value.substring(0, 2)}.${value.substring(2, 5)}.${value.substring(5, 8)}/${value.substring(8, 12)}-${value.substring(12, 14)}`
  }
  // Ensure the input value is updated if it was truncated or formatted
  // This is important if the user pastes a long string
  if (input.value !== empresa.value.document) {
    input.value = empresa.value.document
  }
}

const validateEmail = () => {
  const emailValue = empresa.value.email
  if (!emailValue) {
    emailError.value = 'Email is required.' // Or handle as per your app's requirements (e.g., allow empty if not required overall)
    return
  }
  const emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/
  if (!emailRegex.test(emailValue)) {
    emailError.value = 'Please enter a valid email address.'
  } else {
    emailError.value = '' // Clear error if valid
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
      <div class="mb-6 flex space-x-4">
        <router-link
          to="/empresas"
          class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
          >Empresas</router-link
        >
        <router-link
          to="/dashboard"
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
          >Dashboard</router-link
        >
      </div>
      <div class="bg-white shadow-md rounded-lg p-6 mb-6 overflow-auto">
        <!-- Notification Area -->
        <div v-if="showNotification" class="mb-4">
          <div
            :class="{
              'rounded-md p-4': true,
              'bg-green-50 text-green-800': notificationType === 'success',
              'bg-red-50 text-red-800': notificationType === 'error',
            }"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <!-- Success Icon -->
                <svg
                  v-if="notificationType === 'success'"
                  class="h-5 w-5 text-green-400"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
                <!-- Error Icon -->
                <svg
                  v-if="notificationType === 'error'"
                  class="h-5 w-5 text-red-400"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  ></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium">{{ notificationMessage }}</p>
              </div>
              <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                  <button
                    @click="showNotification = false"
                    type="button"
                    :class="{
                      'inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2': true,
                      'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50':
                        notificationType === 'success',
                      'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50':
                        notificationType === 'error',
                    }"
                  >
                    <span class="sr-only">Dismiss</span>
                    <!-- Close Icon -->
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                      ></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <form @submit.prevent="submitForm" class="mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="nome">
                Nome da Empresa</label
              >
              <input
                v-model="empresa.name"
                type="text"
                id="nome"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="cnpj">CNPJ</label>
              <input
                v-model="empresa.document"
                @input="formatCNPJ"
                type="text"
                id="cnpj"
                required
                maxlength="18"
                placeholder="XX.XXX.XXX/XXXX-XX"
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="endereco"
                >Endereço
              </label>
              <input
                v-model="empresa.address"
                type="text"
                id="endereco"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email </label>
              <input
                v-model="empresa.email"
                @blur="validateEmail"
                type="email"
                id="email"
                required
                :class="{
                  'w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500': true,
                  'border-red-500 ring-red-500': emailError,
                }"
              />
              <p v-if="emailError" class="text-xs text-red-600 mt-1">{{ emailError }}</p>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1" for="responsavel"
                >Responsável</label
              >
              <input
                v-model="empresa.responsibility"
                type="text"
                id="responsavel"
                required
                class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div class="flex items-center justify-between space-x-4">
              <button
                type="submit"
                class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              >
                {{ isNewForm ? 'Cadastrar' : 'Atualizar ' }}
              </button>
              <button
                type="button"
                @click="cancelForm"
                class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
              >
                Cancelar
              </button>
            </div>
          </div>
        </form>
      </div>
    </main>
    <Footer />
  </div>
</template>

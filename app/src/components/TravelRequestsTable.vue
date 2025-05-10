<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import CreateTravelRequestModal from './CreateTravelRequestModal.vue'

interface TravelRequest {
  id: number
  destination: string
  departureLocation: string
  departureDate: string
  returnDate: string
  status: 'pending' | 'approved' | 'rejected'
  amount: number
  description: string
  createdAt: string
}

type StatusUpdatePayload = {
  id: number
  status: 'pending' | 'approved' | 'rejected'
}

const travelRequests = ref<TravelRequest[]>([])
const filteredRequests = ref<TravelRequest[]>([])
const selectedStatus = ref('all')
const isLoading = ref(true)
const error = ref('')
const isModalOpen = ref(false)
const userRole = ref('admin') // Simulated user role, can be dynamic

// Advanced filtering state
const filterDestination = ref('')
const filterStartDate = ref('')
const filterEndDate = ref('')
const filterCreatedStartDate = ref('')
const filterCreatedEndDate = ref('')

const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'pending', label: 'Pendente' },
  { value: 'approved', label: 'Aprovado' },
  { value: 'rejected', label: 'Rejeitado' },
]

const fetchTravelRequests = async () => {
  try {
    isLoading.value = true
    // Replace with your actual API endpoint
    const response = await axios.get('/api/travel-requests')
    travelRequests.value = response.data
    filterRequests()
  } catch (err) {
    error.value = 'Erro ao carregar as solicitações de viagem'
    console.error('Error fetching travel requests:', err)
  } finally {
    isLoading.value = false
  }
}

const addNewTravelRequest = (newRequest: TravelRequest) => {
  travelRequests.value.push(newRequest)
  filterRequests()
}

const updateTravelRequestStatus = async (payload: StatusUpdatePayload) => {
  try {
    await axios.patch(`/api/travel-requests/${payload.id}/status`, {
      status: payload.status,
    })

    // Update local state
    const requestIndex = travelRequests.value.findIndex((req) => req.id === payload.id)
    if (requestIndex !== -1) {
      travelRequests.value[requestIndex].status = payload.status
      filterRequests()
    }
  } catch (err) {
    console.error('Erro ao atualizar status da solicitação:', err)
    error.value = 'Não foi possível atualizar o status da solicitação'
  }
}

const canUpdateStatus = computed(() => {
  return userRole.value === 'admin'
})

const filterRequests = () => {
  let result = travelRequests.value

  // Filter by status
  if (selectedStatus.value !== 'all') {
    result = result.filter((request) => request.status === selectedStatus.value)
  }

  // Filter by destination
  if (filterDestination.value.trim()) {
    const searchTerm = filterDestination.value.trim().toLowerCase()
    result = result.filter((request) => request.destination.toLowerCase().includes(searchTerm))
  }

  // Filter by travel dates
  if (filterStartDate.value && filterEndDate.value) {
    result = result.filter((request) => {
      const requestStartDate = new Date(request.departureDate)
      const requestEndDate = new Date(request.returnDate)
      const filterStart = new Date(filterStartDate.value)
      const filterEnd = new Date(filterEndDate.value)

      return (
        (requestStartDate >= filterStart && requestStartDate <= filterEnd) ||
        (requestEndDate >= filterStart && requestEndDate <= filterEnd) ||
        (requestStartDate <= filterStart && requestEndDate >= filterEnd)
      )
    })
  } else if (filterStartDate.value) {
    const filterStart = new Date(filterStartDate.value)
    result = result.filter((request) => new Date(request.departureDate) >= filterStart)
  } else if (filterEndDate.value) {
    const filterEnd = new Date(filterEndDate.value)
    result = result.filter((request) => new Date(request.returnDate) <= filterEnd)
  }

  // Filter by request creation dates
  if (filterCreatedStartDate.value && filterCreatedEndDate.value) {
    const createdStart = new Date(filterCreatedStartDate.value)
    const createdEnd = new Date(filterCreatedEndDate.value)
    result = result.filter((request) => {
      const createdDate = new Date(request.createdAt)
      return createdDate >= createdStart && createdDate <= createdEnd
    })
  } else if (filterCreatedStartDate.value) {
    const createdStart = new Date(filterCreatedStartDate.value)
    result = result.filter((request) => new Date(request.createdAt) >= createdStart)
  } else if (filterCreatedEndDate.value) {
    const createdEnd = new Date(filterCreatedEndDate.value)
    result = result.filter((request) => new Date(request.createdAt) <= createdEnd)
  }

  filteredRequests.value = result
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('pt-BR')
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

onMounted(() => {
  fetchTravelRequests()
})

// Watch for status changes
watch(
  [
    selectedStatus,
    filterDestination,
    filterStartDate,
    filterEndDate,
    filterCreatedStartDate,
    filterCreatedEndDate,
  ],
  () => {
    filterRequests()
  },
)
</script>

<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Solicitação de Viagem</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
        <input
          v-model="filterDestination"
          type="text"
          placeholder="Filtrar por destino"
          class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data da Viagem</label>
        <div class="flex space-x-2">
          <input
            v-model="filterStartDate"
            type="date"
            placeholder="Data início"
            class="w-1/2 border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <input
            v-model="filterEndDate"
            type="date"
            placeholder="Data fim"
            class="w-1/2 border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data da Solicitação</label>
        <div class="flex space-x-2">
          <input
            v-model="filterCreatedStartDate"
            type="date"
            placeholder="Data início"
            class="w-1/2 border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <input
            v-model="filterCreatedEndDate"
            type="date"
            placeholder="Data fim"
            class="w-1/2 border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>
    </div>

    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center space-x-4">
        <label class="text-gray-600">Status:</label>
        <select v-model="selectedStatus" class="border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="option in statusOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>
      </div>

      <button @click="isModalOpen = true" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">Nova Solicitação</button>
    </div>
  </div>


  <div class="p-6 bg-white rounded-lg shadow-md">
    <div v-if="isLoading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-4 text-gray-600">Carregando solicitações...</p>
    </div>

    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-500">{{ error }}</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
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
              Destino
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Data Início
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Data Fim
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Valor
            </th>
            <th
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
              Status
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="request in filteredRequests" :key="request.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ request.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ request.destination }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(request.startDate) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(request.endDate) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatCurrency(request.amount) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center space-x-2">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="{
                    'bg-yellow-100 text-yellow-800': request.status === 'pending',
                    'bg-green-100 text-green-800': request.status === 'approved',
                    'bg-red-100 text-red-800': request.status === 'rejected',
                  }"
                >
                  {{
                    request.status === 'pending'
                      ? 'Pendente'
                      : request.status === 'approved'
                        ? 'Aprovado'
                        : 'Rejeitado'
                  }}
                </span>

                <div v-if="canUpdateStatus" class="flex space-x-1">
                  <button
                    v-if="request.status !== 'approved'"
                    @click="updateTravelRequestStatus({ id: request.id, status: 'approved' })"
                    class="text-green-600 hover:text-green-800 transition-colors"
                    title="Aprovar"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                  </button>

                  <button
                    v-if="request.status !== 'rejected'"
                    @click="updateTravelRequestStatus({ id: request.id, status: 'rejected' })"
                    class="text-red-600 hover:text-red-800 transition-colors"
                    title="Rejeitar"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-4 w-4"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="filteredRequests.length === 0" class="text-center py-8">
        <p class="text-gray-500">Nenhuma solicitação de viagem encontrada.</p>
      </div>
    </div>
  </div>

  <CreateTravelRequestModal
    v-if="isModalOpen"
    @close="isModalOpen = false"
    @created="addNewTravelRequest"
  />
</template>

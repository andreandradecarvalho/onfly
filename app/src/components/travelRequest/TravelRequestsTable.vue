<script setup lang="ts">
// Handler para mudança de status
function onStatusChange(val: string) {
  selectedStatus.value = val
  filterRequests()
}

import { ref, onMounted, watch, computed } from 'vue'
import axios from 'axios'
import { useAuth } from '@/stores/auth'
import CreateTravelRequestModal from '@/components/travelRequest/CreateTravelRequestModal.vue'
import FlightTicketItemsTable from '@/components/flightTicket/FlightTicketItemsTable.vue'
import TravelRequestsFilter from '@/components/travelRequest/TravelRequestsFilter.vue'
const auth = useAuth()

interface TravelRequest {
  id: number
  orderId: string
  userName: string
  userEmail: string
  companyName: string
  origin: string
  destination: string
  departureDate: string
  returnDate: string
  status: string
  createdAt: string
  isCancellable?: boolean // Added for UI logic
}

type StatusUpdatePayload = {
  id: number
  status: 'solicitado' | 'aprovado' | 'cancelado'
}

const travelRequests = ref<TravelRequest[]>([])
const filteredRequests = ref<TravelRequest[]>([])
const selectedStatus = ref('all')
const isLoading = ref(true)
const error = ref('')
const isModalOpen = ref(false)
const userRole = ref('admin') // Added back userRole
const processedFilteredRequests = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  return filteredRequests.value.map((req) => {
    if (req.status === 'cancelado') {
      return { ...req, isCancellable: false }
    }

    let isCancellable = true // Default to true for non-approved
    if (req.status === 'aprovado') {
      const departureDate = new Date(req.departureDate)
      departureDate.setHours(0, 0, 0, 0)
      const fiveDaysFromNow = new Date(today.getTime() + 5 * 24 * 60 * 60 * 1000)
      isCancellable = departureDate >= fiveDaysFromNow // Changed to >=
    }
    // For 'solicitado' or other non-approved, non-cancelled statuses, isCancellable remains true by default
    return { ...req, isCancellable }
  })
})

const filterDestination = ref('')
const filterStartDate = ref('')
const filterEndDate = ref('')
const filterCreatedStartDate = ref('')
const filterCreatedEndDate = ref('')

const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'solicitado', label: 'Solicitado' },
  { value: 'aprovado', label: 'Aprovado' },
  { value: 'cancelado', label: 'Cancelado' },
]

const fetchTravelRequests = async () => {
  try {
    isLoading.value = true
    console.log('API URL:', import.meta.env.VITE_API_URL)
    console.log('Token:', auth.token.value)
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/flight_tickets`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    travelRequests.value = response.data.map(
      (item: {
        id: number
        order_id: string
        user?: { name?: string; email?: string; companies?: { name?: string }[] }
        origin: string
        destination: string
        departure_date: string
        return_date: string
        status: string
        created_at: string
      }) => {
        return {
          id: item.id,
          orderId: item.order_id,
          userName: item.user?.name || '',
          userEmail: item.user?.email || '',
          companyName: item.user?.companies?.[0]?.name || '',
          origin: item.origin,
          destination: item.destination,
          departureDate: item.departure_date,
          returnDate: item.return_date,
          status: item.status,
          createdAt: item.created_at,
        }
      },
    )
    filterRequests()
  } catch (err: any) {
    if (err.response) {
      error.value = `Erro: ${err.response.status} - ${err.response.data?.message || err.response.statusText}`
      console.error('Error fetching flight tickets:', err, err.response)
    } else {
      error.value = 'Erro ao carregar as solicitações de passagem aérea'
      console.error('Error fetching flight tickets:', err)
    }
  } finally {
    isLoading.value = false
  }
}

const addNewTravelRequest = (newRequest: TravelRequest) => {
  travelRequests.value.push(newRequest)
  filterRequests()
}

const handleConfirmUpdateStatus = (payload: StatusUpdatePayload) => {
  // If the action is to cancel, check rules if the ticket is approved.
  if (payload.status === 'cancelado') {
    const requestToCancel = travelRequests.value.find((req) => req.id === payload.id)
    if (requestToCancel && requestToCancel.status === 'aprovado') {
      const today = new Date()
      today.setHours(0, 0, 0, 0)
      const departureDate = new Date(requestToCancel.departureDate)
      departureDate.setHours(0, 0, 0, 0)
      const fiveDaysFromNow = new Date(today.getTime() + 5 * 24 * 60 * 60 * 1000)

      if (departureDate <= fiveDaysFromNow) {
        showAlert({
          title: 'Cancelamento Não Permitido',
          message:
            'Cancelamento não permitido. Para solicitações aprovadas, a data de partida deve ser em 5 dias ou mais.',
          confirmText: 'Entendido',
          onConfirm: () => {
            console.log('User acknowledged 5-day rule for approved.')
          },
          cancelText: '',
        })
        return // Stop further processing for this specific case
      }
    }
    // For non-approved tickets, or approved tickets outside the 5-day window, proceed to general confirmation.
  }

  // If not a restricted cancellation, or if it's an approval, proceed with general confirmation.
  showAlert({
    title: 'Confirmar Ação',
    message:
      payload.status === 'aprovado'
        ? 'Tem certeza de que deseja aprovar esta solicitação?'
        : 'Tem certeza de que deseja cancelar esta solicitação?',
    confirmText: payload.status === 'aprovado' ? 'Aprovar' : 'Cancelar',
    cancelText: 'Cancelar',
    onConfirm: () => updateTravelRequestStatus(payload),
  })
}

const updateTravelRequestStatus = async (payload: StatusUpdatePayload) => {
  try {
    await axios.put(
      `${import.meta.env.VITE_API_URL}/flight_tickets/${payload.id}`,
      { status: payload.status },
      {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      },
    )

    const requestIndex = travelRequests.value.findIndex((req) => req.id === payload.id)
    if (requestIndex !== -1) {
      travelRequests.value[requestIndex].status = payload.status
      filterRequests()
    }
  } catch (err) {
    console.error('Error updating travel request status:', err)
    error.value = 'Erro ao atualizar o status da solicitação'
  }
}

const canUpdateStatus = computed(() => {
  return userRole.value === 'admin'
})

const filterRequests = () => {
  let tempRequests = travelRequests.value

  if (selectedStatus.value !== 'all') {
    tempRequests = tempRequests.filter((req) => req.status === selectedStatus.value)
  }

  if (filterDestination.value) {
    tempRequests = tempRequests.filter((req) =>
      req.destination.toLowerCase().includes(filterDestination.value.toLowerCase()),
    )
  }

  if (filterStartDate.value) {
    tempRequests = tempRequests.filter(
      (req) => new Date(req.departureDate) >= new Date(filterStartDate.value),
    )
  }

  if (filterEndDate.value) {
    tempRequests = tempRequests.filter(
      (req) => new Date(req.departureDate) <= new Date(filterEndDate.value),
    )
  }

  if (filterCreatedStartDate.value) {
    tempRequests = tempRequests.filter(
      (req) => new Date(req.createdAt) >= new Date(filterCreatedStartDate.value),
    )
  }
  filteredRequests.value = tempRequests
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
// --- ALERT MODAL ---
const showAlertModal = ref(false)
const alertTitle = ref('')
const alertMessage = ref('')
const alertConfirmText = ref('Excluir')
const alertCancelText = ref('Cancelar')
const alertOnConfirm = ref<null | (() => void)>(null)

function showAlert({
  title,
  message,
  onConfirm,
  confirmText = 'Excluir',
  cancelText = 'Cancelar',
}: {
  title: string
  message: string
  onConfirm: () => void
  confirmText?: string
  cancelText?: string
}) {
  alertTitle.value = title
  alertMessage.value = message
  alertOnConfirm.value = onConfirm
  alertConfirmText.value = confirmText
  alertCancelText.value = cancelText
  showAlertModal.value = true
}
</script>

<template>
  <div>
    <!-- ALERT MODAL PADRÃO SOLICITADO -->
    <div
      v-if="showAlertModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
    >
      <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm mx-auto">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">{{ alertTitle }}</h3>
        <p class="text-sm text-gray-500 mb-6">{{ alertMessage }}</p>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            @click="showAlertModal = false"
          >
            {{ alertCancelText }}
          </button>
          <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            @click="
              () => {
                showAlertModal = false
                alertOnConfirm && alertOnConfirm()
              }
            "
          >
            {{ alertConfirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <TravelRequestsFilter
    :filterDestination="filterDestination.value"
    :filterStartDate="filterStartDate.value"
    :filterEndDate="filterEndDate.value"
    :filterCreatedStartDate="filterCreatedStartDate.value"
    :filterCreatedEndDate="filterCreatedEndDate.value"
    :selectedStatus="selectedStatus.value"
    :statusOptions="statusOptions"
    @update:filterDestination="(val) => (filterDestination.value = val)"
    @update:filterStartDate="(val) => (filterStartDate.value = val)"
    @update:filterEndDate="(val) => (filterEndDate.value = val)"
    @update:filterCreatedStartDate="(val) => (filterCreatedStartDate.value = val)"
    @update:filterCreatedEndDate="(val) => (filterCreatedEndDate.value = val)"
    @update:selectedStatus="onStatusChange"
  />

  <div class="p-6 bg-white rounded-lg shadow-md">
    <div v-if="isLoading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-4 text-gray-600">Carregando solicitações...</p>
    </div>

    <div v-else-if="error" class="text-center py-8">
      <p class="text-red-500">{{ error }}</p>
    </div>

    <div v-else class="overflow-x-auto">
      <FlightTicketItemsTable
        :requests="processedFilteredRequests"
        :canUpdateStatus="canUpdateStatus"
        @confirm-update-status="handleConfirmUpdateStatus"
      />
    </div>
  </div>

  <CreateTravelRequestModal
    v-if="isModalOpen"
    @close="isModalOpen = false"
    @created="addNewTravelRequest"
  />
</template>

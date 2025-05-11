<template>
  <div
    v-if="open"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-xl p-10 w-full max-w-4xl">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Editar Solicitação de Viagem</h2>
      <form @submit.prevent="submitEdit" class="space-y-4">
        <div
          v-if="showSuccessNotification"
          class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md text-center"
        >
          {{ notificationMessage }}
        </div>
        <div
          v-if="errorMessage"
          class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md text-center"
        >
          {{ errorMessage }}
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureLocation" class="block text-sm font-medium text-gray-700 mb-1"
              >Saindo de</label
            >
            <select
              id="departureLocation"
              v-model="form.departureLocation"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="" disabled>Selecione uma cidade</option>
              <option v-for="city in cities" :key="city.value" :value="city.label">
                {{ city.label }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
            <select
              id="destination"
              v-model="form.destination"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="" disabled>Selecione uma cidade</option>
              <option v-for="city in cities" :key="city.value" :value="city.label">
                {{ city.label }}
              </option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureDate" class="block text-sm font-medium text-gray-700"
              >Data da ida</label
            >
            <input
              id="departureDate"
              v-model="form.departureDate"
              type="date"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label for="returnDate" class="block text-sm font-medium text-gray-700 mb-1"
              >Data da volta</label
            >
            <input
              id="returnDate"
              v-model="form.returnDate"
              type="date"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="travelerId" class="block text-sm font-medium text-gray-700 mb-1"
              >Viajante</label
            >
            <select
              id="travelerId"
              v-model="form.travelerId"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="" disabled>Selecione um viajante</option>
              <optgroup
                v-for="company in companiesWithTravelers"
                :key="company.name"
                :label="company.name"
              >
                <option
                  v-for="traveler in company.travelers"
                  :key="traveler.id"
                  :value="String(traveler.id)"
                >
                  {{ traveler.name }}
                </option>
              </optgroup>
            </select>
          </div>
        </div>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50"
            :disabled="loading"
          >
            <span v-if="loading">Salvando...</span>
            <span v-else>Salvar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, reactive, onMounted } from 'vue'
import axios from 'axios'
import { useAuth } from '@/stores/auth'
import type { TravelRequest } from './FlightTicketItemsTable.vue'

const props = defineProps<{
  open: boolean
  request: TravelRequest | null
}>()

const emit = defineEmits(['close', 'save'])

const auth = useAuth()

const cities = ref([
  { value: 'SAO', label: 'São Paulo' },
  { value: 'RIO', label: 'Rio de Janeiro' },
  { value: 'BHI', label: 'Belo Horizonte' },
  { value: 'BSB', label: 'Brasília' },
  { value: 'SSA', label: 'Salvador' },
  { value: 'REC', label: 'Recife' },
  { value: 'POA', label: 'Porto Alegre' },
  { value: 'CWB', label: 'Curitiba' },
])

const companiesWithTravelers = ref<
  Array<{ name: string; travelers: Array<{ id: string | number; name: string }> }>
>([])

const form = reactive({
  departureLocation: '',
  destination: '',
  departureDate: '',
  returnDate: '',
  travelerId: '',
})

const fetchTravelersAndGroupThem = async () => {
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/users`, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    const users = response.data
    if (!Array.isArray(users)) {
      companiesWithTravelers.value = []
      return
    }
    const groupedByCompany: { [key: string]: Array<{ id: string | number; name: string }> } = {}
    users.forEach((user: any) => {
      const companyName = user.company_name || 'Empresa Desconhecida'
      if (!groupedByCompany[companyName]) groupedByCompany[companyName] = []
      groupedByCompany[companyName].push({ id: user.id, name: user.name })
    })
    companiesWithTravelers.value = Object.entries(groupedByCompany).map(([name, travelers]) => ({
      name,
      travelers,
    }))
  } catch {
    companiesWithTravelers.value = []
  }
}

onMounted(async () => {
  await fetchTravelersAndGroupThem()
  // Após carregar viajantes, se houver request selecionada, garanta que o viajante fique marcado
  console.log('props.request')
  console.log(props)
  if (props.request && props.request.travelerId) {
    // Verifica se existe esse viajante na lista carregada
    const exists = companiesWithTravelers.value.some((company) =>
      company.travelers.some(
        (traveler) => String(traveler.id) === String(props.request!.travelerId),
      ),
    )
    if (exists) {
      form.travelerId = String(props.request.travelerId)
    } else {
      form.travelerId = ''
    }
  }
})

watch(
  () => props.request,
  (newRequest) => {
    console.log('newRequest')
    console.log(newRequest)
    if (newRequest) {
      form.departureLocation = newRequest.origin
      form.destination = newRequest.destination
      form.departureDate = newRequest.departureDate
      form.returnDate = newRequest.returnDate
      // Se vier user_id, use normalmente
      if (newRequest.user_id) {
        form.travelerId = String(newRequest.user_id)
      } else {
        // Senão, tente encontrar pelo nome ou email
        let foundId = ''
        companiesWithTravelers.value.forEach((company) => {
          company.travelers.forEach((traveler) => {
            if (
              traveler.name === newRequest.userName ||
              (traveler.email && traveler.email === newRequest.userEmail)
            ) {
              foundId = String(traveler.id)
            }
          })
        })
        form.travelerId = foundId
      }
    }
  },
  { immediate: false },
)

const loading = ref(false)
const errorMessage = ref('')
const notificationMessage = ref('')
const showSuccessNotification = ref(false)

async function submitEdit() {
  if (!props.request) return
  loading.value = true
  notificationMessage.value = ''
  errorMessage.value = ''
  const payload = {
    user_id: Number(form.travelerId),
    origin: form.departureLocation,
    destination: form.destination,
    departure_date: form.departureDate,
    return_date: form.returnDate,
  }
  try {
    await axios.put(`${import.meta.env.VITE_API_URL}/flight_tickets/${props.request.id}`, payload, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${auth.token.value}`,
      },
    })
    notificationMessage.value = 'Solicitação de viagem atualizada com sucesso!'
    showSuccessNotification.value = true
    setTimeout(() => {
      emit('save', payload)
      emit('close')
      showSuccessNotification.value = false
      notificationMessage.value = ''
      loading.value = false
    }, 3000)
  } catch (error) {
    errorMessage.value = 'Erro ao atualizar solicitação!'
    loading.value = false
    console.error('Erro ao editar ticket:', error)
    setTimeout(() => {
      emit('close')
      errorMessage.value = ''
    }, 3000)
  }
}
</script>

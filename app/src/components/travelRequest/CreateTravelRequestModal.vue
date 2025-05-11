<script setup lang="ts">
import { ref, onMounted } from 'vue' // Removido computed
import axios from 'axios'
import { useAuth } from '@/stores/auth' // ATENÇÃO: Verifique se este é o caminho correto para o seu store de autenticação.

const auth = useAuth()

// Lista de cidades de exemplo
const cities = ref([
  { value: 'SAO', label: 'São Paulo' },
  { value: 'RIO', label: 'Rio de Janeiro' },
  { value: 'BHI', label: 'Belo Horizonte' },
  { value: 'BSB', label: 'Brasília' },
  { value: 'SSA', label: 'Salvador' },
  { value: 'REC', label: 'Recife' },
  { value: 'POA', label: 'Porto Alegre' },
  { value: 'CWB', label: 'Curitiba' },
  // Adicione mais cidades conforme necessário
])

// Função para formatar data para o padrão BR
const formatDateToBR = (dateString: string): string => {
  if (!dateString) return ''
  const [year, month, day] = dateString.split('-')
  return `${day}/${month}/${year}`
}

interface TravelRequest {
  departureLocation: string
  destination: string
  departureDate: string
  returnDate: string
  travelerId: string | number
}

const emit = defineEmits(['close', 'created'])

const companiesWithTravelers = ref<
  Array<{ name: string; travelers: Array<{ id: string | number; name: string }> }>
>([])

// Função para buscar e agrupar viajantes
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
      console.error('API /users não retornou um array:', users)
      companiesWithTravelers.value = [] // Limpa ou define um valor padrão
      return
    }

    const groupedByCompany: { [key: string]: Array<{ id: string | number; name: string }> } = {}

    users.forEach((user) => {
      const companyName = user.company_name || 'Empresa Desconhecida' // Ajustado para usar user.company_name
      if (!groupedByCompany[companyName]) {
        groupedByCompany[companyName] = []
      }
      groupedByCompany[companyName].push({ id: user.id, name: user.name })
    })

    companiesWithTravelers.value = Object.entries(groupedByCompany).map(([name, travelers]) => ({
      name,
      travelers,
    }))
  } catch (error) {
    console.error('Erro ao buscar viajantes:', error)
    // Tratar o erro, talvez mostrar uma mensagem para o usuário
    companiesWithTravelers.value = [] // Limpa em caso de erro para não mostrar dados antigos/exemplos
  }
}

// Buscar viajantes quando o componente for montado
onMounted(() => {
  fetchTravelersAndGroupThem()
})

const travelRequest = ref<TravelRequest>({
  departureLocation: '',
  destination: '',
  departureDate: '',
  returnDate: '',
  travelerId: '',
  passengers: 1,

})

const errors = ref<{ [key: string]: string }>({})
const showSuccessNotification = ref(false)
const notificationMessage = ref('')

const validateForm = (): boolean => {
  errors.value = {}

  if (!travelRequest.value.departureLocation.trim()) {
    errors.value.departureLocation = 'Local de saída é obrigatório'
  }

  if (!travelRequest.value.destination.trim()) {
    errors.value.destination = 'Destino é obrigatório'
  }

  if (!travelRequest.value.departureDate) {
    errors.value.departureDate = 'Data da ida é obrigatória'
  }

  if (
    travelRequest.value.returnDate &&
    travelRequest.value.departureDate > travelRequest.value.returnDate
  ) {
    errors.value.returnDate = 'Data da volta deve ser posterior à data da ida'
  }

  if (!travelRequest.value.travelerId) {
    errors.value.travelerId = 'Viajante é obrigatório'
  }

  if (travelRequest.value.passengers < 1) {

  }

  return Object.keys(errors.value).length === 0
}

const submitTravelRequest = async () => {
  if (validateForm()) {
    const requesterId = auth.userData?.value?.id
    try {
      const payload = {
        user_id: travelRequest.value.travelerId,
        requester_id: requesterId,
        origin: travelRequest.value.departureLocation,
        destination: travelRequest.value.destination,
        departure_date: travelRequest.value.departureDate,
        return_date: travelRequest.value.returnDate,
        status: 'solicitado', // Preserving user's change
      }

      const response = await axios.post(`${import.meta.env.VITE_API_URL}/flight_tickets`, payload, {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${auth.token.value}`,
        },
      })

      // Show success message
      notificationMessage.value = 'Solicitação de viagem criada com sucesso!'
      showSuccessNotification.value = true

      // Wait a bit, then emit events and close
      setTimeout(() => {
        emit('created', response.data)
        emit('close')
        showSuccessNotification.value = false // Reset for next time
      }, 3000) // 3 seconds delay
    } catch (error: any) {
      console.error('Erro detalhado ao criar solicitação de viagem:', error)
      if (axios.isAxiosError(error)) {
        if (error.response) {
          const apiErrorMessage =
            error.response.data?.message ||
            error.response.data?.error ||
            (typeof error.response.data === 'string'
              ? error.response.data
              : JSON.stringify(error.response.data))
          errors.value.submit = `Erro da API (${error.response.status}): ${apiErrorMessage}.`
          console.error('Detalhes do erro da API:', {
            status: error.response.status,
            data: error.response.data,
            headers: error.response.headers,
          })
        } else if (error.request) {
          errors.value.submit =
            'Não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.'
          console.error('Erro de requisição (sem resposta do servidor):', error.request)
        } else {
          errors.value.submit = `Erro ao configurar a requisição para a API: ${error.message}`
          console.error('Erro de configuração da requisição Axios:', error.message)
        }
      } else if (error instanceof Error) {
        errors.value.submit = `Erro inesperado ao enviar solicitação: ${error.message}`
      } else {
        errors.value.submit = 'Ocorreu um erro desconhecido ao processar sua solicitação.'
      }
    }
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-10 w-full max-w-4xl">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Nova Solicitação de Viagem</h2>

      <form @submit.prevent="submitTravelRequest" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureLocation" class="block text-sm font-medium text-gray-700 mb-1"
              >Saindo de</label
            >
            <select
              id="departureLocation"
              v-model="travelRequest.departureLocation"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.departureLocation }"
            >
              <option value="" disabled>Selecione uma cidade</option>
              <option v-for="city in cities" :key="city.value" :value="city.label">
                {{ city.label }}
              </option>
            </select>
            <p v-if="errors.departureLocation" class="text-red-500 text-xs mt-1">
              {{ errors.departureLocation }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
            <select
              id="destination"
              v-model="travelRequest.destination"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.destination }"
            >
              <option value="" disabled>Selecione uma cidade</option>
              <option v-for="city in cities" :key="city.value" :value="city.label">
                {{ city.label }}
              </option>
            </select>
            <p v-if="errors.destination" class="text-red-500 text-xs mt-1">
              {{ errors.destination }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureDate" class="block text-sm font-medium text-gray-700"
              >Data da ida</label
            >
            <input
              id="departureDate"
              v-model="travelRequest.departureDate"
              type="date"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.departureDate }"
            />
            <p v-if="travelRequest.departureDate" class="text-xs text-gray-500 mt-1">
              Selecionado: {{ formatDateToBR(travelRequest.departureDate) }}
            </p>
            <p v-if="errors.departureDate" class="text-red-500 text-xs mt-1">
              {{ errors.departureDate }}
            </p>
          </div>

          <div>
            <label for="returnDate" class="block text-sm font-medium text-gray-700 mb-1"
              >Data da volta</label
            >
            <input
              id="returnDate"
              v-model="travelRequest.returnDate"
              type="date"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.returnDate }"
            />
            <p v-if="travelRequest.returnDate" class="text-xs text-gray-500 mt-1">
              Selecionado: {{ formatDateToBR(travelRequest.returnDate) }}
            </p>
            <p v-if="errors.returnDate" class="text-red-500 text-xs mt-1">
              {{ errors.returnDate }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="travelerId" class="block text-sm font-medium text-gray-700 mb-1"
              >Viajante</label
            >
            <select
              id="travelerId"
              v-model="travelRequest.travelerId"
              class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.travelerId }"
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
                  :value="traveler.id"
                >
                  {{ traveler.name }}
                </option>
              </optgroup>
            </select>
            <p v-if="errors.travelerId" class="text-red-500 text-xs mt-1">
              {{ errors.travelerId }}
            </p>
          </div>


        </div>



        <!-- Submission Error Message -->
        <p v-if="errors.submit" class="text-red-500 text-sm mt-2 mb-4">
          {{ errors.submit }}
        </p>

        <!-- Success Notification -->
        <div
          v-if="showSuccessNotification"
          class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md"
          role="alert"
        >
          {{ notificationMessage }}
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
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Criar Solicitação
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

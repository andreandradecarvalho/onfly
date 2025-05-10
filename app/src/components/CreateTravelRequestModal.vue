<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

interface TravelRequest {
  departureLocation: string
  destination: string
  departureDate: string
  returnDate: string
  passengers: number
  amount: number
  description: string
}

const emit = defineEmits(['close', 'created'])

const travelRequest = ref<TravelRequest>({
  departureLocation: '',
  destination: '',
  departureDate: '',
  returnDate: '',
  passengers: 1,
  amount: 0,
  description: ''
})

const errors = ref<{ [key: string]: string }>({})

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

  if (!travelRequest.value.returnDate) {
    errors.value.returnDate = 'Data da volta é obrigatória'
  }

  if (travelRequest.value.departureDate > travelRequest.value.returnDate) {
    errors.value.returnDate = 'Data da volta deve ser posterior à data da ida'
  }

  if (travelRequest.value.passengers < 1) {
    errors.value.passengers = 'Número de passageiros deve ser pelo menos 1'
  }

  if (travelRequest.value.amount <= 0) {
    errors.value.amount = 'Valor deve ser maior que zero'
  }

  return Object.keys(errors.value).length === 0
}

const submitTravelRequest = async () => {
  if (validateForm()) {
    try {
      const response = await axios.post('/api/travel-requests', {
        ...travelRequest.value,
        status: 'pending'
      })
      
      emit('created', response.data)
      emit('close')
    } catch (error) {
      console.error('Erro ao criar solicitação de viagem:', error)
      errors.value.submit = 'Erro ao enviar solicitação. Tente novamente.'
    }
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">Nova Solicitação de Viagem</h2>
      
      <form @submit.prevent="submitTravelRequest" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureLocation" class="block text-sm font-medium text-gray-700">Saindo de</label>
            <input 
              id="departureLocation" 
              v-model="travelRequest.departureLocation" 
              type="text" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.departureLocation }"
            >
            <p v-if="errors.departureLocation" class="text-red-500 text-xs mt-1">
              {{ errors.departureLocation }}
            </p>
          </div>

          <div>
            <label for="destination" class="block text-sm font-medium text-gray-700">Indo para</label>
            <input 
              id="destination" 
              v-model="travelRequest.destination" 
              type="text" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.destination }"
            >
            <p v-if="errors.destination" class="text-red-500 text-xs mt-1">
              {{ errors.destination }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="departureDate" class="block text-sm font-medium text-gray-700">Data da ida</label>
            <input 
              id="departureDate" 
              v-model="travelRequest.departureDate" 
              type="date" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.departureDate }"
            >
            <p v-if="errors.departureDate" class="text-red-500 text-xs mt-1">
              {{ errors.departureDate }}
            </p>
          </div>

          <div>
            <label for="returnDate" class="block text-sm font-medium text-gray-700">Data da volta</label>
            <input 
              id="returnDate" 
              v-model="travelRequest.returnDate" 
              type="date" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.returnDate }"
            >
            <p v-if="errors.returnDate" class="text-red-500 text-xs mt-1">
              {{ errors.returnDate }}
            </p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Valor Estimado</label>
            <input 
              id="amount" 
              v-model.number="travelRequest.amount" 
              type="number" 
              step="0.01" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.amount }"
            >
            <p v-if="errors.amount" class="text-red-500 text-xs mt-1">
              {{ errors.amount }}
            </p>
          </div>

          <div>
            <label for="passengers" class="block text-sm font-medium text-gray-700">Passageiros</label>
            <input 
              id="passengers" 
              v-model.number="travelRequest.passengers" 
              type="number" 
              min="1" 
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
              :class="{ 'border-red-500': errors.passengers }"
            >
            <p v-if="errors.passengers" class="text-red-500 text-xs mt-1">
              {{ errors.passengers }}
            </p>
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700">Descrição (Opcional)</label>
          <textarea 
            id="description" 
            v-model="travelRequest.description" 
            rows="3" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
          ></textarea>
        </div>

        <p v-if="errors.submit" class="text-red-500 text-sm mb-4">
          {{ errors.submit }}
        </p>

        <div class="flex justify-end space-x-4 mt-6">
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

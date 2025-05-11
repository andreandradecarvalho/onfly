<script setup lang="ts">
import { defineProps, defineEmits, ref } from 'vue';
import EditFlightTicketModal from './EditFlightTicketModal.vue';

// Inclua todos os eventos necessários aqui:
const emit = defineEmits(['edit-request', 'confirm-update-status']);

const isEditModalOpen = ref(false);
const selectedRequest = ref<TravelRequest | null>(null);

function editRequest(request: TravelRequest) {
  selectedRequest.value = { ...request };
  isEditModalOpen.value = true;
}

function handleEditSave(updatedRequest: TravelRequest) {
  emit('edit-request', updatedRequest);
  isEditModalOpen.value = false;
}

interface TravelRequest {
  id: number;
  orderId: string;
  userName: string;
  userEmail: string;
  companyName: string;
  origin: string;
  destination: string;
  departureDate: string;
  returnDate: string;
  status: string;
  createdAt: string;
  isCancellable?: boolean; // Added to reflect parent data
}

type StatusUpdatePayload = {
  id: number;
  status: 'aprovado' | 'cancelado';
};

defineProps<{
  requests: TravelRequest[];
  canUpdateStatus: boolean;
}>()

const formatDate = (dateString: string) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(date);
};

const confirmAction = (id: number, status: 'aprovado' | 'cancelado') => {
  emit('confirm-update-status', { id, status });
};
</script>

<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passageiro</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rota</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datas</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="request in requests" :key="request.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ request.id }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <span class="block font-medium text-gray-900">{{ request.userName }}</span>
            <span class="block text-xs text-gray-500">{{ request.companyName }}</span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <span class="font-semibold text-blue-700">{{ request.origin }}</span>
            <span class="mx-2 text-gray-400" title="Avião">
              <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19l7-7m0 0l-7-7m7 7H3" />
                <path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" fill="none" />
              </svg>
            </span>
            <span class="font-semibold text-blue-700">{{ request.destination }}</span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <div class="flex flex-col">
              <span class="inline-flex items-center mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 text-green-600 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                {{ formatDate(request.departureDate) }}
              </span>
              <span v-if="request.returnDate" class="inline-flex items-center mt-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 text-blue-600 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7-7l-7 7 7 7" />
                </svg>
                {{ formatDate(request.returnDate) }}
              </span>
              <span v-else class="inline-block text-gray-400 mt-1">-</span>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <span
              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
              :class="{
                'bg-yellow-100 text-yellow-800': request.status === 'solicitado' || request.status === 'pendente',
                'bg-green-100 text-green-800': request.status === 'aprovado',
                'bg-red-100 text-red-800': request.status === 'cancelado',
                'bg-gray-200 text-gray-700': !['solicitado', 'pendente', 'aprovado', 'cancelado'].includes(request.status),
              }"
            >
              {{ request.status }}
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center space-x-2">
              <div v-if="canUpdateStatus && request.status !== 'cancelado'" class="flex space-x-4">
                <button
                  v-if="request.status !== 'aprovado' && request.status !== 'cancelado'"
                  @click="editRequest(request)"
                  class="text-blue-600 hover:text-blue-800 transition-colors"
                  title="Editar"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m2-2h.01M12 20h9" />
                  </svg>
                </button>
                <button
                  v-if="request.status !== 'aprovado'"
                  @click="confirmAction(request.id, 'aprovado')"
                  class="text-green-600 hover:text-green-800 transition-colors"
                  title="Aprovar"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </button>
                <button
                  @click="confirmAction(request.id, 'cancelado')"
                  class="text-red-600 hover:text-red-800 transition-colors"
                  :class="{ 'opacity-50 cursor-not-allowed': !request.isCancellable }"
                  :disabled="!request.isCancellable"
                  title="Cancelar"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="requests.length === 0" class="text-center py-8">
      <p class="text-gray-500">Nenhuma solicitação de viagem encontrada.</p>
    </div>
  </div>
    <EditFlightTicketModal
      :open="isEditModalOpen"
      :request="selectedRequest"
      @close="isEditModalOpen = false"
      @save="handleEditSave"
    />
</template>

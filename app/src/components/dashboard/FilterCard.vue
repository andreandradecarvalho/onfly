<script lang="ts">
const statusOptions = [
  { value: 'all', label: 'Todos' },
  { value: 'pending', label: 'Pendente' },
  { value: 'approved', label: 'Aprovado' },
  { value: 'rejected', label: 'Rejeitado' },
]
</script>

<template>
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Filtros de Solicitações de Viagem</h2>
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
</template>

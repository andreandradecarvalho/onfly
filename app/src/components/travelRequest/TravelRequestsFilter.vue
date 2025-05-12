<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

defineProps<{
  filterDestination: string
  filterStartDate: string
  filterEndDate: string
  filterCreatedStartDate: string
  filterCreatedEndDate: string
  selectedStatus: string
  statusOptions: { value: string; label: string }[]
}>()

const emit = defineEmits([
  'update:filterDestination',
  'update:filterStartDate',
  'update:filterEndDate',
  'update:filterCreatedStartDate',
  'update:filterCreatedEndDate',
  'update:selectedStatus',
])
</script>

<template>
  <div class="bg-white shadow-md rounded-lg p-6 mb-6" id="travel-filter">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Solicitação de Viagem</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Destino</label>
        <input
          :value="filterDestination"
          @keyup="(e) => emit('update:filterDestination', e.target.value)"
          type="text"
          placeholder="Filtrar por destino"
          class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data da Ida</label>
        <input
          :value="filterStartDate"
          @input="(e) => { console.log('Data início selecionada:', e.target.value); emit('update:filterStartDate', e.target.value) }"
          type="date"
          placeholder="Data início"
          class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data da Volta</label>
        <input
          :value="filterEndDate"
          @input="emit('update:filterEndDate', $event.target.value)"
          type="date"
          placeholder="Data fim"
          class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select
          :value="selectedStatus"
          @change="emit('update:selectedStatus', $event.target.value)"
          class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option v-for="option in statusOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

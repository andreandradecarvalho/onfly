<script setup lang="ts">
import { ref } from 'vue';

const from = ref('');
const to = ref('');
const departDate = ref('');
const returnDate = ref('');
const passengers = ref(1);

const search = () => {
  console.log('Search flight:', { from, to, departDate, returnDate, passengers });
};
</script>

<template>
  <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl mx-auto -mt-16 relative z-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Comprar passagem</h2>
    <form @submit.prevent="search" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Saindo de</label>
          <input
            v-model="from"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Departure City"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Indo para</label>
          <input
            v-model="to"
            type="text"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Arrival City"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Data da ida</label>
          <input
            v-model="departDate"
            type="date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Data da volta</label>
          <input
            v-model="returnDate"
            type="date"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Passageiros</label>
          <input
            v-model="passengers"
            type="number"
            min="1"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
          />
        </div>
      </div>

      <button
        type="submit"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200"
      >
        Buscar voos
      </button>
    </form>
  </div>
</template>

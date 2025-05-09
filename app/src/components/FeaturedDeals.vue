<script setup lang="ts">
const deals = [
  {
    id: 1,
    destination: 'Paris',
    image: 'https://images.pexels.com/photos/699466/pexels-photo-699466.jpeg',
    price: 499,
    description: 'Experimente a cidade do amor com nossas ofertas especiais de inverno'
  },
  {
    id: 2,
    destination: 'Tokyo',
    image: 'https://images.pexels.com/photos/2506923/pexels-photo-2506923.jpeg',
    price: 799,
    description: 'Descubra a mistura de tradição e modernidade'
  },
  {
    id: 3,
    destination: 'New York',
    image: 'https://images.pexels.com/photos/802024/pexels-photo-802024.jpeg',
    price: 399,
    description: 'Visite a cidade que nunca dorme'
  }
];
</script>

<template>
  <section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-3xl font-bold text-gray-800 mb-8">Ofertas em destaque</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="deal in deals" :key="deal.id" class="bg-white rounded-lg shadow-md overflow-hidden">
          <img :src="deal.image" :alt="deal.destination" class="w-full h-48 object-cover"/>
          <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-800">{{ deal.destination }}</h3>
            <p class="text-gray-600 mt-2">{{ deal.description }}</p>
            <div class="mt-4 flex justify-between items-center">
              <span class="text-2xl font-bold text-blue-600">${{ deal.price }}</span>
              <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Book Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

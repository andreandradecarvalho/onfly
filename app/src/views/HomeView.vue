<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import SearchForm from '../components/SearchForm.vue'
import FeaturedDeals from '../components/FeaturedDeals.vue'
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />

    <!-- Hero Section -->
    <div class="relative">
      <div
        class="h-[500px] bg-cover bg-center"
        style="background-image: url('https://images.pexels.com/photos/62623/wing-plane-flying-airplane-62623.jpeg')"
      >
        <div class="absolute inset-0 bg-black bg-opacity-50">
          <div class="max-w-7xl mx-auto px-4 h-full flex items-center">
            <div class="text-white">
              <h1 class="text-4xl md:text-6xl font-bold mb-4">Explore o mundo conosco</h1>
              <p class="text-xl md:text-2xl mb-8">Encontre as melhores ofertas de voos para os destinos dos seus sonhos</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4">
      <SearchForm />
    </div>

    <FeaturedDeals />

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 class="text-xl font-bold mb-4">Onfly</h3>
            <p class="text-gray-400">Seu parceiro de confiança para viagens aéreas.</p>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-4">Quick Links</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white">Sobre nós</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Contato</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Termos e Condições</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Política de privacidade</a></li>
            </ul>
          </div>
          <div>
            <h3 class="text-xl font-bold mb-4">Fale conosco</h3>
            <ul class="space-y-2 text-gray-400">
              <li>Email: contato@onfly.com.br</li>
              <li>WhatsApp: +55 (31) 99877-9437</li>
              <li>Endereço: Rua Maranhão, 339 – 2º andar, Santa Efigênia, Belo Horizonte – MG</li>
            </ul>
          </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-400">
          <p>&copy; 2025 Onfly. Feito com muito <heart>❤</heart> e carinho no San Pedro Valley.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

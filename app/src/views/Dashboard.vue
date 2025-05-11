<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import NoLogged from '@/components/NoLogged.vue'
import Footer from '@/components/Footer.vue'
import TravelRequestsTable from '@/components/travelRequest/TravelRequestsTable.vue'
import { useAuth } from '../stores/auth'
import { ref } from 'vue'
import CreateTravelRequestModal from '@/components/travelRequest/CreateTravelRequestModal.vue'
const auth = useAuth()
console.log(auth)
const isModalOpen = ref(false)
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <NoLogged v-if="!auth.isAuthenticated()" />
    <main v-if="auth.isAuthenticated()" class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">
        Olá, {{ auth.userData.value?.name || 'Usuário' }}
      </h1>
      <div class="mb-6 flex space-x-4">
        <router-link
          v-if="auth.userData.value?.is_super_admin"
          to="/empresas"
          class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded"
          >Empresas</router-link
        >
        <router-link
          v-if="auth.userData.value?.is_super_admin || auth.userData.value?.is_admin"
          to="/pessoas"
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
          >Pessoas</router-link
        >
        <button
          @click="isModalOpen = true"
          class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors"
        >
          Nova Solicitação
        </button>
      </div>
      <TravelRequestsTable />
    </main>
    <Footer />
    <CreateTravelRequestModal v-if="isModalOpen" @close="isModalOpen = false" />
  </div>
</template>

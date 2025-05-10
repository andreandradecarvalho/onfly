<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import NoLogged from '@/components/NoLogged.vue'
import Footer from '@/components/Footer.vue'
import TravelRequestsTable from '@/components/TravelRequestsTable.vue'
import { useAuth } from '../stores/auth'
const auth = useAuth()
console.log(auth)
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
        <router-link v-if="auth.userData.value?.is_super_admin" to="/empresas" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Empresas</router-link>
        <router-link v-if="auth.userData.value?.is_super_admin || auth.userData.value?.is_admin" to="/pessoas" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Pessoas</router-link>
      </div>
      <TravelRequestsTable />
    </main>
    <Footer />
  </div>
</template>

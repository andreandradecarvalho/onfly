<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import NoLogged from '@/components/NoLogged.vue';
import Footer from '@/components/Footer.vue';
import IsLogged from '@/components/IsLogged.vue';
import { useAuth } from '../stores/auth';
const auth = useAuth();
console.log(auth);
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <NoLogged v-if="!auth.isAuthenticated()"/>
    <IsLogged v-if="auth.isAuthenticated()"/>
    <Footer />
  </div>
</template>

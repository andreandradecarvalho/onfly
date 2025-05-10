<script setup lang="ts"></script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">
      {{ isNewForm ? 'Nova Empresa' : 'Editar Empresa' }}
    </h1>
    <form @submit.prevent="submitForm" class="max-w-lg mx-auto">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="nome"> Nome da Empresa</label>
        <input v-model="empresa.nome" type="text" id="nome" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="cnpj">CNPJ</label>
        <input v-model="empresa.cnpj" type="text" id="cnpj" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="endereco">Endereço </label>
        <input v-model="empresa.endereco" type="text" id="endereco" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email </label>
        <input v-model="empresa.email" type="email" id="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" >
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="responsavel">Responsável</label>
        <input v-model="empresa.responsavel" type="text" id="responsavel" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" >
      </div>
      <div class="flex items-center justify-between space-x-4">
        <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" >
          {{ isNewForm ? 'Criar Empresa' : 'Atualizar Empresa' }}
        </button>
        <button type="button" @click="cancelForm" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Cancelar</button>
      </div>
    </form>
  </div>
</template>

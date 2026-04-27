<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2">Clientes</h3>
                <p class="text-3xl text-blue-600">{{ totalClientes }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2">Fornecedores</h3>
                <p class="text-3xl text-green-600">{{ totalFornecedores }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-2">Contactos</h3>
                <p class="text-3xl text-purple-600">{{ totalContactos }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const totalClientes = ref(0)
const totalFornecedores = ref(0)
const totalContactos = ref(0)

// Depois vamos buscar estes valores da API
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md">
            <div class="p-4 border-b">
                <h2 class="text-xl font-bold">Gestão App</h2>
            </div>

            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <router-link to="/" class="block p-2 rounded hover:bg-gray-100">
                            Dashboard
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/clientes" class="block p-2 rounded hover:bg-gray-100">
                            Clientes
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/fornecedores" class="block p-2 rounded hover:bg-gray-100">
                            Fornecedores
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/configuracoes/paises" class="block p-2 rounded hover:bg-gray-100">
                            Países
                        </router-link>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold">{{ pageTitle }}</h1>
                    <div class="flex items-center space-x-4">
                        <span>{{ user?.name }}</span>
                        <button @click="logout" class="text-red-600">Sair</button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-6">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const user = window.Laravel?.user || { name: 'Admin' }

const pageTitle = computed(() => {
    const titles = {
        '/': 'Dashboard',
        '/clientes': 'Clientes',
        '/fornecedores': 'Fornecedores',
        '/configuracoes/paises': 'Configurações - Países'
    }
    return titles[route.path] || 'Gestão'
})

const logout = () => {
    // Implementar logout depois
    window.location.href = '/logout'
}
</script>

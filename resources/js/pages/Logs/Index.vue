<template>
    <AppLayout title="Logs do Sistema">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Registo de Atividades</h1>

                <!-- Filtros -->
                <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                            <input
                                type="date"
                                v-model="filtros.data_inicio"
                                @change="aplicarFiltros"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                            <input
                                type="date"
                                v-model="filtros.data_fim"
                                @change="aplicarFiltros"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Utilizador</label>
                            <select
                                v-model="filtros.utilizador_id"
                                @change="aplicarFiltros"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            >
                                <option value="">Todos</option>
                                <option v-for="user in utilizadores" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ação</label>
                            <input
                                type="text"
                                v-model="filtros.acao"
                                @change="aplicarFiltros"
                                placeholder="Pesquisar ação..."
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button
                            @click="limparFiltros"
                            class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded"
                        >
                            Limpar Filtros
                        </button>
                    </div>
                </div>

                <!-- Tabela de Logs -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilizador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ação</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispositivo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs" :key="log.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.data }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.hora }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.utilizador }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                                {{ log.menu }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-md break-words">{{ log.acao }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.dispositivo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.ip }}</td>
                                    </tr>
                                    <tr v-if="logs.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum registo de atividade encontrado.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    logs: {
        type: Array,
        default: () => []
    },
    utilizadores: {
        type: Array,
        default: () => []
    },
    filtros: {
        type: Object,
        default: () => ({})
    },
    pagination: {
        type: Object,
        default: () => ({})
    }
});

const filtros = ref({
    data_inicio: props.filtros?.data_inicio || '',
    data_fim: props.filtros?.data_fim || '',
    utilizador_id: props.filtros?.utilizador_id || '',
    acao: props.filtros?.acao || ''
});

const aplicarFiltros = () => {
    router.get('/logs', filtros.value, {
        preserveState: true,
        preserveScroll: true
    });
};

const limparFiltros = () => {
    filtros.value = {
        data_inicio: '',
        data_fim: '',
        utilizador_id: '',
        acao: ''
    };
    aplicarFiltros();
};
</script>

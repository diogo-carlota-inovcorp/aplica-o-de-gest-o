<template>
    <AppLayout title="Encomendas">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Encomendas</h1>
                    <div class="space-x-3">
                        <a href="/encomendas/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Nova Encomenda
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="encomenda in encomendas" :key="encomenda.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ encomenda.data_encomenda }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ encomenda.numero }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ encomenda.cliente?.nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            € {{ Number(encomenda.total).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs': encomenda.estado === 'rascunho',
                                                'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs': encomenda.estado === 'fechado'
                                            }">
                                                {{ encomenda.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a :href="`/encomendas/${encomenda.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <a :href="`/encomendas/${encomenda.id}/pdf`" target="_blank" class="text-green-600 hover:text-green-900">PDF</a>
                                            <button @click="converterParaFornecedores(encomenda.id)" v-if="encomenda.estado === 'fechado'" class="text-purple-600 hover:text-purple-900">
                                                Encomendar Fornecedores
                                            </button>
                                            <button @click="destroy(encomenda.id)" class="text-red-600 hover:text-red-900">Excluir</button>
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

import { router } from '@inertiajs/vue3';

defineProps({
    encomendas: Array
});

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir esta encomenda?')) {
        router.delete(`/encomendas/${id}`);
    }
};

const converterParaFornecedores = (id) => {
    if (confirm('Converter esta encomenda em encomendas por fornecedor?')) {
        router.post(`/encomendas/${id}/converter-fornecedores`);
    }
};
</script>

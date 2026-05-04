<template>
    <AppLayout title="Propostas">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Propostas</h1>
                    <a
                        href="/propostas/create"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Nova Proposta
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="proposta in propostas" :key="proposta.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ proposta.data_proposta }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ proposta.numero }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ proposta.validade }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ proposta.cliente?.nome }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            € {{ Number(proposta.total).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs': proposta.estado === 'rascunho',
                                                'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs': proposta.estado === 'fechado'
                                            }">
                                                {{ proposta.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a :href="`/propostas/${proposta.id}/edit`" class="text-blue-600 hover:text-blue-900 mr-3">
                                                Editar
                                            </a>
                                            <button @click="destroy(proposta.id)" class="text-red-600 hover:text-red-900">
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="propostas.length === 0">
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            Nenhuma proposta cadastrada.
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
    propostas: {
        type: Array,
        default: () => []
    }
});

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir esta proposta?')) {
        router.delete(`/propostas/${id}`);
    }
};
</script>

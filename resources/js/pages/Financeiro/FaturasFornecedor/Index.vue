<template>
    <AppLayout title="Faturas de Fornecedor">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Faturas de Fornecedor</h1>
                    <a href="/faturas-fornecedor/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Nova Fatura
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encomenda</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="fatura in faturas" :key="fatura.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ fatura.data_fatura }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ fatura.numero }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ fatura.fornecedor?.nome }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ fatura.encomenda_fornecedor?.numero_fornecedor || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a v-if="fatura.documento" :href="'/storage/' + fatura.documento" target="_blank" class="text-blue-600 hover:text-blue-900">
                                                Ver Documento
                                            </a>
                                            <span v-else class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            € {{ Number(fatura.valor_total).toFixed(2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs': fatura.estado === 'pendente',
                                                'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs': fatura.estado === 'paga'
                                            }">
                                                {{ fatura.estado === 'pendente' ? 'Pendente' : 'Paga' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button
                                                v-if="fatura.estado === 'paga' && !fatura.comprovativo_pagamento"
                                                @click="abrirModal(fatura)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Enviar Comprovativo
                                            </button>

                                            <a :href="`/faturas-fornecedor/${fatura.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <button @click="destroy(fatura.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                        </td>
                                    </tr>
                                    <tr v-if="faturas.length === 0">
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                            Nenhuma fatura cadastrada.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal - Adicione isto fora do div py-6 -->
        <EnviarComprovativoModal
            v-if="faturaSelecionada"
            :fatura="faturaSelecionada"
            @close="faturaSelecionada = null"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'; // ← Importe o ref
import { router } from '@inertiajs/vue3';
import EnviarComprovativoModal from './EnviarComprovativo.vue'; // ← Importe o modal

const props = defineProps({
    faturas: {
        type: Array,
        default: () => []
    }
});

const faturaSelecionada = ref(null); // ← Adicione esta linha

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir esta fatura?')) {
        router.delete(`/faturas-fornecedor/${id}`);
    }
};

const abrirModal = (fatura) => { // ← Adicione esta função
    console.log('Abrindo modal para fatura:', fatura.numero);
    faturaSelecionada.value = fatura;
};
</script>

<template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Artigos</h1>
                    <a
                        href="/artigos/create"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Novo Artigo
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Foto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="artigo in artigos" :key="artigo.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ artigo.referencia }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <img v-if="artigo.foto" :src="'/storage/' + artigo.foto" class="h-10 w-10 object-cover rounded">
                                            <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center text-gray-400">Sem foto</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ artigo.nome }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ artigo.descricao }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">€ {{ Number(artigo.preco).toFixed(2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a :href="`/artigos/${artigo.id}/edit`" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                                            <button @click="destroy(artigo.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                                        </td>
                                    </tr>
                                    <tr v-if="artigos.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum artigo cadastrado.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';

defineProps({
    artigos: {
        type: Array,
        default: () => []
    }
});

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir este artigo?')) {
        router.delete(`/artigos/${id}`);
    }
};
</script>

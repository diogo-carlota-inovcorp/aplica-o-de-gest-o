<template>
    <AppLayout title="Editar Encomenda">
        <div class="py-6">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Editar Encomenda</h1>
                    <a href="/encomendas" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Dados Básicos -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                                <input v-model="form.numero" type="text" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                                <select v-model="form.cliente_id" class="w-full rounded-md border-gray-300 px-3 py-2" required>
                                    <option value="">Selecione...</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.nome }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data Encomenda *</label>
                                <input type="date" v-model="form.data_encomenda" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select v-model="form.estado" class="w-full rounded-md border-gray-300 px-3 py-2">
                                    <option value="rascunho">Rascunho</option>
                                    <option value="fechado">Fechado</option>
                                </select>
                            </div>
                        </div>

                        <!-- Linhas de Artigos -->
                        <div>
                            <h2 class="text-lg font-medium mb-4">Artigos</h2>
                            <div v-for="(linha, index) in form.linhas" :key="index" class="border rounded-lg p-4 mb-4">
                                <div class="flex justify-between mb-3">
                                    <h3 class="font-medium">Artigo #{{ index + 1 }}</h3>
                                    <button type="button" @click="removerLinha(index)" class="text-red-600">Remover</button>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Artigo *</label>
                                        <select v-model="linha.artigo_id" class="w-full rounded-md border-gray-300 px-3 py-2">
                                            <option value="">Selecione...</option>
                                            <option v-for="artigo in artigos" :key="artigo.id" :value="artigo.id">
                                                {{ artigo.referencia }} - {{ artigo.nome }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor *</label>
                                        <select v-model="linha.fornecedor_id" class="w-full rounded-md border-gray-300 px-3 py-2" required>
                                            <option value="">Selecione...</option>
                                            <option v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id">
                                                {{ fornecedor.nome }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Preço Custo *</label>
                                        <input type="number" step="0.01" v-model="linha.preco_custo" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantidade *</label>
                                        <input type="number" min="1" v-model="linha.quantidade" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                                        <input type="text" disabled :value="linha.subtotal" class="w-full rounded-md bg-gray-100 px-3 py-2" />
                                    </div>
                                </div>
                            </div>
                            <button type="button" @click="adicionarLinha" class="w-full border-2 border-dashed border-gray-300 rounded-lg py-2 text-gray-600 hover:border-blue-500 hover:text-blue-500">
                                + Adicionar Artigo
                            </button>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center pt-4 border-t">
                            <h2 class="text-lg font-medium">Total da Encomenda</h2>
                            <p class="text-2xl font-bold">€ {{ totalGeral.toFixed(2) }}</p>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="form.processing">
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    encomenda: Object,
    clientes: Array,
    fornecedores: Array,
    artigos: Array
});

const form = useForm({
    numero: props.encomenda.numero,
    data_encomenda: props.encomenda.data_encomenda,
    cliente_id: props.encomenda.cliente_id,
    estado: props.encomenda.estado,
    linhas: props.encomenda.linhas.map(linha => ({
        artigo_id: linha.artigo_id,
        fornecedor_id: linha.fornecedor_id,
        preco_custo: linha.preco_custo,
        quantidade: linha.quantidade,
        subtotal: linha.subtotal
    }))
});

const adicionarLinha = () => {
    form.linhas.push({
        artigo_id: null,
        fornecedor_id: null,
        preco_custo: 0,
        quantidade: 1,
        subtotal: 0
    });
};

const removerLinha = (index) => {
    form.linhas.splice(index, 1);
};

const totalGeral = computed(() => {
    return form.linhas.reduce((total, linha) => total + (linha.preco_custo * linha.quantidade), 0);
});

const submit = () => {
    form.put(`/encomendas/${props.encomenda.id}`);
};
</script>

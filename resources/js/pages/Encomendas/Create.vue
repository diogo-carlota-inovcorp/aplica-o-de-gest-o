<template>
    <AppLayout title="Nova Encomenda">
        <div class="py-6">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Nova Encomenda</h1>
                    <a href="/encomendas" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Dados Básicos -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium mb-4">Dados da Encomenda</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número *</label>
                                <input v-model="form.numero" type="text" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cliente *</label>
                                <select v-model="form.cliente_id" class="w-full rounded-md border-gray-300 px-3 py-2" required>
                                    <option value="">Selecione...</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.nome }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Data Encomenda *</label>
                                <input type="date" v-model="form.data_encomenda" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                            </div>
                        </div>
                    </div>

                    <!-- Linhas de Artigos (similar às propostas) -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium mb-4">Artigos</h2>

                        <div v-for="(linha, index) in form.linhas" :key="index" class="border rounded-lg p-4 mb-4">
                            <div class="flex justify-between mb-3">
                                <h3 class="font-medium">Artigo #{{ index + 1 }}</h3>
                                <button type="button" @click="removerLinha(index)" class="text-red-600">Remover</button>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Artigo *</label>
                                    <select v-model="linha.artigo_id" @change="buscarArtigo(linha)" class="w-full rounded-md border-gray-300 px-3 py-2">
                                        <option value="">Selecione...</option>
                                        <option v-for="artigo in artigos" :key="artigo.id" :value="artigo.id">
                                            {{ artigo.referencia }} - {{ artigo.nome }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fornecedor *</label>
                                    <select v-model="linha.fornecedor_id" class="w-full rounded-md border-gray-300 px-3 py-2" required>
                                        <option value="">Selecione...</option>
                                        <option v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id">
                                            {{ fornecedor.nome }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Preço Custo *</label>
                                    <input type="number" step="0.01" v-model="linha.preco_custo" @input="calcularSubtotal(linha)" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantidade *</label>
                                    <input type="number" min="1" v-model="linha.quantidade" @input="calcularSubtotal(linha)" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                    <input type="text" disabled :value="linha.subtotal" class="w-full rounded-md bg-gray-100 px-3 py-2" />
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="adicionarLinha" class="w-full border-2 border-dashed border-gray-300 rounded-lg py-2 text-gray-600 hover:border-blue-500 hover:text-blue-500">
                            + Adicionar Artigo
                        </button>
                    </div>

                    <!-- Total -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-medium">Total da Encomenda</h2>
                            <p class="text-2xl font-bold">€ {{ totalGeral.toFixed(2) }}</p>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="salvarRascunho" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                            Salvar Rascunho
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                            Fechar Encomenda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    clientes: Array,
    fornecedores: Array,
    artigos: Array
});

const form = useForm({
    numero: '',
    data_encomenda: new Date().toISOString().split('T')[0],
    cliente_id: '',
    estado: 'rascunho',
    linhas: []
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

const calcularSubtotal = (linha) => {
    linha.subtotal = (linha.preco_custo || 0) * (linha.quantidade || 0);
};

const totalGeral = computed(() => {
    return form.linhas.reduce((total, linha) => total + (linha.subtotal || 0), 0);
});

const buscarArtigo = (linha) => {
    const artigo = props.artigos.find(a => a.id === linha.artigo_id);
    if (artigo) {
        linha.preco_custo = artigo.preco || 0;
        calcularSubtotal(linha);
    }
};

const salvarRascunho = () => {
    form.estado = 'rascunho';
    form.post('/encomendas');
};

const submit = () => {
    if (form.linhas.length === 0) {
        alert('Adicione pelo menos um artigo.');
        return;
    }
    form.estado = 'fechado';
    form.post('/encomendas');
};
</script>

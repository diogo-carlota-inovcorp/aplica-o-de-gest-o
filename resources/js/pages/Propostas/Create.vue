<template>
    <AppLayout title="Nova Proposta">
        <div class="py-6">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Dados Básicos -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium mb-4">Dados da Proposta</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label>Número *</Label>
                                <Input v-model="form.numero" required />
                            </div>
                            <div>
                                <Label>Cliente *</Label>
                                <Select v-model="form.cliente_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione um cliente" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                            {{ cliente.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div>
                                <Label>Data da Proposta *</Label>
                                <Input type="date" v-model="form.data_proposta" required />
                            </div>
                            <div>
                                <Label>Validade *</Label>
                                <Input type="date" v-model="form.validade" required />
                            </div>
                        </div>
                    </div>

                    <!-- Linhas de Artigos -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium mb-4">Artigos</h2>
                        <div class="space-y-4">
                            <div v-for="(linha, index) in form.linhas" :key="index"
                                 class="border rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <h3 class="font-medium">Artigo #{{ index + 1 }}</h3>
                                    <Button type="button" variant="destructive" size="sm"
                                            @click="removerLinha(index)">
                                        Remover
                                    </Button>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <Label>Artigo *</Label>
                                        <Select v-model="linha.artigo_id" @update:model-value="buscarArtigo(linha, index)">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Buscar por ref/nome" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="artigo in artigos" :key="artigo.id" :value="artigo.id">
                                                    {{ artigo.referencia }} - {{ artigo.nome }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div>
                                        <Label>Fornecedor</Label>
                                        <Select v-model="linha.fornecedor_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione..." />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem v-for="forn in fornecedores" :key="forn.id" :value="forn.id">
                                                    {{ forn.nome }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div>
                                        <Label>Preço de Custo *</Label>
                                        <Input type="number" step="0.01" v-model="linha.preco_custo"
                                               @input="calcularSubtotal(linha)" />
                                    </div>
                                    <div>
                                        <Label>Quantidade *</Label>
                                        <Input type="number" min="1" v-model="linha.quantidade"
                                               @input="calcularSubtotal(linha)" />
                                    </div>
                                    <div>
                                        <Label>Subtotal</Label>
                                        <Input type="text" disabled :value="linha.subtotal" class="bg-gray-100" />
                                    </div>
                                </div>
                            </div>
                            <Button type="button" @click="adicionarLinha" variant="outline" class="w-full">
                                + Adicionar Artigo
                            </Button>
                        </div>
                    </div>

                    <!-- Totais e Ações -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-medium">Total da Proposta</h2>
                            <p class="text-2xl font-bold">€ {{ totalGeral.toFixed(2) }}</p>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="salvarRascunho"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded"
                            >
                                Salvar Rascunho
                            </button>
                            <button
                                type="button"
                                @click="fecharProposta"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded"
                            >
                                Fechar Proposta
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Button } from '@/Components/ui/button';

const props = defineProps({
    clientes: Array,
    artigos: Array,
    fornecedores: Array
});

const form = useForm({
    numero: '',
    data_proposta: new Date().toISOString().split('T')[0],
    validade: '',
    cliente_id: '',
    estado: 'rascunho', // ← Adicionado
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

const buscarArtigo = (linha, index) => {
    const artigo = props.artigos.find(a => a.id === linha.artigo_id);
    if (artigo) {
        linha.preco_custo = artigo.preco || 0;
        calcularSubtotal(linha);
    }
};

const salvarRascunho = () => {
    form.estado = 'rascunho';
    form.post('/propostas');
};

const fecharProposta = () => {
    if (form.linhas.length === 0) {
        alert('Adicione pelo menos um artigo antes de fechar a proposta.');
        return;
    }
    form.estado = 'fechado';
    form.post('/propostas');
};

// Definir validade padrão (30 dias após data proposta)
import { watch } from 'vue';
watch(() => form.data_proposta, () => {
    const data = new Date(form.data_proposta);
    data.setDate(data.getDate() + 30);
    form.validade = data.toISOString().split('T')[0];
});
</script>

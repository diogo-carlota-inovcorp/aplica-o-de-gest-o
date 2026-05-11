<template>
    <AppLayout title="Nova Fatura Fornecedor">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Nova Fatura</h1>
                    <a href="/faturas-fornecedor" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Número -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                            <input
                                v-model="form.numero"
                                type="text"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.numero" class="text-red-600 text-sm mt-1">
                                {{ form.errors.numero }}
                            </div>
                        </div>

                        <!-- Data da Fatura e Vencimento -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data da Fatura *</label>
                                <input
                                    type="date"
                                    v-model="form.data_fatura"
                                    @change="calcularVencimento"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data de Vencimento *</label>
                                <input
                                    type="date"
                                    v-model="form.data_vencimento"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Fornecedor e Encomenda -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor *</label>
                                <select
                                    v-model="form.fornecedor_id"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option v-for="fornecedor in fornecedores" :key="fornecedor.id" :value="fornecedor.id">
                                        {{ fornecedor.nome }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Encomenda Fornecedor</label>
                                <select
                                    v-model="form.encomenda_fornecedor_id"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                >
                                    <option value="">Selecione...</option>
                                    <option v-for="encomenda in encomendasFornecedor" :key="encomenda.id" :value="encomenda.id">
                                        {{ encomenda.numero_fornecedor }} - € {{ encomenda.total }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Valor Total -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor Total (€) *</label>
                            <input
                                type="number"
                                step="0.01"
                                v-model="form.valor_total"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.valor_total" class="text-red-600 text-sm mt-1">
                                {{ form.errors.valor_total }}
                            </div>
                        </div>

                        <!-- Documento Anexo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Documento (PDF, JPEG, PNG)</label>
                            <input
                                type="file"
                                @change="handleDocumentoChange"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-gray-900"
                            />
                            <div v-if="documentoPreview" class="mt-2">
                                <span class="text-sm text-gray-600">Ficheiro selecionado: {{ documentoPreview }}</span>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select v-model="form.estado" class="w-full rounded-md border-gray-300 px-3 py-2">
                                <option value="pendente">Pendente de Pagamento</option>
                                <option value="paga">Paga</option>
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                :disabled="form.processing"
                            >
                                Salvar
                            </button>
                            <a
                                href="/faturas-fornecedor"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    fornecedores: Array,
    encomendasFornecedor: Array
});

const form = useForm({
    numero: '',
    data_fatura: new Date().toISOString().split('T')[0],
    data_vencimento: '',
    fornecedor_id: '',
    encomenda_fornecedor_id: '',
    valor_total: '',
    documento: null,
    estado: 'pendente'
});

const documentoPreview = ref(null);

const handleDocumentoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.documento = file;
        documentoPreview.value = file.name;
    }
};

const calcularVencimento = () => {
    if (form.data_fatura) {
        const data = new Date(form.data_fatura);
        data.setDate(data.getDate() + 30); // 30 dias para vencimento
        form.data_vencimento = data.toISOString().split('T')[0];
    }
};

const submit = () => {
    form.post('/faturas-fornecedor', {
        forceFormData: true,
    });
};
</script>

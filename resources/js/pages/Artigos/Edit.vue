<template>
    <AppLayout title="Editar Artigo">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Editar Artigo</h1>
                    <a href="/artigos" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Referência -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Referência *</label>
                            <input
                                v-model="form.referencia"
                                type="text"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.referencia" class="text-red-600 text-sm mt-1">
                                {{ form.errors.referencia }}
                            </div>
                        </div>

                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input
                                v-model="form.nome"
                                type="text"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">
                                {{ form.errors.nome }}
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <textarea
                                v-model="form.descricao"
                                rows="3"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            ></textarea>
                        </div>

                        <!-- Preço e IVA -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço (€) *</label>
                                <input
                                    v-model="form.preco"
                                    type="number"
                                    step="0.01"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                    required
                                />
                                <div v-if="form.errors.preco" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.preco }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IVA *</label>
                                <select
                                    v-model="form.iva_id"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                    required
                                >
                                    <option value="">Selecione...</option>
                                    <option v-for="iva in ivas" :key="iva.id" :value="iva.id">
                                        {{ iva.percentagem }}% - {{ iva.nome }}
                                    </option>
                                </select>
                                <div v-if="form.errors.iva_id" class="text-red-600 text-sm mt-1">
                                    {{ form.errors.iva_id }}
                                </div>
                            </div>
                        </div>

                        <!-- Foto atual -->
                        <div v-if="artigo.foto">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto atual</label>
                            <img :src="'/storage/' + artigo.foto" class="h-32 w-32 object-cover rounded" />
                        </div>

                        <!-- Nova Foto -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nova Foto</label>
                            <input
                                type="file"
                                @change="handleFotoChange"
                                accept="image/*"
                                class="w-full text-gray-900"
                            />
                            <div v-if="fotoPreview" class="mt-2">
                                <img :src="fotoPreview" class="h-32 w-32 object-cover rounded" />
                            </div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea
                                v-model="form.observacoes"
                                rows="3"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            ></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="flex items-center">
                            <input
                                v-model="form.estado"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600"
                            />
                            <label class="ml-2 text-sm text-gray-700">Ativo</label>
                        </div>

                        <!-- Botões -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                :disabled="form.processing"
                            >
                                Atualizar
                            </button>
                            <a
                                href="/artigos"
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
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artigo: {
        type: Object,
        required: true
    },
    ivas: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    referencia: props.artigo.referencia,
    nome: props.artigo.nome,
    descricao: props.artigo.descricao,
    preco: props.artigo.preco,
    iva_id: props.artigo.iva_id,
    foto: null,
    observacoes: props.artigo.observacoes,
    estado: props.artigo.estado,
});

const fotoPreview = ref(null);

const handleFotoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.foto = file;
        fotoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.put(`/artigos/${props.artigo.id}`, {
        forceFormData: true,
    });
};
</script>

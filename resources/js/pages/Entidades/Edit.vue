<template>
    <AppLayout :title="`Editar ${entidade.tipo === 'cliente' ? 'Cliente' : 'Fornecedor'}`">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Editar {{ entidade.tipo === 'cliente' ? 'Cliente' : 'Fornecedor' }}
                    </h1>
                    <a
                        :href="`/entidades?tipo=${entidade.tipo}`"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- NIF -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIF *</label>
                            <input
                                v-model="form.nif"
                                type="text"
                                maxlength="9"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.nif" class="text-red-600 text-sm mt-1">
                                {{ form.errors.nif }}
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

                        <!-- Morada -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Morada</label>
                            <textarea
                                v-model="form.morada"
                                rows="3"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            ></textarea>
                        </div>

                        <!-- Código Postal e Localidade -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                                <input
                                    v-model="form.codigo_postal"
                                    type="text"
                                    placeholder="1234-567"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Localidade</label>
                                <input
                                    v-model="form.localidade"
                                    type="text"
                                    class="w-full rounded-md border-gray-300 px-3 py-2"
                                />
                            </div>
                        </div>

                        <!-- País -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                            <select v-model="form.pais_id" class="w-full rounded-md border-gray-300 px-3 py-2">
                                <option value="">Selecione...</option>
                                <option v-for="pais in paises" :key="pais.id" :value="pais.id">
                                    {{ pais.nome }}
                                </option>
                            </select>
                        </div>

                        <!-- Contactos -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input v-model="form.telefone" type="tel" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telemóvel</label>
                                <input v-model="form.telemovel" type="tel" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input v-model="form.website" type="url" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>
                        </div>

                        <!-- RGPD -->
                        <div class="flex items-center">
                            <input v-model="form.consentimento_rgpd" type="checkbox" class="rounded border-gray-300 text-blue-600" />
                            <label class="ml-2 text-sm text-gray-700">Consentimento RGPD</label>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea v-model="form.observacoes" rows="3" class="w-full rounded-md border-gray-300 px-3 py-2"></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="flex items-center">
                            <input v-model="form.estado" type="checkbox" class="rounded border-gray-300 text-blue-600" />
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
                                :href="`/entidades?tipo=${entidade.tipo}`"
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

const props = defineProps({
    entidade: Object,
    paises: Array
});

const form = useForm({
    nif: props.entidade.nif,
    nome: props.entidade.nome,
    morada: props.entidade.morada,
    codigo_postal: props.entidade.codigo_postal,
    localidade: props.entidade.localidade,
    pais_id: props.entidade.pais_id,
    telefone: props.entidade.telefone,
    telemovel: props.entidade.telemovel,
    website: props.entidade.website,
    email: props.entidade.email,
    consentimento_rgpd: props.entidade.consentimento_rgpd,
    observacoes: props.entidade.observacoes,
    estado: props.entidade.estado,
});

const submit = () => {
    form.put(`/entidades/${props.entidade.id}`);
};
</script>

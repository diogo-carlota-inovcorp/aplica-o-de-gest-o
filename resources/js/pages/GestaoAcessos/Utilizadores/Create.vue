<template>
    <AppLayout title="Novo Utilizador">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Novo Utilizador</h1>
                    <a href="/users" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Telemóvel -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telemóvel</label>
                            <input
                                v-model="form.telemovel"
                                type="tel"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            />
                            <div v-if="form.errors.telemovel" class="text-red-600 text-sm mt-1">
                                {{ form.errors.telemovel }}
                            </div>
                        </div>

                        <!-- Grupo de Permissões -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grupo de Permissões *</label>
                            <select
                                v-model="form.grupo_permissao_id"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            >
                                <option value="">Selecione...</option>
                                <option v-for="grupo in grupos" :key="grupo.id" :value="grupo.id">
                                    {{ grupo.nome }}
                                </option>
                            </select>
                            <div v-if="form.errors.grupo_permissao_id" class="text-red-600 text-sm mt-1">
                                {{ form.errors.grupo_permissao_id }}
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
                            <div v-if="form.errors.password" class="text-red-600 text-sm mt-1">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Confirmar Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Password *</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                                required
                            />
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
                                Salvar
                            </button>
                            <a
                                href="/users"
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
    grupos: Array
});

const form = useForm({
    name: '',
    email: '',
    telemovel: '',
    grupo_permissao_id: '',
    password: '',
    password_confirmation: '',
    estado: true,
});

const submit = () => {
    form.post('/users');
};
</script>

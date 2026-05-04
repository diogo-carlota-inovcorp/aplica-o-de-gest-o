<template>
    <AppLayout title="Editar Utilizador">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Editar Utilizador</h1>
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
                        </div>

                        <!-- Password (opcional na edição) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nova Password (opcional)</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
                            />
                            <div class="text-xs text-gray-500 mt-1">Deixe em branco para manter a password atual</div>
                        </div>

                        <div v-if="form.password">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Password</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="w-full rounded-md border-gray-300 px-3 py-2"
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
                                Atualizar
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
    user: Object,
    grupos: Array
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    telemovel: props.user.telemovel,
    grupo_permissao_id: props.user.grupo_permissao_id,
    password: '',
    password_confirmation: '',
    estado: props.user.estado,
});

const submit = () => {
    form.put(`/users/${props.user.id}`);
};
</script>

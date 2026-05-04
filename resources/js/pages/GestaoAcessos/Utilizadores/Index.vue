<template>
    <AppLayout title="Utilizadores">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Utilizadores</h1>
                    <a href="/users/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Novo Utilizador
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telemóvel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.telemovel || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.grupo_permissao?.nome || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs': user.estado,
                                                'bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs': !user.estado
                                            }">
                                                {{ user.estado ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a :href="`/users/${user.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <button
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="destroy(user.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Excluir
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum utilizador cadastrado.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>

import { router } from '@inertiajs/vue3';

defineProps({
    users: Array
});

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir este utilizador?')) {
        router.delete(`/users/${id}`);
    }
};
</script>

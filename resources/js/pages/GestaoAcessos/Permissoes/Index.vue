<template>
    <AppLayout title="Permissões">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Grupos de Permissões</h1>
                    <a href="/permissoes/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Novo Grupo
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome do Grupo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilizadores Relacionados</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="role in roles" :key="role.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ role.name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ role.users_count || 0 }} utilizador(es)
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                Ativo
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a :href="`/permissoes/${role.id}/edit`" class="text-blue-600 hover:text-blue-900">Editar</a>
                                            <button
                                                v-if="role.name !== 'Administrador'"
                                                @click="destroy(role.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Excluir
                                            </button>
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
    roles: Array
});

const destroy = (id) => {
    if (confirm('Tem certeza que deseja excluir este grupo?')) {
        router.delete(`/permissoes/${id}`);
    }
};
</script>

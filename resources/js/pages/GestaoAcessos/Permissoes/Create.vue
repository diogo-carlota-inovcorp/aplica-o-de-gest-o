<template>
    <AppLayout title="Novo Grupo de Permissões">
        <div class="py-6">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Novo Grupo de Permissões</h1>
                    <a href="/permissoes" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Voltar
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Nome do Grupo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Grupo *</label>
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

                        <!-- Permissões agrupadas por módulo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Permissões</label>
                            <div class="space-y-4">
                                <!-- Clientes -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('clientes')"
                                            :checked="todosSelecionados('clientes')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Clientes</h3>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`clientes.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Fornecedores -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('fornecedores')"
                                            :checked="todosSelecionados('fornecedores')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Fornecedores</h3>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`fornecedores.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Artigos -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('artigos')"
                                            :checked="todosSelecionados('artigos')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Artigos</h3>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`artigos.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Propostas -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('propostas')"
                                            :checked="todosSelecionados('propostas')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Propostas</h3>
                                    </div>
                                    <div class="grid grid-cols-5 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete', 'fechar']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`propostas.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Encomendas -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('encomendas')"
                                            :checked="todosSelecionados('encomendas')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Encomendas</h3>
                                    </div>
                                    <div class="grid grid-cols-5 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete', 'fechar']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`encomendas.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Utilizadores -->
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <input
                                            type="checkbox"
                                            @change="selecionarTodos('users')"
                                            :checked="todosSelecionados('users')"
                                            class="mr-2"
                                        />
                                        <h3 class="font-medium text-gray-900">Utilizadores</h3>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 ml-6">
                                        <label v-for="acao in ['view', 'create', 'edit', 'delete']" :key="acao" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="`users.${acao}`"
                                                v-model="form.permissions"
                                                class="mr-2"
                                            />
                                            <span class="text-sm">{{ getNomeAcao(acao) }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                                href="/permissoes"
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
import { computed } from 'vue';

const props = defineProps({
    permissions: Array
});

const form = useForm({
    name: '',
    permissions: [],
    estado: true,
});

const getNomeAcao = (acao) => {
    const acoes = {
        view: 'Visualizar',
        create: 'Criar',
        edit: 'Editar',
        delete: 'Excluir',
        fechar: 'Fechar',
        pagar: 'Pagar'
    };
    return acoes[acao] || acao;
};

const getPermissoesPorModulo = (modulo) => {
    return props.permissions
        .filter(p => p.name.startsWith(`${modulo}.`))
        .map(p => p.name);
};

const selecionarTodos = (modulo) => {
    const permissoesModulo = getPermissoesPorModulo(modulo);
    const todosSelecionados = permissoesModulo.every(p => form.permissions.includes(p));

    if (todosSelecionados) {
        form.permissions = form.permissions.filter(p => !permissoesModulo.includes(p));
    } else {
        permissoesModulo.forEach(p => {
            if (!form.permissions.includes(p)) {
                form.permissions.push(p);
            }
        });
    }
};

const todosSelecionados = (modulo) => {
    const permissoesModulo = getPermissoesPorModulo(modulo);
    return permissoesModulo.length > 0 && permissoesModulo.every(p => form.permissions.includes(p));
};

const submit = () => {
    form.post('/permissoes');
};
</script>

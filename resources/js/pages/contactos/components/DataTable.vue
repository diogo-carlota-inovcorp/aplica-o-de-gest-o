<script setup lang="ts">
defineProps<{
    data: any[]
}>()

const emit = defineEmits<{
    delete: [id: number]
}>()
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apelido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Função</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telemóvel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr v-for="item in data" :key="item.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.nome }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.apelido || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.funcao?.nome || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.entidade?.nome || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.telefone || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.telemovel || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ item.email || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a :href="`/contactos/${item.id}/edit`" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                        <button @click="emit('delete', item.id)" class="text-red-600 hover:text-red-900">Excluir</button>
                    </td>
                </tr>
                <tr v-if="data.length === 0">
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Nenhum contacto encontrado.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

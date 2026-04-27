<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import DataTable from './components/DataTable.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps<{
    entidades: any[]
    tipo: string
}>()

const handleDelete = (id: number) => {
    if (confirm('Tem certeza que deseja excluir?')) {
        router.delete(`/entidades/${id}`)
    }
}
</script>

<template>
    <AppLayout :title="tipo === 'cliente' ? 'Clientes' : 'Fornecedores'">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ tipo === 'cliente' ? 'Clientes' : 'Fornecedores' }}
                    </h1>
                    <Link :href="`/entidades/create?tipo=${tipo}`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Novo {{ tipo === 'cliente' ? 'Cliente' : 'Fornecedor' }}
                    </Link>
                </div>

                <DataTable :data="entidades" @delete="handleDelete" />
            </div>
        </div>
    </AppLayout>
</template>

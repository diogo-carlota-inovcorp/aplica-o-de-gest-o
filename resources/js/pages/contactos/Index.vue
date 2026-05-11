<script setup lang="ts">

import DataTable from './components/DataTable.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps<{
    contactos: any[]
    tipo: string  // 'cliente' ou 'fornecedor' ou 'ambos'
}>()

const handleDelete = (id: number) => {
    if (confirm('Tem certeza que deseja excluir este contacto?')) {
        router.delete(`/contactos/${id}`)
    }
}
</script>

<template>
    <AppLayout title="Contactos">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Contactos
                    </h1>
                    <Link href="/contactos/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Novo Contacto
                    </Link>
                </div>

                <DataTable :data="contactos" @delete="handleDelete" />
            </div>
        </div>
    </AppLayout>
</template>

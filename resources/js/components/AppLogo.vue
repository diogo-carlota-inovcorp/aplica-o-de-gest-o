<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const empresa = usePage().props.empresa as {
    logo?: string;
    nome: string;
};
</script>

<template>
    <div class="flex items-center gap-2">
        <img
            v-if="empresa?.logo"
            :src="'/storage/' + empresa.logo"
            class="h-8 w-8 object-contain"
            :alt="empresa?.nome"
        />
        <div v-else class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
            <span class="text-sm font-bold text-white">{{ empresa?.nome?.charAt(0) || 'G' }}</span>
        </div>
        <span class="text-sm font-semibold truncate max-w-[120px]">
            
        </span>
    </div>
</template>

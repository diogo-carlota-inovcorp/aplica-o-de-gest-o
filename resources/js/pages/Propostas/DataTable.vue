<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.accessorKey || column.id"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ column.header }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="row in data" :key="row.id">
                    <td
                        v-for="column in columns"
                        :key="column.accessorKey || column.id"
                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                    >
                        <div v-if="column.cell">
                            <div v-html="column.cell({ row: { original: row } })"></div>
                        </div>
                        <div v-else-if="column.accessorKey">
                            {{ getNestedValue(row, column.accessorKey) }}
                        </div>
                    </td>
                </tr>
                <tr v-if="data.length === 0">
                    <td :colspan="columns.length" class="px-6 py-4 text-center text-gray-500">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    data: {
        type: Array,
        default: () => []
    },
    columns: {
        type: Array,
        required: true
    }
});

const getNestedValue = (obj, path) => {
    return path.split('.').reduce((current, key) => {
        return current && current[key] !== undefined ? current[key] : '';
    }, obj);
};
</script>

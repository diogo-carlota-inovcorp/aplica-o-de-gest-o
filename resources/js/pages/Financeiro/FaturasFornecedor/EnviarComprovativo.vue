<template>
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">Enviar Comprovativo ao Fornecedor</h2>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Comprovativo de Pagamento *
                    </label>
                    <input
                        type="file"
                        @change="handleFileChange"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-gray-900"
                        required
                    />
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="close"
                        class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded"
                        :disabled="processing"
                    >
                        Enviar e Marcar como Paga
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    fatura: Object
});

const comprovativo = ref(null);
const processing = ref(false);

const handleFileChange = (event) => {
    comprovativo.value = event.target.files[0];
};

const submit = () => {
    if (!comprovativo.value) {
        alert('Selecione o comprovativo de pagamento');
        return;
    }

    processing.value = true;

    const formData = new FormData();
    formData.append('comprovativo', comprovativo.value);

    router.post(`/faturas-fornecedor/${props.fatura.id}/enviar-comprovativo`, formData, {
        forceFormData: true,
        onSuccess: () => {
            processing.value = false;
            window.location.href = '/faturas-fornecedor';
        },
        onError: () => {
            processing.value = false;
        }
    });
};

const close = () => {
    window.location.href = '/faturas-fornecedor';
};
</script>

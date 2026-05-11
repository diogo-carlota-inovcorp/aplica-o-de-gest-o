<template>
    <AppLayout title="Configurações da Empresa">
        <div class="py-6">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Configurações da Empresa</h1>
                    <p class="text-sm text-gray-600">Personalize os dados que aparecem nos PDFs e na aplicação</p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6" enctype="multipart/form-data">
                            <!-- Logotipo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logotipo da Empresa</label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <img v-if="logoPreview" :src="logoPreview" class="h-24 w-24 object-cover rounded-lg border">
                                        <div v-else-if="config.logo" class="h-24 w-24 bg-gray-100 rounded-lg border flex items-center justify-center">
                                            <img :src="'/storage/' + config.logo" class="h-20 w-20 object-contain">
                                        </div>
                                        <div v-else class="h-24 w-24 bg-gray-100 rounded-lg border flex items-center justify-center">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" @change="handleLogoChange" accept="image/*" class="w-full text-gray-900">
                                        <p class="text-xs text-gray-500 mt-1">Formatos: PNG, JPG, JPEG. Máx: 2MB</p>
                                    </div>
                                </div>
                                <div v-if="form.errors.logo" class="text-red-600 text-sm mt-1">{{ form.errors.logo }}</div>
                            </div>

                            <!-- Nome da Empresa -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Empresa *</label>
                                <input v-model="form.nome" type="text" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                                <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>
                            </div>

                            <!-- NIF -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Contribuinte (NIF)</label>
                                <input v-model="form.nif" type="text" maxlength="20" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>

                            <!-- Morada -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Morada</label>
                                <textarea v-model="form.morada" rows="2" class="w-full rounded-md border-gray-300 px-3 py-2"></textarea>
                            </div>

                            <!-- Código Postal e Localidade -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                                    <input v-model="form.codigo_postal" type="text" placeholder="1234-567" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Localidade</label>
                                    <input v-model="form.localidade" type="text" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>
                            </div>

                            <!-- Contactos -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input v-model="form.email" type="email" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                    <input v-model="form.telefone" type="tel" class="w-full rounded-md border-gray-300 px-3 py-2" />
                                </div>
                            </div>

                            <!-- Website -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input v-model="form.website" type="url" class="w-full rounded-md border-gray-300 px-3 py-2" placeholder="https://www.exemplo.com" />
                            </div>

                            <!-- Botões -->
                            <div class="flex justify-end space-x-3 pt-4 border-t">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="form.processing">
                                    {{ form.processing ? 'A guardar...' : 'Guardar Configurações' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    config: Object
});

const form = useForm({
    nome: props.config.nome,
    morada: props.config.morada || '',
    codigo_postal: props.config.codigo_postal || '',
    localidade: props.config.localidade || '',
    nif: props.config.nif || '',
    email: props.config.email || '',
    telefone: props.config.telefone || '',
    website: props.config.website || '',
    logo: null,
});

const logoPreview = ref(null);

const handleLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.put('/configuracoes/empresa', {
        forceFormData: true,
    });
};
</script>

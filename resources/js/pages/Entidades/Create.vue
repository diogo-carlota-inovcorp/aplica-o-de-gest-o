<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps<{
    tipo: string
    paises: any[]
}>()

const isValidating = ref(false)

const form = useForm({
    tipo: props.tipo,
    nif: '',
    nome: '',
    morada: '',
    codigo_postal: '',
    localidade: '',
    pais_id: '',
    telefone: '',
    telemovel: '',
    website: '',
    email: '',
    consentimento_rgpd: false,
    observacoes: '',
    estado: true,
})

// Validar NIF via VIES
const validateNIF = async () => {
    console.log('Botão clicado!') // Para debug
    console.log('NIF:', form.nif)

    if (!form.nif || form.nif.length < 9) {
        alert('Por favor, insira um NIF válido com 9 dígitos')
        return
    }

    isValidating.value = true

    try {
        const response = await axios.post('/entidades/validate-nif', {
            nif: form.nif,
            pais_code: 'PT'
        })

        console.log('Resposta:', response.data)

        if (response.data.exists) {
            alert('Este NIF já está cadastrado!')
        } else {
            alert('NIF válido!')
        }
    } catch (error) {
        console.error('Erro ao validar NIF:', error)
        alert('Erro ao validar NIF. Tente novamente.')
    } finally {
        isValidating.value = false
    }
}

const submit = () => {
    form.post('/entidades')
}
</script>

<template>
    <AppLayout :title="tipo === 'cliente' ? 'Novo Cliente' : 'Novo Fornecedor'">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    {{ tipo === 'cliente' ? 'Novo Cliente' : 'Novo Fornecedor' }}
                </h1>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- NIF com validação -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIF *</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="form.nif"
                                    type="text"
                                    maxlength="9"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                    required
                                />
                                <button
                                    type="button"
                                    @click="validateNIF"
                                    :disabled="isValidating"
                                    class="mt-1 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50"
                                >
                                    {{ isValidating ? 'Validando...' : 'Validar NIF' }}
                                </button>
                            </div>
                            <div v-if="form.errors.nif" class="text-red-600 text-sm mt-1">{{ form.errors.nif }}</div>
                        </div>


                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome *</label>
                            <input v-model="form.nome" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900" required>
                            <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>
                        </div>

                        <!-- Morada -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Morada</label>
                            <textarea v-model="form.morada" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900"></textarea>
                        </div>

                        <!-- Código Postal e Localidade -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Código Postal (XXXX-XXX)</label>
                                <input v-model="form.codigo_postal" type="text" placeholder="1234-567" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                                <div v-if="form.errors.codigo_postal" class="text-red-600 text-sm mt-1">{{ form.errors.codigo_postal }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Localidade</label>
                                <input v-model="form.localidade" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            </div>
                        </div>

                        <!-- País -->   
                        <div>
                            <label class="block text-sm font-medium text-gray-700">País</label>
                            <select v-model="form.pais_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900  ">
                                <option value="">Selecione...</option>
                                <option v-for="pais in paises" :key="pais.id" :value="pais.id">
                                    {{ pais.nome }}
                                </option>
                            </select>
                        </div>

                        <!-- Contactos -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input v-model="form.telefone" type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
                                <input v-model="form.telemovel" type="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            </div>
                        </div>

                        <!-- Número (se for numeração automática ou manual) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número</label>
                            <input 
                                v-model="form.numero" 
                                type="text" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900"
                                placeholder="Número da entidade"
                            >
                            <div v-if="form.errors.numero" class="text-red-600 text-sm mt-1">{{ form.errors.numero }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Website</label>
                                <input v-model="form.website" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            </div>
                        </div>

                        <!-- RGPD -->
                        <div class="flex items-center">
                            <input v-model="form.consentimento_rgpd" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm ">
                            <label class="ml-2 text-sm text-gray-700">Consentimento RGPD</label>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea v-model="form.observacoes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900"></textarea>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <select v-model="form.estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                                <option :value="true">Ativo</option>
                                <option :value="false">Inativo</option>
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex space-x-3 pt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="form.processing">
                                Salvar
                            </button>
                            <Link :href="`/entidades?tipo=${form.tipo}`" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

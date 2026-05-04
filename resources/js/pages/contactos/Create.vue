<script setup lang="ts">

import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps<{
    entidades: any[]
    funcoes: any[]
}>()

const isValidatingEmail = ref(false)

const form = useForm({
    numero: '',
    entidade_id: '',
    nome: '',
    apelido: '',
    funcao_id: '',
    telefone: '',
    telemovel: '',
    email: '',
    consentimento_rgpd: false,
    observacoes: '',
    estado: true,
})

// Validar email duplicado
const validateEmail = async () => {
    if (!form.email) {
        alert('Por favor, insira um email')
        return
    }

    isValidatingEmail.value = true

    try {
        const response = await axios.post('/contactos/validate-email', {
            email: form.email
        })

        if (response.data.exists) {
            alert('Este email já está cadastrado em outro contacto!')
        } else {
            alert('Email válido e disponível!')
        }
    } catch (error) {
        console.error('Erro ao validar email:', error)
        alert('Erro ao validar email. Tente novamente.')
    } finally {
        isValidatingEmail.value = false
    }
}

const submit = () => {
    form.post('/contactos')
}
</script>

<template>
    <AppLayout title="Novo Contacto">
        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">
                    Novo Contacto
                </h1>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Número (automático ou manual) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número *</label>
                            <input
                                v-model="form.numero"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                placeholder="Nº do contacto (ex: CT-001)"
                                required
                            >
                            <div v-if="form.errors.numero" class="text-red-600 text-sm mt-1">{{ form.errors.numero }}</div>
                        </div>

                        <!-- Entidade -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Entidade *</label>
                            <select
                                v-model="form.entidade_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                required
                            >
                                <option value="">Selecione uma entidade...</option>
                                <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                    {{ entidade.nome }} ({{ entidade.nif || 'Sem NIF' }})
                                </option>
                            </select>
                            <div v-if="form.errors.entidade_id" class="text-red-600 text-sm mt-1">{{ form.errors.entidade_id }}</div>
                        </div>

                        <!-- Nome e Apelido -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nome *</label>
                                <input
                                    v-model="form.nome"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                    required
                                >
                                <div v-if="form.errors.nome" class="text-red-600 text-sm mt-1">{{ form.errors.nome }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Apelido</label>
                                <input
                                    v-model="form.apelido"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                >
                            </div>
                        </div>

                        <!-- Função -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Função</label>
                            <select
                                v-model="form.funcao_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                            >
                                <option value="">Selecione uma função...</option>
                                <option v-for="funcao in funcoes" :key="funcao.id" :value="funcao.id">
                                    {{ funcao.nome }}
                                </option>
                            </select>
                            <div v-if="form.errors.funcao_id" class="text-red-600 text-sm mt-1">{{ form.errors.funcao_id }}</div>
                        </div>

                        <!-- Telefone e Telemóvel -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input
                                    v-model="form.telefone"
                                    type="tel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                    placeholder="+351 123 456 789"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telemóvel</label>
                                <input
                                    v-model="form.telemovel"
                                    type="tel"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                    placeholder="+351 912 345 678"
                                >
                            </div>
                        </div>

                        <!-- Email com validação -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="flex gap-2">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                    placeholder="contacto@empresa.com"
                                />
                                <button
                                    type="button"
                                    @click="validateEmail"
                                    :disabled="isValidatingEmail"
                                    class="mt-1 px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 disabled:opacity-50"
                                >
                                    {{ isValidatingEmail ? 'Validando...' : 'Validar Email' }}
                                </button>
                            </div>
                            <div v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <!-- Consentimento RGPD -->
                        <div class="flex items-center">
                            <input
                                v-model="form.consentimento_rgpd"
                                type="checkbox"
                                class="rounded border-gray-300 text-blue-600 shadow-sm"
                            >
                            <label class="ml-2 text-sm text-gray-700">
                                Consentimento RGPD (autorizo o tratamento dos meus dados)
                            </label>
                        </div>
                        <div v-if="form.errors.consentimento_rgpd" class="text-red-600 text-sm mt-1">{{ form.errors.consentimento_rgpd }}</div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea
                                v-model="form.observacoes"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                                placeholder="Informações adicionais sobre o contacto..."
                            ></textarea>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <select
                                v-model="form.estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900 px-3 py-2"
                            >
                                <option :value="true">Ativo</option>
                                <option :value="false">Inativo</option>
                            </select>
                        </div>

                        <!-- Botões -->
                        <div class="flex space-x-3 pt-4">
                            <button
                                type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                :disabled="form.processing"
                            >
                                Salvar Contacto
                            </button>
                            <Link href="/contactos" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

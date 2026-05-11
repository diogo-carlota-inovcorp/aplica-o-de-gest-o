<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import {
    Users, Building2, Phone, UserCheck,
    Package, FileText, ShoppingCart, TrendingUp,
    Crown, Calendar, Clock, AlertCircle,
    ChevronRight, Plus, Trash2, CreditCard, Award
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    planoInfo: Object,
    limites: Object,
    utilizacao: Object,
    assinatura: Object,
    logsAlteracoes: Array,
    planosDisponiveis: Array,
    meusLocatarios: Array
});

const page = usePage();
const modalAberto = ref(false);
const modalPlanoAberto = ref(false);
const planoSelecionado = ref(null);
const locatarioAtual = ref(page.props.locatario_atual);

const formLocatario = ref({
    nome: '',
    email: '',
    plano_id: ''
});

const trocarLocatario = (locatario) => {
    router.get(`/mudar-locatario/${locatario.slug}`, {}, {
        preserveState: false,
        onSuccess: () => window.location.reload()
    });
};

const criarLocatario = () => {
    if (!formLocatario.value.nome) {
        alert('Por favor, insira o nome da empresa');
        return;
    }
    if (!formLocatario.value.plano_id) {
        alert('Por favor, selecione um plano');
        return;
    }

    router.post('/locatarios', {
        nome: formLocatario.value.nome,
        email: formLocatario.value.email,
        plano_id: formLocatario.value.plano_id
    }, {
        onSuccess: () => {
            modalAberto.value = false;
            alert('Empresa criada com sucesso!');
            window.location.reload();
        }
    });
};

const confirmarApagar = (locatario) => {
    if (props.meusLocatarios?.length <= 1) {
        alert('Não pode apagar a única empresa.');
        return;
    }
    if (confirm(`Tem certeza que deseja apagar a empresa "${locatario.nome}"?`)) {
        router.delete(`/locatarios/${locatario.id}`, {
            onSuccess: () => window.location.reload()
        });
    }
};

const mudarPlano = () => {
    if (!planoSelecionado.value) return;
    router.post(`/assinaturas/upgrade/${planoSelecionado.value.id}`, {}, {
        onSuccess: () => {
            modalPlanoAberto.value = false;
            alert('Plano alterado com sucesso!');
            window.location.reload();
        }
    });
};
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="min-h-screen bg-gray-50">
            <div class="py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    <!-- Header com gradiente -->
                    <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white shadow-lg">
                        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-48 w-48 rounded-full bg-white/10"></div>
                        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-32 w-32 rounded-full bg-white/10"></div>

                        <div class="relative flex flex-col justify-between sm:flex-row sm:items-center">
                            <div>
                                <h1 class="text-3xl font-bold">Dashboard</h1>
                                <p class="mt-1 text-blue-100">
                                    Bem-vindo de volta!
                                    <span class="font-semibold text-white">{{ locatarioAtual?.nome || 'Selecione uma empresa' }}</span>
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm transition-all hover:shadow-md">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-blue-100 opacity-50 group-hover:scale-110 transition"></div>
                            <div class="relative">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                                    <Users class="h-5 w-5 text-blue-600" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ stats.clientes || 0 }}</div>
                                <div class="text-sm text-gray-500">Clientes</div>
                                <div class="mt-2 text-xs text-gray-400">
                                    Limite: {{ utilizacao?.utilizadores?.limite || '∞' }}
                                </div>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm transition-all hover:shadow-md">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-green-100 opacity-50 group-hover:scale-110 transition"></div>
                            <div class="relative">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                                    <Building2 class="h-5 w-5 text-green-600" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ stats.fornecedores || 0 }}</div>
                                <div class="text-sm text-gray-500">Fornecedores</div>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm transition-all hover:shadow-md">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-purple-100 opacity-50 group-hover:scale-110 transition"></div>
                            <div class="relative">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                                    <Phone class="h-5 w-5 text-purple-600" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ stats.contactos || 0 }}</div>
                                <div class="text-sm text-gray-500">Contactos</div>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl bg-white p-5 shadow-sm transition-all hover:shadow-md">
                            <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-orange-100 opacity-50 group-hover:scale-110 transition"></div>
                            <div class="relative">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100">
                                    <UserCheck class="h-5 w-5 text-orange-600" />
                                </div>
                                <div class="text-2xl font-bold text-gray-900">{{ stats.utilizadores || 0 }}</div>
                                <div class="text-sm text-gray-500">Utilizadores</div>
                                <div class="mt-2 text-xs text-gray-400">
                                    Limite: {{ utilizacao?.utilizadores?.limite || '∞' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Minhas Empresas -->
                    <div class="mb-8 rounded-xl bg-white p-6 shadow-sm">
                        <div class="mb-4 flex flex-col justify-between sm:flex-row sm:items-center">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Minhas Empresas</h2>
                                <p class="text-sm text-gray-500">Gerencie suas empresas e alterne entre elas</p>
                            </div>
                            <button @click="modalAberto = true" class="mt-3 flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-green-700 sm:mt-0">
                                <Plus class="h-4 w-4" />
                                Nova Empresa
                            </button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="locatario in meusLocatarios"
                                :key="locatario.id"
                                @click="trocarLocatario(locatario)"
                                :class="[
                                    'group cursor-pointer rounded-xl border-2 p-4 transition-all hover:shadow-md',
                                    locatarioAtual?.id === locatario.id
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-200 bg-white hover:border-gray-300'
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ locatario.nome }}</h3>
                                        <p class="text-sm text-gray-500">{{ locatario.email || 'Sem email' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div v-if="locatarioAtual?.id === locatario.id" class="rounded-full bg-blue-500 px-2 py-0.5 text-xs font-medium text-white">
                                            Atual
                                        </div>
                                        <button
                                            @click.stop="confirmarApagar(locatario)"
                                            class="rounded-lg p-1 text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                                            title="Apagar empresa"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plano Atual -->
                    <div v-if="planoInfo" class="mb-8 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
                        <div class="p-6 text-white">
                            <div class="flex flex-col justify-between sm:flex-row sm:items-start">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <Crown class="h-5 w-5 text-yellow-300" />
                                        <h2 class="text-xl font-bold">Plano {{ planoInfo.nome }}</h2>
                                    </div>
                                    <p class="mt-1 text-2xl font-bold">€{{ planoInfo.preco_mensal }}<span class="text-sm font-normal">/mês</span></p>

                                    <div v-if="planoInfo.em_teste" class="mt-3 flex items-center gap-2 rounded-lg bg-yellow-500 px-3 py-1.5 text-sm">
                                        <Clock class="h-4 w-4" />
                                        Período de teste: {{ planoInfo.dias_teste_restantes }} dias restantes
                                    </div>

                                    <div v-if="planoInfo.data_fim_subscricao" class="mt-2 flex items-center gap-2 text-sm text-blue-100">
                                        <Calendar class="h-4 w-4" />
                                        Próxima fatura: {{ planoInfo.data_fim_subscricao }}
                                    </div>
                                </div>
                                <button @click="modalPlanoAberto = true" class="mt-4 flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-purple-600 transition hover:bg-gray-100 sm:mt-0">
                                    <CreditCard class="h-4 w-4" />
                                    Alterar Plano
                                </button>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3">
                                <div v-for="func in planoInfo.funcionalidades" :key="func" class="flex items-center gap-2 text-sm">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-300"></div>
                                    {{ func }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Utilização do Plano -->
                    <div class="mb-8 rounded-xl bg-white p-6 shadow-sm">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                            <TrendingUp class="h-5 w-5 text-gray-600" />
                            Utilização do Plano
                        </h3>
                        <div class="space-y-5">
                            <div v-if="utilizacao?.utilizadores">
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Utilizadores</span>
                                    <span class="text-gray-500">{{ utilizacao.utilizadores.atual }} / {{ utilizacao.utilizadores.limite || '∞' }}</span>
                                </div>
                                <div v-if="utilizacao.utilizadores.limite" class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-blue-600 transition-all" :style="{ width: utilizacao.utilizadores.percentual + '%' }"></div>
                                </div>
                            </div>

                            <div v-if="utilizacao?.artigos">
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Artigos</span>
                                    <span class="text-gray-500">{{ utilizacao.artigos.atual }} / {{ utilizacao.artigos.limite || '∞' }}</span>
                                </div>
                                <div v-if="utilizacao.artigos.limite" class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-green-600 transition-all" :style="{ width: utilizacao.artigos.percentual + '%' }"></div>
                                </div>
                            </div>

                            <div v-if="utilizacao?.propostas">
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-gray-700">Propostas (este mês)</span>
                                    <span class="text-gray-500">{{ utilizacao.propostas.atual }} / {{ utilizacao.propostas.limite || '∞' }}</span>
                                </div>
                                <div v-if="utilizacao.propostas.limite" class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full bg-purple-600 transition-all" :style="{ width: Math.min(utilizacao.propostas.percentual, 100) + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Histórico de Alterações -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900">
                            <Award class="h-5 w-5 text-gray-600" />
                            Histórico de Alterações
                        </h3>
                        <div class="space-y-3">
                            <div v-for="log in logsAlteracoes" :key="log.data" class="flex items-start gap-3 rounded-lg border-l-4 border-blue-500 bg-gray-50 p-3">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-700">{{ log.acao }}</p>
                                    <p class="text-xs text-gray-400">{{ log.data }}</p>
                                </div>
                            </div>
                            <div v-if="logsAlteracoes?.length === 0" class="py-8 text-center text-gray-500">
                                Nenhuma alteração registada.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modais -->
            <div v-if="modalAberto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="modalAberto = false">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                    <h2 class="mb-4 text-xl font-bold text-gray-900">Criar Nova Empresa</h2>
                    <form @submit.prevent="criarLocatario" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nome da Empresa *</label>
                            <input v-model="formLocatario.nome" type="text" class="w-full rounded-lg border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" required />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="formLocatario.email" type="email" class="w-full rounded-lg border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Plano *</label>
                            <select v-model="formLocatario.plano_id" class="w-full rounded-lg border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Selecione um plano</option>
                                <option v-for="plano in planosDisponiveis" :key="plano.id" :value="plano.id">
                                    {{ plano.nome }} - €{{ plano.preco_mensal }}/mês
                                </option>
                            </select>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="modalAberto = false" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 transition hover:bg-gray-300">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">Criar Empresa</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="modalPlanoAberto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="modalPlanoAberto = false">
                <div class="w-full max-w-3xl rounded-2xl bg-white p-6 shadow-xl max-h-[85vh] overflow-y-auto">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Escolher Plano</h2>
                        <button @click="modalPlanoAberto = false" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600">✕</button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="plano in planosDisponiveis" :key="plano.id" @click="planoSelecionado = plano" :class="[
                            'cursor-pointer rounded-xl border-2 p-4 transition-all hover:shadow-md',
                            planoSelecionado?.id === plano.id ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'
                        ]">
                            <div class="flex items-center justify-between">
                                <h3 class="font-semibold text-gray-900">{{ plano.nome }}</h3>
                                <div v-if="plano.nome === 'Profissional'" class="rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-700">Popular</div>
                            </div>
                            <p class="mt-2 text-2xl font-bold text-blue-600">€{{ plano.preco_mensal }}<span class="text-sm font-normal text-gray-500">/mês</span></p>
                            <p class="mt-2 text-sm text-gray-600">{{ plano.descricao }}</p>
                            <ul class="mt-3 space-y-1 text-sm text-gray-500">
                                <li v-for="func in (plano.funcionalidades || [])" :key="func" class="flex items-center gap-2">
                                    <Check class="h-3 w-3 text-green-500" />
                                    {{ func }}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3 border-t pt-4">
                        <button @click="modalPlanoAberto = false" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 transition hover:bg-gray-300">Cancelar</button>
                        <button v-if="planoSelecionado" @click="mudarPlano" class="rounded-lg bg-blue-600 px-4 py-2 text-white transition hover:bg-blue-700">
                            Mudar para {{ planoSelecionado.nome }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

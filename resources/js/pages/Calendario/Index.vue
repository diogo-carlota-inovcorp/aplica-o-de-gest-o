<template>
    <AppLayout title="Calendário">
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Calendário de Atividades</h1>
                <button @click="abrirModal" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    + Nova Atividade
                </button>
            </div>

            <!-- Filtros -->
            <div class="grid grid-cols-1 gap-4 rounded-md border border-gray-200 bg-white p-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Utilizador</label>
                    <select v-model="filtros.utilizador_id" @change="carregarEventos" class="w-full rounded-md border-gray-300 px-3 py-2">
                        <option value="">Todos</option>
                        <option v-for="user in utilizadores" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Entidade</label>
                    <select v-model="filtros.entidade_id" @change="carregarEventos" class="w-full rounded-md border-gray-300 px-3 py-2">
                        <option value="">Todas</option>
                        <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">{{ entidade.nome }}</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button @click="carregarEventos" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">
                        Filtrar
                    </button>
                </div>
            </div>

            <!-- Calendário Personalizado -->
            <div class="rounded-md border border-gray-200 bg-white p-4">
                <SimpleCalendar
                    :eventos="eventos"
                    @selecionar-dia="abrirModalComData"
                    @ver-evento="verDetalhesEvento"
                    @editar-evento="abrirModalEditar"
                    @apagar-evento="apagarAtividade"
                />
            </div>

            <!-- Modal Nova/Editar Atividade -->
            <div v-if="modalAberto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="fecharModal">
                <div class="w-full max-w-lg rounded-lg bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold">{{ editando ? 'Editar Atividade' : 'Nova Atividade' }}</h2>
                        <button @click="fecharModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="salvarAtividade" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Título *</label>
                            <input v-model="form.titulo" type="text" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Data *</label>
                                <input v-model="form.data" type="date" class="w-full rounded-md border-gray-300 px-3 py-2" required />
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Hora</label>
                                <input v-model="form.hora_inicio" type="time" class="w-full rounded-md border-gray-300 px-3 py-2" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Duração (minutos)</label>
                            <input v-model="form.duracao" type="number" min="1" class="w-full rounded-md border-gray-300 px-3 py-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Utilizador *</label>
                                <select v-model="form.user_id" class="w-full rounded-md border-gray-300 px-3 py-2" required>
                                    <option value="">Selecione...</option>
                                    <option v-for="user in utilizadores" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Entidade</label>
                                <select v-model="form.entidade_id" class="w-full rounded-md border-gray-300 px-3 py-2">
                                    <option value="">Selecione...</option>
                                    <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">{{ entidade.nome }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium">Tipo</label>
                                <select v-model="form.tipo_id" class="w-full rounded-md border-gray-300 px-3 py-2">
                                    <option value="">Selecione...</option>
                                    <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">{{ tipo.nome }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium">Ação</label>
                                <select v-model="form.acao_id" class="w-full rounded-md border-gray-300 px-3 py-2">
                                    <option value="">Selecione...</option>
                                    <option v-for="acao in acoes" :key="acao.id" :value="acao.id">{{ acao.nome }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium">Descrição</label>
                            <textarea v-model="form.descricao" rows="3" class="w-full rounded-md border-gray-300 px-3 py-2 text-sm"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="fecharModal" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">
                                Cancelar
                            </button>
                            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">
                                {{ form.processing ? 'A guardar...' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Detalhes -->
            <div v-if="modalDetalhes" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="fecharDetalhes">
                <div class="w-full max-w-md rounded-lg bg-white p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-bold">Detalhes da Atividade</h2>
                        <button @click="fecharDetalhes" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <p><strong>Título:</strong> {{ eventoDetalhes?.title }}</p>
                        <p><strong>Data:</strong> {{ eventoDetalhes?.start?.split('T')[0] }}</p>
                        <p><strong>Hora:</strong> {{ eventoDetalhes?.start?.split('T')[1]?.substring(0, 5) || 'Não definida' }}</p>
                        <p><strong>Descrição:</strong> {{ eventoDetalhes?.extendedProps?.descricao || 'Sem descrição' }}</p>
                        <p><strong>Entidade:</strong> {{ eventoDetalhes?.extendedProps?.entidade_nome || 'Não definida' }}</p>
                        <p><strong>Tipo:</strong> {{ eventoDetalhes?.extendedProps?.tipo_nome || 'Não definido' }}</p>
                        <p><strong>Ação:</strong> {{ eventoDetalhes?.extendedProps?.acao_nome || 'Não definida' }}</p>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="abrirModalEditar(eventoDetalhes)" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                            Editar
                        </button>
                        <button @click="fecharDetalhes" class="rounded bg-gray-500 px-4 py-2 text-white hover:bg-gray-600">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import SimpleCalendar from '@/Components/SimpleCalendar.vue';

// Props do backend
const props = defineProps({
    tipos: { type: Array, default: () => [] },
    acoes: { type: Array, default: () => [] },
    entidades: { type: Array, default: () => [] },
    utilizadores: { type: Array, default: () => [] }
});

// Estado
const eventos = ref([]);
const modalAberto = ref(false);
const modalDetalhes = ref(false);
const eventoDetalhes = ref(null);
const editando = ref(false);
const eventoIdEditando = ref(null);

// Filtros
const filtros = ref({
    utilizador_id: '',
    entidade_id: ''
});

// Formulário
const form = ref({
    titulo: '',
    data: '',
    hora_inicio: '',
    duracao: 60,
    user_id: '',
    entidade_id: '',
    tipo_id: '',
    acao_id: '',
    descricao: '',
    processing: false
});

// Carregar eventos
const carregarEventos = async () => {
    try {
        const params = new URLSearchParams();
        if (filtros.value.utilizador_id) params.append('utilizador_id', filtros.value.utilizador_id);
        if (filtros.value.entidade_id) params.append('entidade_id', filtros.value.entidade_id);

        const response = await fetch(`/calendario/eventos?${params}`);
        const data = await response.json();

        eventos.value = data.map(e => ({
            id: e.id,
            title: e.titulo,
            start: `${e.data}T${e.hora_inicio || '00:00:00'}`,
            end: `${e.data}T${e.hora_fim || '23:59:59'}`,
            backgroundColor: e.cor || '#3b82f6',
            extendedProps: {
                descricao: e.descricao,
                partilha: e.partilha,
                conhecimento: e.conhecimento,
                entidade_id: e.entidade_id,
                entidade_nome: e.entidade_nome,
                tipo_id: e.tipo_id,
                tipo_nome: e.tipo_nome,
                acao_id: e.acao_id,
                acao_nome: e.acao_nome,
                duracao: e.duracao,
                hora_inicio: e.hora_inicio
            }
        }));
    } catch (error) {
        console.error('Erro ao carregar eventos:', error);
    }
};

// Abrir modal para nova atividade
const abrirModal = () => {
    editando.value = false;
    eventoIdEditando.value = null;
    form.value = {
        titulo: '',
        data: new Date().toISOString().split('T')[0],
        hora_inicio: '',
        duracao: 60,
        user_id: '',
        entidade_id: '',
        tipo_id: '',
        acao_id: '',
        descricao: '',
        processing: false
    };
    modalAberto.value = true;
};

const abrirModalComData = (data) => {
    abrirModal();
    form.value.data = data;
};

// Abrir modal para editar atividade
const abrirModalEditar = (evento) => {
    editando.value = true;
    eventoIdEditando.value = evento.id;

    const dataEvento = evento.start.split('T')[0];
    const horaEvento = evento.start.split('T')[1]?.substring(0, 5) || '';

    form.value = {
        titulo: evento.title,
        data: dataEvento,
        hora_inicio: horaEvento,
        duracao: evento.extendedProps?.duracao || 60,
        user_id: evento.extendedProps?.user_id || '',
        entidade_id: evento.extendedProps?.entidade_id || '',
        tipo_id: evento.extendedProps?.tipo_id || '',
        acao_id: evento.extendedProps?.acao_id || '',
        descricao: evento.extendedProps?.descricao || '',
        processing: false
    };

    modalAberto.value = true;
    if (modalDetalhes.value) fecharDetalhes();
};

// Salvar atividade (criar ou editar)
const salvarAtividade = () => {
    if (!form.value.titulo) {
        alert('Por favor, insira um título.');
        return;
    }

    if (!form.value.data) {
        alert('Por favor, selecione uma data.');
        return;
    }

    if (!form.value.user_id) {
        alert('Por favor, selecione um utilizador.');
        return;
    }

    form.value.processing = true;

    const dados = {
        titulo: form.value.titulo,
        data: form.value.data,
        hora_inicio: form.value.hora_inicio || '09:00:00',
        hora_fim: form.value.hora_inicio ? (() => {
            const [h, m] = form.value.hora_inicio.split(':');
            const date = new Date();
            date.setHours(parseInt(h), parseInt(m) + (parseInt(form.value.duracao) || 60));
            return `${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:00`;
        })() : '10:00:00',
        duracao: form.value.duracao || 60,
        user_id: parseInt(form.value.user_id),
        entidade_id: form.value.entidade_id ? parseInt(form.value.entidade_id) : null,
        tipo_id: form.value.tipo_id ? parseInt(form.value.tipo_id) : null,
        acao_id: form.value.acao_id ? parseInt(form.value.acao_id) : null,
        partilha: 'Não',
        conhecimento: 'Não',
        estado: 'pendente',
        descricao: form.value.descricao || null
    };

    const url = editando.value ? `/calendario/${eventoIdEditando.value}` : '/calendario';
    const method = editando.value ? 'put' : 'post';

    router[method](url, dados, {
        onSuccess: () => {
            fecharModal();
            carregarEventos();
            alert(editando.value ? 'Atividade atualizada com sucesso!' : 'Atividade agendada com sucesso!');
            form.value.processing = false;
        },
        onError: (errors) => {
            console.error('Erro:', errors);
            alert('Erro ao guardar atividade.');
            form.value.processing = false;
        }
    });
};


const apagarAtividade = (id) => {
    console.log('Apagar atividade com ID:', id); // Debug

    if (confirm('Tem certeza que deseja apagar esta atividade?')) {
        router.delete(`/calendario/${id}`, {
            onSuccess: () => {
                console.log('Atividade apagada com sucesso');
                carregarEventos();
                if (modalDetalhes.value) fecharDetalhes();
                alert('Atividade apagada com sucesso!');
            },
            onError: (errors) => {
                console.error('Erro ao apagar:', errors);
                alert('Erro ao apagar atividade. Tente novamente.');
            }
        });
    }
};

// Detalhes
const verDetalhesEvento = (evento) => {
    eventoDetalhes.value = evento;
    modalDetalhes.value = true;
};

const fecharDetalhes = () => {
    modalDetalhes.value = false;
    eventoDetalhes.value = null;
};

const fecharModal = () => {
    modalAberto.value = false;
    editando.value = false;
    eventoIdEditando.value = null;
};

onMounted(() => {
    carregarEventos();
});
</script>

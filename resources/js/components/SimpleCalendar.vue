<template>
    <div class="simple-calendar">
        <!-- Cabeçalho do calendário -->
        <div class="flex items-center justify-between mb-4">
            <button @click="mudarMes(-1)" class="p-2 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h2 class="text-xl font-bold">{{ nomeMes }} {{ ano }}</h2>
            <button @click="mudarMes(1)" class="p-2 rounded hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Dias da semana -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            <div v-for="dia in diasSemana" :key="dia" class="text-center font-medium text-sm py-2 text-gray-600">
                {{ dia }}
            </div>
        </div>

        <!-- Dias do mês -->
        <div class="grid grid-cols-7 gap-1">
            <div
                v-for="(dia, index) in diasCalendario"
                :key="index"
                @click="selecionarDia(dia.date)"
                :class="[
                    'min-h-[100px] p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition',
                    dia.isCurrentMonth ? 'bg-white' : 'bg-gray-50 text-gray-400',
                    dia.isToday ? 'border-blue-500 border-2' : 'border-gray-200',
                    dataSelecionada === dia.date ? 'ring-2 ring-blue-500' : ''
                ]"
            >
                <div class="font-medium text-sm mb-2">{{ dia.day }}</div>
                <div class="space-y-1">
                    <div
                        v-for="evento in getEventosDoDia(dia.date)"
                        :key="evento.id"
                        class="text-xs p-1 rounded truncate flex items-center justify-between group"
                        :style="{ backgroundColor: evento.backgroundColor + '20', color: evento.backgroundColor }"
                    >
                        <!-- Conteúdo do evento (clicável para ver detalhes) -->
                        <div @click.stop="verEvento(evento)" class="flex-1 truncate cursor-pointer">
                            <span class="font-medium">{{ evento.hora || '' }}</span> {{ evento.title }}
                        </div>
                        <!-- Botões de ação -->
                        <div class="hidden group-hover:flex items-center gap-1 ml-1">
                            <button
                                @click.stop="editarEvento(evento)"
                                class="p-1 rounded hover:bg-gray-200 transition"
                                title="Editar"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                @click.stop="apagarEvento(evento.id)"
                                class="p-1 rounded hover:bg-gray-200 transition"
                                title="Apagar"
                            >
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    eventos: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['selecionarDia', 'verEvento', 'editarEvento', 'apagarEvento']);

const dataAtual = ref(new Date());
const dataSelecionada = ref(null);

const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];

const nomeMes = computed(() => {
    return dataAtual.value.toLocaleString('pt-PT', { month: 'long' });
});

const ano = computed(() => {
    return dataAtual.value.getFullYear();
});

const diasCalendario = computed(() => {
    const ano = dataAtual.value.getFullYear();
    const mes = dataAtual.value.getMonth();

    const primeiroDia = new Date(ano, mes, 1);
    const ultimoDia = new Date(ano, mes + 1, 0);

    const diasAnterior = primeiroDia.getDay();
    const diasTotal = ultimoDia.getDate();

    const hoje = new Date();
    const hojeStr = hoje.toISOString().split('T')[0];

    const dias = [];

    const dataAnterior = new Date(ano, mes, 0);
    const diasAnteriorTotal = dataAnterior.getDate();
    for (let i = diasAnterior - 1; i >= 0; i--) {
        const date = new Date(ano, mes - 1, diasAnteriorTotal - i);
        dias.push({
            day: date.getDate(),
            date: date.toISOString().split('T')[0],
            isCurrentMonth: false,
            isToday: date.toISOString().split('T')[0] === hojeStr
        });
    }

    for (let i = 1; i <= diasTotal; i++) {
        const date = new Date(ano, mes, i);
        const dateStr = date.toISOString().split('T')[0];
        dias.push({
            day: i,
            date: dateStr,
            isCurrentMonth: true,
            isToday: dateStr === hojeStr
        });
    }

    const diasRestantes = 42 - dias.length;
    for (let i = 1; i <= diasRestantes; i++) {
        const date = new Date(ano, mes + 1, i);
        dias.push({
            day: date.getDate(),
            date: date.toISOString().split('T')[0],
            isCurrentMonth: false,
            isToday: false
        });
    }

    return dias;
});

const mudarMes = (delta) => {
    dataAtual.value = new Date(dataAtual.value.getFullYear(), dataAtual.value.getMonth() + delta, 1);
};

const selecionarDia = (data) => {
    dataSelecionada.value = data;
    emit('selecionarDia', data);
};

const verEvento = (evento) => {
    emit('verEvento', evento);
};

const editarEvento = (evento) => {
    emit('editarEvento', evento);
};

const apagarEvento = (id) => {
    emit('apagarEvento', id);
};

const getEventosDoDia = (data) => {
    return props.eventos.filter(evento => {
        if (!evento.start) return false;
        const dataEvento = evento.start.split('T')[0];
        return dataEvento === data;
    }).map(evento => ({
        ...evento,
        hora: evento.start?.split('T')[1]?.substring(0, 5)
    }));
};
</script>

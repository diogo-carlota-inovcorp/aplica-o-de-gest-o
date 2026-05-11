<template>
    
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { Building2, ChevronDown, Check, Plus } from 'lucide-vue-next';

const page = usePage();
const dropdownAberto = ref(false);
const meusLocatarios = ref([]);
const locatarioAtual = computed(() => page.props.locatario_atual);

const carregarMeusLocatarios = async () => {
    const response = await fetch('/api/meus-locatarios');
    meusLocatarios.value = await response.json();
};

const mudarLocatario = (locatario) => {
    router.get(`/mudar-locatario/${locatario.slug}`, {}, {
        preserveState: false,
        onSuccess: () => {
            dropdownAberto.value = false;
            window.location.reload();
        }
    });
};

const criarNovoLocatario = () => {
    router.get('/locatarios/criar');
    dropdownAberto.value = false;
};

onMounted(() => {
    carregarMeusLocatarios();
});
</script>

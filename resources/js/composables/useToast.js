import { ref } from 'vue';

const toasts = ref([]);

export const useToast = () => {
    const pushToast = ({ type, title, message }) => {
        const id = Date.now();
        toasts.value.push({ id, type, title, message });
        setTimeout(() => {
            toasts.value = toasts.value.filter(t => t.id !== id);
        }, 5000);
    };

    return { toasts, pushToast };
};

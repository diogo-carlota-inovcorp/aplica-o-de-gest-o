// routes/contactos.ts
import type { RouteDefinition } from '@/types';

const prefix = '/contactos';

const contactos = {
    index: {
        url: (params?: Record<string, any>) => {
            const url = prefix;
            if (params && Object.keys(params).length) {
                const query = new URLSearchParams(params).toString();
                return `${url}?${query}`;
            }
            return url;
        },
        visit: (params?: Record<string, any>) => ({
            url: contactos.index.url(params),
            method: 'GET',
        }),
    },
    create: {
        url: () => `${prefix}/create`,
        visit: () => ({
            url: contactos.create.url(),
            method: 'GET',
        }),
    },
    edit: {
        url: (id: number) => `${prefix}/${id}/edit`,
        visit: (id: number) => ({
            url: contactos.edit.url(id),
            method: 'GET',
        }),
    },
    store: {
        url: () => prefix,
        visit: () => ({
            url: contactos.store.url(),
            method: 'POST',
        }),
    },
    update: {
        url: (id: number) => `${prefix}/${id}`,
        visit: (id: number) => ({
            url: contactos.update.url(id),
            method: 'PUT',
        }),
    },
    destroy: {
        url: (id: number) => `${prefix}/${id}`,
        visit: (id: number) => ({
            url: contactos.destroy.url(id),
            method: 'DELETE',
        }),
    },
};

export default contactos;

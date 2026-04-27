import { createRouter, createWebHistory } from 'vue-router'


import Dashboard from '../Pages/Dashboard/Index.vue'
import PaisesIndex from '../Pages/Configuracoes/Paises/Index.vue'
import ClientesIndex from '../Pages/Entidades/Clientes/Index.vue'
import FornecedoresIndex from '../Pages/Entidades/Fornecedores/Index.vue'

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/configuracoes/paises',
        name: 'paises',
        component: PaisesIndex
    },
    {
        path: '/clientes',
        name: 'clientes',
        component: ClientesIndex
    },
    {
        path: '/fornecedores',
        name: 'fornecedores',
        component: FornecedoresIndex
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router

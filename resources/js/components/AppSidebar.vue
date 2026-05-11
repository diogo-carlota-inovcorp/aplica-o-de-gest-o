<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen, Calendar, FolderGit2, LayoutGrid, Users, Truck, Contact,
    Receipt, FileText, Building2, Store, CreditCard, Shield, History
} from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import TenantSwitcher from '@/components/TenantSwitcher.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import entidades from '@/routes/entidades';
import contactos from '@/routes/contactos';
import type { NavItem } from '@/types';

// Obter dados da empresa
const empresa = usePage().props.empresa as {
    logo?: string;
    nome: string;
};

// Itens de navegação principais
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: entidades.index.url({ query: { tipo: 'cliente' } }),
        icon: Users,
    },
    {
        title: 'Fornecedores',
        href: entidades.index.url({ query: { tipo: 'fornecedor' } }),
        icon: Truck,
    },
    {
        title: 'Contactos',
        href: contactos.index.url(),
        icon: Contact,
    },
    {
        title: 'Artigos',
        href: '/artigos',
        icon: LayoutGrid,
    },
    {
        title: 'Propostas',
        href: '/propostas',
        icon: LayoutGrid,
    },
    {
        title: 'Encomendas',
        href: '/encomendas',
        icon: Truck,
    },
    {
        title: 'Faturas Fornecedor',
        href: '/faturas-fornecedor',
        icon: Receipt,
    },
    {
        title: 'Calendário',
        href: '/calendario',
        icon: Calendar,
    },
];

// Itens de navegação de gestão (Administração)
const adminNavItems: NavItem[] = [
    {
        title: 'Utilizadores',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Permissões',
        href: '/permissoes',
        icon: Shield,
    },
    {
        title: 'Logs',
        href: '/logs',
        icon: History,
    },
];

// Itens de navegação do Locatário (Tenant)
const tenantNavItems: NavItem[] = [
   
    {
        title: 'Configurações',
        href: '/configuracoes/empresa',
        icon: Building2,
    },
];

// Itens de navegação do footer
const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()" class="flex items-center gap-2">
                            <img
                                v-if="empresa?.logo"
                                :src="'/storage/' + empresa.logo"
                                class="h-8 w-8 object-contain"
                                :alt="empresa?.nome"
                            />
                            <AppLogo v-else />
                            <span class="text-sm font-semibold truncate max-w-30">
                                {{ empresa?.nome || 'Gestão' }}
                            </span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Selector de Locatário (Tenant Switcher) -->
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-800">
                <TenantSwitcher />
            </div>

            <!-- Menu Principal -->
            <NavMain :items="mainNavItems" />

            <!-- Separador - Administração -->
            <div class="px-3 py-2">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Administração
                </div>
            </div>
            <NavMain :items="adminNavItems" />

            <!-- Separador - Minha Empresa (Tenant) -->
            <div class="px-3 py-2">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Minha Empresa
                </div>
            </div>
            <NavMain :items="tenantNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>

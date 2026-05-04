<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissaoSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões para cada módulo
        $modulos = [
            'clientes' => ['view', 'create', 'edit', 'delete'],
            'fornecedores' => ['view', 'create', 'edit', 'delete'],
            'artigos' => ['view', 'create', 'edit', 'delete'],
            'propostas' => ['view', 'create', 'edit', 'delete', 'fechar'],
            'encomendas' => ['view', 'create', 'edit', 'delete', 'fechar'],
            'faturas' => ['view', 'create', 'edit', 'delete', 'pagar'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'permissoes' => ['view', 'create', 'edit', 'delete'],
        ];

        foreach ($modulos as $modulo => $acoes) {
            foreach ($acoes as $acao) {
                Permission::create(['name' => "{$modulo}.{$acao}"]);
            }
        }

        // Criar roles

        // Administrador - todas as permissões
        $admin = Role::create(['name' => 'Administrador']);
        $admin->givePermissionTo(Permission::all());

        // Gestor - permissões específicas
        $gestor = Role::create(['name' => 'Gestor']);
        $gestor->givePermissionTo([
            'clientes.view', 'clientes.create', 'clientes.edit',
            'fornecedores.view', 'fornecedores.create', 'fornecedores.edit',
            'artigos.view', 'artigos.create', 'artigos.edit',
            'propostas.view', 'propostas.create', 'propostas.edit', 'propostas.fechar',
            'encomendas.view', 'encomendas.create', 'encomendas.edit', 'encomendas.fechar',
        ]);

        // Operador - apenas visualização
        $operador = Role::create(['name' => 'Operador']);
        $operador->givePermissionTo([
            'clientes.view',
            'fornecedores.view',
            'artigos.view',
            'propostas.view',
            'encomendas.view',
        ]);
    }
}

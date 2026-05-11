<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger; // ← Adicionar

class PermissaoController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        // Adicionar contagem manualmente
        foreach ($roles as $role) {
            $role->users_count = $role->users()->count();
        }

        return Inertia::render('GestaoAcessos/Permissoes/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();

        return Inertia::render('GestaoAcessos/Permissoes/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
            'estado' => 'boolean',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // LOG: Criar grupo de permissões
        $permissoesCount = count($validated['permissions'] ?? []);
        ActivityLogger::log(
            "Criou grupo de permissões: {$role->name} com {$permissoesCount} permissões",
            'Permissões'
        );

        return redirect()->route('permissoes.index')
            ->with('success', 'Grupo criado com sucesso!');
    }

    public function edit(Role $permisso)
    {
        $permissions = Permission::all();
        $rolePermissions = $permisso->permissions->pluck('name')->toArray();

        return Inertia::render('GestaoAcessos/Permissoes/Edit', [
            'role' => $permisso,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function update(Request $request, Role $permisso)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $permisso->id,
            'permissions' => 'array',
            'estado' => 'boolean',
        ]);

        $oldName = $permisso->name;
        $oldPermissionsCount = $permisso->permissions->count();

        $permisso->update(['name' => $validated['name']]);
        $permisso->syncPermissions($validated['permissions'] ?? []);

        $newPermissionsCount = count($validated['permissions'] ?? []);

        // LOG: Editar grupo de permissões
        ActivityLogger::log(
            "Editou grupo de permissões: {$oldName} → {$permisso->name} | Permissões: {$oldPermissionsCount} → {$newPermissionsCount}",
            'Permissões'
        );

        return redirect()->route('permissoes.index')
            ->with('success', 'Grupo atualizado com sucesso!');
    }

    public function destroy(Role $permisso)
    {
        $nome = $permisso->name;
        $usersCount = $permisso->users()->count();

        $permisso->delete();

        // LOG: Eliminar grupo de permissões
        ActivityLogger::log(
            "Eliminou grupo de permissões: {$nome} (tinha {$usersCount} utilizador(es))",
            'Permissões'
        );

        return redirect()->route('permissoes.index')
            ->with('success', 'Grupo removido com sucesso!');
    }
}

<?php

namespace App\Observers;

use App\Models\User;
use App\Models\GrupoPermissao;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function updated(User $user)
    {
        // Se o grupo_permissao_id mudou, sincronizar role
        if ($user->isDirty('grupo_permissao_id')) {
            $grupo = GrupoPermissao::find($user->grupo_permissao_id);
            if ($grupo) {
                $role = Role::firstOrCreate(['name' => $grupo->nome]);
                $user->syncRoles([$role->name]);
            }
        }
    }

    public function created(User $user)
    {
        $grupo = GrupoPermissao::find($user->grupo_permissao_id);
        if ($grupo) {
            $role = Role::firstOrCreate(['name' => $grupo->nome]);
            $user->assignRole($role->name);
        }
    }
}

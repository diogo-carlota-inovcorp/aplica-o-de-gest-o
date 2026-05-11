<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GrupoPermissao;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Helpers\ActivityLogger; // ← Adicionar

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('grupoPermissao')->get();

        return Inertia::render('GestaoAcessos/Utilizadores/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $grupos = GrupoPermissao::where('estado', true)->get();

        return Inertia::render('GestaoAcessos/Utilizadores/Create', [
            'grupos' => $grupos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telemovel' => 'nullable|string|max:20',
            'grupo_permissao_id' => 'required|exists:grupos_permissao,id',
            'estado' => 'boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $grupo = GrupoPermissao::find($validated['grupo_permissao_id']);
        if ($grupo) {
            $role = Role::firstOrCreate(['name' => $grupo->nome]);
            $user->assignRole($role->name);
        }

        // LOG: Criar utilizador
        ActivityLogger::log(
            "Criou utilizador: {$user->name} | Email: {$user->email} | Grupo: {$grupo->nome}",
            'Utilizadores'
        );

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso!');
    }


    public function edit(User $user)
    {
        $grupos = GrupoPermissao::where('estado', true)->get();

        return Inertia::render('GestaoAcessos/Utilizadores/Edit', [
            'user' => $user,
            'grupos' => $grupos
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telemovel' => 'nullable|string|max:20',
            'grupo_permissao_id' => 'required|exists:grupos_permissao,id',
            'estado' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $oldName = $user->name;
        $oldEmail = $user->email;
        $oldGrupo = $user->grupoPermissao->nome ?? 'N/A';

        $user->update($validated);

        $grupo = GrupoPermissao::find($validated['grupo_permissao_id']);
        if ($grupo) {
            $role = Role::firstOrCreate(['name' => $grupo->nome]);
            $user->syncRoles([$role->name]);
        }

        // LOG: Editar utilizador
        ActivityLogger::log(
            "Editou utilizador: {$oldName} → {$user->name} | Email: {$oldEmail} → {$user->email} | Grupo: {$oldGrupo} → {$grupo->nome}",
            'Utilizadores'
        );

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Impedir exclusão do próprio usuário
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não pode excluir seu próprio usuário.');
        }

        $nome = $user->name;
        $email = $user->email;

        $user->delete();

        // LOG: Eliminar utilizador
        ActivityLogger::log(
            "Eliminou utilizador: {$nome} | Email: {$email}",
            'Utilizadores'
        );

        return redirect()->route('users.index')
            ->with('success', 'Utilizador removido com sucesso!');
    }
}

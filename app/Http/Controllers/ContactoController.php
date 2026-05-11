<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactoController extends Controller
{
    public function index()
    {
        $contactos = Contacto::with(['entidade', 'funcao'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('contactos/Index', [
            'contactos' => $contactos
        ]);
    }

    public function create()
    {
        return Inertia::render('contactos/Create', [
            'entidades' => Entidade::orderBy('nome')->get(),
            'funcoes' => Funcao::orderBy('nome')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:contactos',
            'entidade_id' => 'required|exists:entidades,id',
            'nome' => 'required|max:255',
            'apelido' => 'nullable|max:255',
            'funcao_id' => 'nullable|exists:funcoes,id',
            'telefone' => 'nullable|max:20',
            'telemovel' => 'nullable|max:20',
            'email' => 'nullable|email|unique:contactos',
            'consentimento_rgpd' => 'boolean',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        $validated['estado'] = $request->estado ? 'ativo' : 'inativo';
        $validated['consentimento_rgpd'] = $request->consentimento_rgpd ? 'sim' : 'nao';

        Contacto::create($validated);

        return redirect()->route('contactos.index')
            ->with('success', 'Contacto criado com sucesso!');
    }

    public function edit($id)
    {
        $contacto = Contacto::with(['entidade', 'funcao'])->findOrFail($id);

        // Converter para formato compatível com o formulário
        $contacto->consentimento_rgpd = $contacto->consentimento_rgpd === 'sim';
        $contacto->estado = $contacto->estado === 'ativo';

        return Inertia::render('contactos/Edit', [
            'contacto' => $contacto,
            'entidades' => Entidade::orderBy('nome')->get(),
            'funcoes' => Funcao::orderBy('nome')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $contacto = Contacto::findOrFail($id);

        $validated = $request->validate([
            'numero' => 'required|unique:contactos,numero,' . $id,
            'entidade_id' => 'required|exists:entidades,id',
            'nome' => 'required|max:255',
            'apelido' => 'nullable|max:255',
            'funcao_id' => 'nullable|exists:funcoes,id',
            'telefone' => 'nullable|max:20',
            'telemovel' => 'nullable|max:20',
            'email' => 'nullable|email|unique:contactos,email,' . $id,
            'consentimento_rgpd' => 'boolean',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        $validated['estado'] = $request->estado ? 'ativo' : 'inativo';
        $validated['consentimento_rgpd'] = $request->consentimento_rgpd ? 'sim' : 'nao';

        $contacto->update($validated);

        return redirect()->route('contactos.index')
            ->with('success', 'Contacto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $contacto = Contacto::findOrFail($id);
        $contacto->delete();

        return redirect()->route('contactos.index')
            ->with('success', 'Contacto excluído com sucesso!');
    }

    public function validateEmail(Request $request)
    {
        $query = Contacto::where('email', $request->email);

        if ($request->has('exclude_id')) {
            $query->where('id', '!=', $request->exclude_id);
        }

        return response()->json([
            'exists' => $query->exists()
        ]);
    }
}

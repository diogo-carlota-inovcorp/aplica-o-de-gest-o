<?php

namespace App\Http\Controllers;

use App\Models\Artigo;
use App\Models\Iva;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger; // ← Adicionar

class ArtigosController extends Controller
{
    public function index()
    {
        $artigos = Artigo::with('iva')->get();

        return Inertia::render('Artigos/Index', [
            'artigos' => $artigos
        ]);
    }

    public function create()
    {
        $ivas = Iva::where('ativo', true)->get();

        return Inertia::render('Artigos/Create', [
            'ivas' => $ivas
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'referencia' => 'required|string|max:50|unique:artigos',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_id' => 'required|exists:ivas,id',
            'foto' => 'nullable|image|max:2048',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('artigos', 'public');
            $validated['foto'] = $path;
        }

        $artigo = Artigo::create($validated);

        // LOG: Criar artigo
        ActivityLogger::log(
            "Criou artigo: {$artigo->nome} (Ref: {$artigo->referencia}) - Preço: €{$artigo->preco}",
            'Artigos'
        );

        return redirect()->route('artigos.index')
            ->with('success', 'Artigo criado com sucesso!');
    }

    public function edit(Artigo $artigo)
    {
        $ivas = Iva::where('ativo', true)->get();

        return Inertia::render('Artigos/Edit', [
            'artigo' => $artigo,
            'ivas' => $ivas
        ]);
    }

    public function update(Request $request, Artigo $artigo)
    {
        $validated = $request->validate([
            'referencia' => 'required|string|max:50|unique:artigos,referencia,' . $artigo->id,
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_id' => 'required|exists:ivas,id',
            'foto' => 'nullable|image|max:2048',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            if ($artigo->foto) {
                \Storage::disk('public')->delete($artigo->foto);
            }
            $path = $request->file('foto')->store('artigos', 'public');
            $validated['foto'] = $path;
        }

        $oldNome = $artigo->nome;
        $oldPreco = $artigo->preco;

        $artigo->update($validated);

        // LOG: Editar artigo
        ActivityLogger::log(
            "Editou artigo: {$oldNome} → {$artigo->nome} | Preço: €{$oldPreco} → €{$artigo->preco}",
            'Artigos'
        );

        return redirect()->route('artigos.index')
            ->with('success', 'Artigo atualizado com sucesso!');
    }

    public function destroy(Artigo $artigo)
    {
        $nome = $artigo->nome;
        $referencia = $artigo->referencia;

        if ($artigo->foto) {
            \Storage::disk('public')->delete($artigo->foto);
        }

        $artigo->delete();

        // LOG: Eliminar artigo
        ActivityLogger::log(
            "Eliminou artigo: {$nome} (Ref: {$referencia})",
            'Artigos'
        );

        return redirect()->route('artigos.index')
            ->with('success', 'Artigo removido com sucesso!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger;

class PlanoController extends Controller
{
    public function index()
    {
        $planos = Plano::where('ativo', true)->orderBy('preco_mensal')->get();

        return Inertia::render('Planos/Index', [
            'planos' => $planos
        ]);
    }

    // API para buscar planos disponíveis
    public function planosDisponiveis()
    {
        $planos = Plano::where('ativo', true)->orderBy('preco_mensal')->get();

        return response()->json($planos);
    }

    public function create()
    {
        return Inertia::render('Planos/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:planos',
            'descricao' => 'nullable|string',
            'preco_mensal' => 'required|numeric|min:0',
            'preco_anual' => 'required|numeric|min:0',
            'funcionalidades' => 'nullable|array',
            'limites' => 'nullable|array',
            'tem_periodo_teste' => 'boolean',
            'dias_teste' => 'integer|min:0',
            'ativo' => 'boolean',
        ]);

        // Gerar slug
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['nome']);

        $plano = Plano::create($validated);

        ActivityLogger::log(
            "Criou plano: {$plano->nome} - €{$plano->preco_mensal}/mês",
            'Planos'
        );

        return redirect()->route('planos.index')
            ->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plano $plano)
    {
        return Inertia::render('Planos/Edit', [
            'plano' => $plano
        ]);
    }

    public function update(Request $request, Plano $plano)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:planos,nome,' . $plano->id,
            'descricao' => 'nullable|string',
            'preco_mensal' => 'required|numeric|min:0',
            'preco_anual' => 'required|numeric|min:0',
            'funcionalidades' => 'nullable|array',
            'limites' => 'nullable|array',
            'tem_periodo_teste' => 'boolean',
            'dias_teste' => 'integer|min:0',
            'ativo' => 'boolean',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['nome']);

        $plano->update($validated);

        ActivityLogger::log(
            "Editou plano: {$plano->nome}",
            'Planos'
        );

        return redirect()->route('planos.index')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plano $plano)
    {
        $nome = $plano->nome;
        $plano->delete();

        ActivityLogger::log(
            "Removeu plano: {$nome}",
            'Planos'
        );

        return redirect()->route('planos.index')
            ->with('success', 'Plano removido com sucesso!');
    }
}

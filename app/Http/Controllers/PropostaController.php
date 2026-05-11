<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Models\Entidade;
use App\Models\Artigo;
use App\Models\Encomenda;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger; // ← Adicionar

class PropostaController extends Controller
{
    public function index()
    {
        $propostas = Proposta::with('cliente')->get();

        return Inertia::render('Propostas/Index', [
            'propostas' => $propostas
        ]);
    }

    public function create()
    {
        $clientes = Entidade::where('tipo', 'cliente')->where('estado', true)->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->where('estado', true)->get();
        $artigos = Artigo::where('estado', true)->get();

        return Inertia::render('Propostas/Create', [
            'clientes' => $clientes,
            'fornecedores' => $fornecedores,
            'artigos' => $artigos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:propostas',
            'data_proposta' => 'required|date',
            'validade' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'nullable|exists:entidades,id',
            'linhas.*.preco_custo' => 'required|numeric|min:0',
            'linhas.*.quantidade' => 'required|integer|min:1',
        ]);

        $total = 0;

        foreach ($validated['linhas'] as &$linha) {
            $linha['subtotal'] = $linha['preco_custo'] * $linha['quantidade'];
            $total += $linha['subtotal'];
        }

        $proposta = Proposta::create([
            'numero' => $validated['numero'],
            'data_proposta' => $validated['data_proposta'],
            'validade' => $validated['validade'],
            'cliente_id' => $validated['cliente_id'],
            'total' => $total,
            'estado' => $request->estado ?? 'rascunho',
        ]);

        foreach ($validated['linhas'] as $linha) {
            $proposta->linhas()->create([
                'artigo_id' => $linha['artigo_id'],
                'fornecedor_id' => $linha['fornecedor_id'] ?? null,
                'preco_custo' => $linha['preco_custo'],
                'quantidade' => $linha['quantidade'],
                'subtotal' => $linha['subtotal'],
            ]);
        }

        // LOG: Criar proposta
        $cliente = Entidade::find($validated['cliente_id']);
        ActivityLogger::log(
            "Criou proposta nº {$proposta->numero} para cliente: {$cliente->nome} - Total: €{$total}",
            'Propostas'
        );

        return redirect()->route('propostas.index')
            ->with('success', 'Proposta criada com sucesso!');
    }

    public function edit(Proposta $proposta)
    {
        $clientes = Entidade::where('tipo', 'cliente')->where('estado', true)->get();
        $artigos = Artigo::where('estado', true)->get();

        $proposta->load('linhas.artigo', 'linhas.fornecedor');

        return Inertia::render('Propostas/Edit', [
            'proposta' => $proposta,
            'clientes' => $clientes,
            'artigos' => $artigos
        ]);
    }

    public function update(Request $request, Proposta $proposta)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:propostas,numero,' . $proposta->id,
            'data_proposta' => 'required|date',
            'validade' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'nullable|exists:entidades,id',
            'linhas.*.preco_custo' => 'required|numeric|min:0',
            'linhas.*.quantidade' => 'required|integer|min:1',
        ]);

        $total = 0;
        foreach ($validated['linhas'] as &$linha) {
            $linha['subtotal'] = $linha['preco_custo'] * $linha['quantidade'];
            $total += $linha['subtotal'];
        }

        $oldEstado = $proposta->estado;
        $oldNumero = $proposta->numero;

        $proposta->update([
            'numero' => $validated['numero'],
            'data_proposta' => $validated['data_proposta'],
            'validade' => $validated['validade'],
            'cliente_id' => $validated['cliente_id'],
            'total' => $total,
            'estado' => $request->estado ?? $proposta->estado,
        ]);

        // Remover linhas antigas e adicionar novas
        $proposta->linhas()->delete();
        foreach ($validated['linhas'] as $linha) {
            $proposta->linhas()->create([
                'artigo_id' => $linha['artigo_id'],
                'fornecedor_id' => $linha['fornecedor_id'] ?? null,
                'preco_custo' => $linha['preco_custo'],
                'quantidade' => $linha['quantidade'],
                'subtotal' => $linha['subtotal'],
            ]);
        }

        // LOG: Editar proposta
        $cliente = Entidade::find($validated['cliente_id']);
        ActivityLogger::log(
            "Editou proposta nº {$oldNumero} → {$proposta->numero} | Cliente: {$cliente->nome} | Estado: {$oldEstado} → {$proposta->estado}",
            'Propostas'
        );

        return redirect()->route('propostas.index')
            ->with('success', 'Proposta atualizada com sucesso!');
    }

    public function destroy(Proposta $proposta)
    {
        $numero = $proposta->numero;
        $cliente = $proposta->cliente?->nome ?? 'N/A';

        $proposta->delete();

        // LOG: Eliminar proposta
        ActivityLogger::log(
            "Eliminou proposta nº {$numero} (Cliente: {$cliente})",
            'Propostas'
        );

        return redirect()->route('propostas.index')
            ->with('success', 'Proposta removida com sucesso!');
    }

    public function gerarPdf(Proposta $proposta)
    {
        // LOG: Download PDF
        ActivityLogger::log(
            "Gerou PDF da proposta nº {$proposta->numero}",
            'Propostas'
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.proposta', compact('proposta'));
        return $pdf->download("proposta_{$proposta->numero}.pdf");
    }

    public function converterParaEncomenda(Proposta $proposta)
    {
        $encomenda = Encomenda::create([
            'proposta_id' => $proposta->id,
            'numero' => 'ENC-' . $proposta->numero,
            'cliente_id' => $proposta->cliente_id,
            'data_encomenda' => now(),
            'estado' => 'rascunho'
        ]);

        foreach ($proposta->linhas as $linha) {
            $encomenda->linhas()->create($linha->toArray());
        }

        // LOG: Converter proposta para encomenda
        ActivityLogger::log(
            "Converteu proposta nº {$proposta->numero} em encomenda nº {$encomenda->numero}",
            'Propostas'
        );

        return redirect()->route('encomendas.edit', $encomenda);
    }
}

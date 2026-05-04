<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Entidade;
use App\Models\Artigo;
use App\Models\Proposta;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger; // ← Adicionar

class EncomendaController extends Controller
{
    public function index()
    {
        $encomendas = Encomenda::with('cliente')->get();

        return Inertia::render('Encomendas/Index', [
            'encomendas' => $encomendas
        ]);
    }

    public function create()
    {
        $clientes = Entidade::where('tipo', 'cliente')->where('estado', true)->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->where('estado', true)->get();
        $artigos = Artigo::where('estado', true)->get();

        return Inertia::render('Encomendas/Create', [
            'clientes' => $clientes,
            'fornecedores' => $fornecedores,
            'artigos' => $artigos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:encomendas',
            'data_encomenda' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'estado' => 'required|in:rascunho,fechado',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'required|exists:entidades,id',
            'linhas.*.preco_custo' => 'required|numeric|min:0',
            'linhas.*.quantidade' => 'required|integer|min:1',
        ]);

        // Calcular total
        $total = 0;
        foreach ($validated['linhas'] as $linha) {
            $total += $linha['preco_custo'] * $linha['quantidade'];
        }

        // Criar encomenda
        $encomenda = Encomenda::create([
            'numero' => $validated['numero'],
            'data_encomenda' => $validated['data_encomenda'],
            'cliente_id' => $validated['cliente_id'],
            'total' => $total,
            'estado' => $validated['estado'],
        ]);

        // Criar linhas
        foreach ($validated['linhas'] as $linha) {
            $encomenda->linhas()->create([
                'artigo_id' => $linha['artigo_id'],
                'fornecedor_id' => $linha['fornecedor_id'],
                'preco_custo' => $linha['preco_custo'],
                'quantidade' => $linha['quantidade'],
                'subtotal' => $linha['preco_custo'] * $linha['quantidade'],
            ]);
        }

        // LOG: Criar encomenda
        $cliente = Entidade::find($validated['cliente_id']);
        ActivityLogger::log(
            "Criou encomenda nº {$encomenda->numero} para cliente: {$cliente->nome} - Total: €{$total}",
            'Encomendas'
        );

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda criada com sucesso!');
    }

    public function edit(Encomenda $encomenda)
    {
        $clientes = Entidade::where('tipo', 'cliente')->where('estado', true)->get();
        $fornecedores = Entidade::where('tipo', 'fornecedor')->where('estado', true)->get();
        $artigos = Artigo::where('estado', true)->get();

        $encomenda->load('linhas.artigo', 'linhas.fornecedor');

        return Inertia::render('Encomendas/Edit', [
            'encomenda' => $encomenda,
            'clientes' => $clientes,
            'fornecedores' => $fornecedores,
            'artigos' => $artigos
        ]);
    }

    public function update(Request $request, Encomenda $encomenda)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:encomendas,numero,' . $encomenda->id,
            'data_encomenda' => 'required|date',
            'cliente_id' => 'required|exists:entidades,id',
            'estado' => 'required|in:rascunho,fechado',
            'linhas' => 'required|array|min:1',
            'linhas.*.artigo_id' => 'required|exists:artigos,id',
            'linhas.*.fornecedor_id' => 'required|exists:entidades,id',
            'linhas.*.preco_custo' => 'required|numeric|min:0',
            'linhas.*.quantidade' => 'required|integer|min:1',
        ]);

        // Calcular total
        $total = 0;
        foreach ($validated['linhas'] as $linha) {
            $total += $linha['preco_custo'] * $linha['quantidade'];
        }

        $oldEstado = $encomenda->estado;
        $oldNumero = $encomenda->numero;

        // Atualizar encomenda
        $encomenda->update([
            'numero' => $validated['numero'],
            'data_encomenda' => $validated['data_encomenda'],
            'cliente_id' => $validated['cliente_id'],
            'total' => $total,
            'estado' => $validated['estado'],
        ]);

        // Remover linhas antigas e adicionar novas
        $encomenda->linhas()->delete();
        foreach ($validated['linhas'] as $linha) {
            $encomenda->linhas()->create([
                'artigo_id' => $linha['artigo_id'],
                'fornecedor_id' => $linha['fornecedor_id'],
                'preco_custo' => $linha['preco_custo'],
                'quantidade' => $linha['quantidade'],
                'subtotal' => $linha['preco_custo'] * $linha['quantidade'],
            ]);
        }

        // LOG: Editar encomenda
        $cliente = Entidade::find($validated['cliente_id']);
        ActivityLogger::log(
            "Editou encomenda nº {$oldNumero} → {$encomenda->numero} | Cliente: {$cliente->nome} | Estado: {$oldEstado} → {$validated['estado']}",
            'Encomendas'
        );

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda atualizada com sucesso!');
    }

    public function destroy(Encomenda $encomenda)
    {
        $numero = $encomenda->numero;
        $cliente = $encomenda->cliente?->nome ?? 'N/A';

        $encomenda->delete();

        // LOG: Eliminar encomenda
        ActivityLogger::log(
            "Eliminou encomenda nº {$numero} (Cliente: {$cliente})",
            'Encomendas'
        );

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomenda removida com sucesso!');
    }

    // Converter proposta em encomenda
    public function converterDeProposta(Proposta $proposta)
    {
        if ($proposta->estado !== 'fechado') {
            return back()->with('error', 'A proposta precisa estar fechada para converter.');
        }

        $ultimoNumero = Encomenda::max('numero');
        $numero = 'ENC-' . str_pad((intval(substr($ultimoNumero, 4)) + 1), 6, '0', STR_PAD_LEFT);

        $encomenda = Encomenda::create([
            'numero' => $numero,
            'data_encomenda' => now(),
            'cliente_id' => $proposta->cliente_id,
            'total' => $proposta->total,
            'estado' => 'rascunho',
            'proposta_id' => $proposta->id,
        ]);

        foreach ($proposta->linhas as $linha) {
            $encomenda->linhas()->create([
                'artigo_id' => $linha->artigo_id,
                'fornecedor_id' => $linha->fornecedor_id,
                'preco_custo' => $linha->preco_custo,
                'quantidade' => $linha->quantidade,
                'subtotal' => $linha->subtotal,
            ]);
        }

        // LOG: Converter proposta em encomenda
        ActivityLogger::log(
            "Converteu proposta nº {$proposta->numero} em encomenda nº {$encomenda->numero}",
            'Encomendas'
        );

        return redirect()->route('encomendas.edit', $encomenda);
    }

    // Converter encomenda fechada em encomendas por fornecedor
    public function converterParaFornecedores(Encomenda $encomenda)
    {
        if ($encomenda->estado !== 'fechado') {
            return back()->with('error', 'A encomenda precisa estar fechada para converter.');
        }

        $linhasPorFornecedor = $encomenda->linhas->groupBy('fornecedor_id');
        $count = 0;

        foreach ($linhasPorFornecedor as $fornecedorId => $linhas) {
            $fornecedor = Entidade::find($fornecedorId);
            $totalFornecedor = $linhas->sum('subtotal');
            $numeroFornecedor = 'PO-' . ($fornecedor->codigo ?? 'FOR') . '-' . date('Ymd') . '-' . rand(100, 999);

            \DB::table('encomenda_fornecedor')->insert([
                'encomenda_id' => $encomenda->id,
                'fornecedor_id' => $fornecedorId,
                'numero_fornecedor' => $numeroFornecedor,
                'total' => $totalFornecedor,
                'estado' => 'pendente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }

        // LOG: Converter encomenda para fornecedores
        ActivityLogger::log(
            "Converteu encomenda nº {$encomenda->numero} em {$count} encomenda(s) de fornecedor",
            'Encomendas'
        );

        return redirect()->route('encomendas.index')
            ->with('success', 'Encomendas de fornecedor criadas com sucesso!');
    }

    public function gerarPdf(Encomenda $encomenda)
    {
        // LOG: Download PDF
        ActivityLogger::log(
            "Gerou PDF da encomenda nº {$encomenda->numero}",
            'Encomendas'
        );

        $encomenda->load('cliente', 'linhas.artigo');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.encomenda', compact('encomenda'));
        return $pdf->download("encomenda_{$encomenda->numero}.pdf");
    }
}

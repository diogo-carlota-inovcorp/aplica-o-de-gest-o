<?php

namespace App\Http\Controllers;

use App\Models\FaturaFornecedor;
use App\Models\Entidade;
use App\Models\EncomendaFornecedor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\ComprovativoPagamentoMail;
use App\Notifications\FaturaPagaNotification;
use App\Helpers\ActivityLogger; // ← Adicionar


class FaturaFornecedorController extends Controller
{
    public function index()
    {
        $faturas = FaturaFornecedor::with(['fornecedor', 'encomendaFornecedor'])->get();

        return Inertia::render('Financeiro/FaturasFornecedor/Index', [
            'faturas' => $faturas
        ]);
    }

    public function create()
    {
        $fornecedores = Entidade::where('tipo', 'fornecedor')->where('estado', true)->get();
        $encomendasFornecedor = EncomendaFornecedor::where('estado', '!=', 'entregue')->get();

        return Inertia::render('Financeiro/FaturasFornecedor/Create', [
            'fornecedores' => $fornecedores,
            'encomendasFornecedor' => $encomendasFornecedor
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:faturas_fornecedor',
            'data_fatura' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_fatura',
            'fornecedor_id' => 'required|exists:entidades,id',
            'encomenda_fornecedor_id' => 'nullable|exists:encomenda_fornecedor,id',
            'valor_total' => 'required|numeric|min:0',
            'documento' => 'nullable|file|max:5120|mimes:pdf,jpeg,jpg,png',
            'estado' => 'required|in:pendente,paga',
        ]);

        // Upload do documento
        if ($request->hasFile('documento') && $request->file('documento')->isValid()) {
            $path = $request->file('documento')->store('faturas/documentos', 'public');
            $validated['documento'] = $path;
        }

        $fatura = FaturaFornecedor::create($validated);

        // LOG: Criar fatura
        $fornecedor = Entidade::find($validated['fornecedor_id']);
        ActivityLogger::log(
            "Criou fatura nº {$fatura->numero} para fornecedor: {$fornecedor->nome} - Valor: €{$fatura->valor_total}",
            'Faturas Fornecedor'
        );

        return redirect()->route('faturas-fornecedor.index')
            ->with('success', 'Fatura criada com sucesso!');
    }

    public function edit(FaturaFornecedor $faturas_fornecedor)
    {
        $fornecedores = Entidade::where('tipo', 'fornecedor')->where('estado', true)->get();
        $encomendasFornecedor = EncomendaFornecedor::all();

        return Inertia::render('Financeiro/FaturasFornecedor/Edit', [
            'fatura' => $faturas_fornecedor,
            'fornecedores' => $fornecedores,
            'encomendasFornecedor' => $encomendasFornecedor
        ]);
    }

    public function update(Request $request, FaturaFornecedor $faturas_fornecedor)
    {
        $validated = $request->validate([
            'numero' => 'required|unique:faturas_fornecedor,numero,' . $faturas_fornecedor->id,
            'data_fatura' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_fatura',
            'fornecedor_id' => 'required|exists:entidades,id',
            'encomenda_fornecedor_id' => 'nullable|exists:encomenda_fornecedor,id',
            'valor_total' => 'required|numeric|min:0',
            'documento' => 'nullable|file|max:5120',
            'estado' => 'required|in:pendente,paga',
        ]);

        // Upload do documento
        if ($request->hasFile('documento')) {
            if ($faturas_fornecedor->documento) {
                Storage::disk('public')->delete($faturas_fornecedor->documento);
            }
            $path = $request->file('documento')->store('faturas/documentos', 'public');
            $validated['documento'] = $path;
        }

        $estadoAnterior = $faturas_fornecedor->estado;
        $oldNumero = $faturas_fornecedor->numero;
        $oldValor = $faturas_fornecedor->valor_total;
        $fornecedorNome = $faturas_fornecedor->fornecedor->nome ?? 'N/A';

        $faturas_fornecedor->update($validated);

        // LOG: Editar fatura
        ActivityLogger::log(
            "Editou fatura nº {$oldNumero} | Fornecedor: {$fornecedorNome} | Valor: €{$oldValor} → €{$faturas_fornecedor->valor_total}",
            'Faturas Fornecedor'
        );

        // Se mudou de pendente para paga, processar pagamento
        if ($estadoAnterior === 'pendente' && $faturas_fornecedor->estado === 'paga') {
            return Inertia::modal('Financeiro/FaturasFornecedor/EnviarComprovativo', [
                'fatura' => $faturas_fornecedor
            ]);
        }

        return redirect()->route('faturas-fornecedor.index')
            ->with('success', 'Fatura atualizada com sucesso!');
    }

    public function destroy(FaturaFornecedor $faturas_fornecedor)
    {
        $numero = $faturas_fornecedor->numero;
        $fornecedor = $faturas_fornecedor->fornecedor->nome ?? 'N/A';

        if ($faturas_fornecedor->documento) {
            Storage::disk('public')->delete($faturas_fornecedor->documento);
        }
        if ($faturas_fornecedor->comprovativo_pagamento) {
            Storage::disk('public')->delete($faturas_fornecedor->comprovativo_pagamento);
        }

        $faturas_fornecedor->delete();

        // LOG: Eliminar fatura
        ActivityLogger::log(
            "Eliminou fatura nº {$numero} (Fornecedor: {$fornecedor})",
            'Faturas Fornecedor'
        );

        return redirect()->route('faturas-fornecedor.index')
            ->with('success', 'Fatura removida com sucesso!');
    }

    public function enviarComprovativo(Request $request, FaturaFornecedor $faturas_fornecedor)
    {
        $request->validate([
            'comprovativo' => 'required|file|max:5120|mimes:pdf,jpeg,jpg,png',
        ]);

        // Verificar se o arquivo foi enviado
        if ($request->hasFile('comprovativo')) {
            $file = $request->file('comprovativo');

            // Verificar se o arquivo é válido
            if ($file->isValid()) {
                $path = $file->store('faturas/comprovativos', 'public');

                if ($path) {
                    $faturas_fornecedor->update([
                        'comprovativo_pagamento' => $path,
                        'estado' => 'paga'
                    ]);

                    // LOG: Enviar comprovativo
                    ActivityLogger::log(
                        "Marcou fatura nº {$faturas_fornecedor->numero} como PAGA e enviou comprovativo",
                        'Faturas Fornecedor'
                    );

                    // Enviar email
                    $this->enviarEmailComprovativo($faturas_fornecedor, $path);

                    return redirect()->route('faturas-fornecedor.index')
                        ->with('success', 'Comprovativo enviado e fatura marcada como paga!');
                }
            }
        }

        return back()->with('error', 'Erro ao fazer upload do comprovativo. Tente novamente.');
    }

    private function processarPagamento(FaturaFornecedor $fatura, $comprovativoFile = null)
    {
        if ($comprovativoFile && $comprovativoFile->isValid()) {
            $path = $comprovativoFile->store('faturas/comprovativos', 'public');
            if ($path) {
                $fatura->update(['comprovativo_pagamento' => $path]);
                $this->enviarEmailComprovativo($fatura, $path);
            }
        }
    }

    private function enviarEmailComprovativo(FaturaFornecedor $fatura, $comprovativoPath)
    {
        $fornecedor = Entidade::find($fatura->fornecedor_id);

        if (!$fornecedor || !$fornecedor->email) {
            \Log::warning('Fornecedor sem email cadastrado', ['fornecedor_id' => $fatura->fornecedor_id]);
            return;
        }

        try {
            // Enviar a notification
            $fornecedor->notify(new FaturaPagaNotification($fatura, $comprovativoPath));

            \Log::info('Email enviado com sucesso via Notification', [
                'fatura' => $fatura->numero,
                'email' => $fornecedor->email
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email via Notification', ['error' => $e->getMessage()]);
        }
    }
}

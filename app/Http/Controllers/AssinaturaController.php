<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Plano;
use App\Models\Locatario;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger;
use App\Models\LogsAlteracoes;

class AssinaturaController extends Controller
{
    public function index()
    {
        $locatario = Locatario::where('slug', session('locatario_atual'))->first();

        if (!$locatario) {
            return redirect()->route('locatarios.index');
        }

        $assinatura = $locatario->assinatura()->with('plano')->first();
        $planos = Plano::where('ativo', true)->orderBy('preco_mensal')->get();

        return Inertia::render('Assinaturas/Index', [
            'assinatura' => $assinatura,
            'planos' => $planos,
            'locatario' => $locatario
        ]);
    }

    public function planosDisponiveis()
    {
        $planos = Plano::where('ativo', true)->orderBy('preco_mensal')->get();

        return response()->json($planos);
    }

    public function assinar(Request $request, Plano $plano)
    {
        $locatario = Locatario::where('slug', session('locatario_atual'))->first();

        if (!$locatario) {
            return redirect()->back()->with('error', 'Locatário não encontrado');
        }

        // Verificar se já tem assinatura ativa
        if ($locatario->assinatura && $locatario->assinatura->estado === 'ativa') {
            return redirect()->back()->with('error', 'Já possui uma assinatura ativa');
        }

        $assinatura = Assinatura::create([
            'locatario_id' => $locatario->id,
            'plano_id' => $plano->id,
            'ciclo_cobranca' => 'mensal',
            'estado' => 'ativa',
            'data_inicio' => now(),
            'data_fim' => $plano->tem_periodo_teste ? now()->addDays($plano->dias_teste) : now()->addMonth(),
        ]);

        ActivityLogger::log(
            "Assinou plano: {$plano->nome} para o locatário {$locatario->nome}",
            'Assinaturas'
        );

        return redirect()->route('assinaturas.index')
            ->with('success', "Assinou o plano {$plano->nome} com sucesso!");
    }

public function upgrade(Request $request, $planoId)
{
    // Buscar o plano pelo ID recebido na URL
    $novoPlano = Plano::find($planoId);

    if (!$novoPlano) {
        return redirect()->back()->with('error', 'Plano não encontrado');
    }

    $locatario = Locatario::where('slug', session('locatario_atual'))->first();

    if (!$locatario) {
        return redirect()->back()->with('error', 'Locatário não encontrado');
    }

    $assinatura = $locatario->assinatura;

    if (!$assinatura) {
        return redirect()->back()->with('error', 'Não tem assinatura ativa');
    }

    $planoAntigo = $assinatura->plano;

    // Atualizar assinatura
    $assinatura->update([
        'plano_id' => $novoPlano->id,
        'data_fim' => now()->addMonth(),
    ]);

    LogsAlteracoes::create([
    'locatario_id' => $locatario->id,
    'user_id' => auth()->id(),
    'tipo' => 'plano',
    'acao' => "Alterou plano de {$planoAntigo->nome} para {$novoPlano->nome}",
    'dados_anteriores' => ['plano_id' => $planoAntigo->id, 'plano_nome' => $planoAntigo->nome],
    'dados_novos' => ['plano_id' => $novoPlano->id, 'plano_nome' => $novoPlano->nome],
]);

    return redirect()->back()->with('success', "Plano alterado para {$novoPlano->nome} com sucesso!");
}

    public function cancelar(Request $request)
    {
        $locatario = Locatario::where('slug', session('locatario_atual'))->first();

        if (!$locatario) {
            return redirect()->back()->with('error', 'Locatário não encontrado');
        }

        $assinatura = $locatario->assinatura;

        if (!$assinatura) {
            return redirect()->back()->with('error', 'Não tem assinatura ativa');
        }

        $assinatura->update([
            'estado' => 'cancelada',
            'data_cancelamento' => now(),
            'data_fim' => now()->addDays(30), // Mantém acesso por mais 30 dias
        ]);

        ActivityLogger::log(
            "Cancelou assinatura do plano {$assinatura->plano->nome}",
            'Assinaturas'
        );

        return redirect()->route('assinaturas.index')
            ->with('success', 'Assinatura cancelada. Terá acesso até ' . $assinatura->data_fim->format('d/m/Y'));
    }

    public function fatura(Assinatura $assinatura)
    {
        // Aqui podes gerar uma fatura em PDF
        // Por enquanto, retorna os dados
        return response()->json([
            'assinatura' => $assinatura->load('plano', 'locatario'),
            'valor' => $assinatura->plano->preco_mensal,
            'periodo' => $assinatura->data_inicio->format('m/Y') . ' a ' . $assinatura->data_fim->format('m/Y'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\Locatario;
use App\Models\Plano;
use App\Models\Assinatura;
use App\Models\User;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $locatarioAtual = Locatario::where('slug', session('locatario_atual'))->first();

        if (!$locatarioAtual) {
            return Inertia::render('Dashboard', [
                'stats' => [
                    'clientes' => 0,
                    'fornecedores' => 0,
                    'contactos' => 0,
                ],
                'planoInfo' => null,
                'limites' => null,
                'utilizacao' => null,
                'assinatura' => null,
                'logsAlteracoes' => [],
            ]);
        }

        // Estatísticas básicas
        $stats = [
            'clientes' => Entidade::where('tipo', 'cliente')->where('locatario_id', $locatarioAtual->id)->count(),
            'fornecedores' => Entidade::where('tipo', 'fornecedor')->where('locatario_id', $locatarioAtual->id)->count(),
            'contactos' => Contacto::where('locatario_id', $locatarioAtual->id)->count(),
            'utilizadores' => $locatarioAtual->utilizadores()->count(),
            'artigos' => \App\Models\Artigo::where('locatario_id', $locatarioAtual->id)->count(),
            'propostas' => \App\Models\Proposta::where('locatario_id', $locatarioAtual->id)->count(),
            'encomendas' => \App\Models\Encomenda::where('locatario_id', $locatarioAtual->id)->count(),
        ];

        // Assinatura atual
        $assinatura = $locatarioAtual->assinatura()->with('plano')->first();

        // Informações do plano
        $planoInfo = null;
        $limites = null;
        $utilizacao = [];

        if ($assinatura && $assinatura->plano) {
            $plano = $assinatura->plano;
            $limites = $plano->limites ?? [];

            // Calcular utilização atual vs limites
            $utilizacao = [
                'utilizadores' => [
                    'atual' => $stats['utilizadores'],
                    'limite' => $limites['max_utilizadores'] ?? null,
                    'percentual' => $limites['max_utilizadores'] ? round(($stats['utilizadores'] / $limites['max_utilizadores']) * 100) : 0,
                ],
                'artigos' => [
                    'atual' => $stats['artigos'],
                    'limite' => $limites['max_artigos'] ?? null,
                    'percentual' => $limites['max_artigos'] ? round(($stats['artigos'] / $limites['max_artigos']) * 100) : 0,
                ],
                'propostas' => [
                    'atual' => $stats['propostas'],
                    'limite' => $limites['max_propostas_mes'] ?? null,
                    'percentual' => $limites['max_propostas_mes'] ? round(($stats['propostas'] / $limites['max_propostas_mes']) * 100) : 0,
                ],
            ];

            // Calcular dias restantes do período de teste
            $diasTesteRestantes = null;
            if ($locatarioAtual->data_fim_teste && $locatarioAtual->data_fim_teste->isFuture()) {
                $diasTesteRestantes = Carbon::now()->diffInDays($locatarioAtual->data_fim_teste, false);
                
                $diasTesteRestantes = ceil($diasTesteRestantes);
            }

            $planoInfo = [
                'nome' => $plano->nome,
                'preco_mensal' => $plano->preco_mensal,
                'preco_anual' => $plano->preco_anual,
                'funcionalidades' => $plano->funcionalidades ?? [],
                'em_teste' => $diasTesteRestantes !== null,
                'dias_teste_restantes' => $diasTesteRestantes,
                'data_fim_teste' => $locatarioAtual->data_fim_teste?->format('d/m/Y'),
                'data_fim_subscricao' => $assinatura->data_fim?->format('d/m/Y'),
            ];
        }

        // Logs de alterações de plano
        $logsAlteracoes = \App\Models\LogsAlteracoes::where('locatario_id', $locatarioAtual->id)
            ->where('tipo', 'plano')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'acao' => $log->acao,
                    'dados_anteriores' => $log->dados_anteriores,
                    'dados_novos' => $log->dados_novos,
                    'data' => $log->created_at->format('d/m/Y H:i'),
                ];
            });

        // Planos disponíveis para upgrade/downgrade
        $planosDisponiveis = Plano::where('ativo', true)->orderBy('preco_mensal')->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'planoInfo' => $planoInfo,
            'limites' => $limites,
            'utilizacao' => $utilizacao,
            'assinatura' => $assinatura,
            'logsAlteracoes' => $logsAlteracoes,
            'planosDisponiveis' => $planosDisponiveis,
            'meusLocatarios' => auth()->user()->locatarios()->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Inertia\Inertia;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        // Buscar logs reais do banco de dados
        $query = ActivityLog::with('user')->latest();

        // Aplicar filtros se existirem
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }
        if ($request->filled('utilizador_id')) {
            $query->where('user_id', $request->utilizador_id);
        }
        if ($request->filled('acao')) {
            $query->where('action', 'like', '%' . $request->acao . '%');
        }

        $logs = $query->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'data' => $log->created_at->format('Y-m-d'),
                'hora' => $log->created_at->format('H:i:s'),
                'utilizador' => $log->name ?? $log->user?->name ?? 'Sistema',
                'menu' => $log->module ?? 'Geral',
                'acao' => $log->action,
                'dispositivo' => $this->getDevice($log->user_agent),
                'ip' => $log->ip ?? '-',
            ];
        });

        // Buscar utilizadores para o filtro
        $utilizadores = \App\Models\User::select('id', 'name')->get();

        return Inertia::render('Logs/Index', [
            'logs' => $logs,
            'utilizadores' => $utilizadores,
            'filtros' => $request->only(['data_inicio', 'data_fim', 'utilizador_id', 'acao']),
            'pagination' => [
                'total' => $logs->count(),
                'per_page' => 100,
            ],
        ]);
    }

    private function getDevice($userAgent)
    {
        if (!$userAgent) return 'Desconhecido';
        if (str_contains($userAgent, 'Windows')) return 'Windows';
        if (str_contains($userAgent, 'Mac')) return 'Mac';
        if (str_contains($userAgent, 'iPhone')) return 'iPhone';
        if (str_contains($userAgent, 'Android')) return 'Android';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        return 'Desconhecido';
    }
}

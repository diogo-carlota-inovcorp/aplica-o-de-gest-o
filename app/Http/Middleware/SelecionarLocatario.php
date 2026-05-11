<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Locatario;

class SelecionarLocatario
{
    public function handle(Request $request, Closure $next)
    {
        // Se o utilizador está autenticado
        if (auth()->check()) {
            // Verificar se já tem um locatário selecionado na sessão
            $locatarioSlug = session('locatario_atual');

            if (!$locatarioSlug && $request->has('locatario')) {
                $locatarioSlug = $request->get('locatario');
            }

            if ($locatarioSlug) {
                $locatario = Locatario::where('slug', $locatarioSlug)
                    ->whereHas('utilizadores', function($q) {
                        $q->where('user_id', auth()->id());
                    })->first();

                if ($locatario && $locatario->isAtivo()) {
                    session(['locatario_atual' => $locatario->slug]);
                    $request->merge(['locatario' => $locatario]);
                    $request->route()->setParameter('locatario', $locatario);
                }
            }
        }

        return $next($request);
    }
}

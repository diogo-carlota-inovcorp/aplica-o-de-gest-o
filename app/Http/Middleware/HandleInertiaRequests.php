<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\EmpresaConfig;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request)
    {
         $locatarioAtual = null;
    if (session()->has('locatario_atual')) {
        $locatarioAtual = \App\Models\Locatario::where('slug', session('locatario_atual'))->first();
    }




    return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'empresa' => function () {
                return EmpresaConfig::getConfig();
            },
            'locatario_atual' => $locatarioAtual,
            'empresa' => $locatarioAtual, 
            'csrf_token' => csrf_token(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Iva;
use App\Models\Artigo;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Contar clientes e fornecedores
        $totalClientes = Entidade::where('tipo', 'cliente')->count();
        $totalFornecedores = Entidade::where('tipo', 'fornecedor')->count();
        $totalContactos = Contacto::count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'clientes' => $totalClientes,
                'fornecedores' => $totalFornecedores,
                'contactos' => $totalContactos,
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Contar clientes e fornecedores
        $totalClientes = Entidade::where('tipo', 'cliente')->count();
        $totalFornecedores = Entidade::where('tipo', 'fornecedor')->count();
        $totalContactos = Entidade::count(); // Ou outro modelo se tiver
        
        return Inertia::render('Dashboard', [
            'stats' => [
                'clientes' => $totalClientes,
                'fornecedores' => $totalFornecedores,
                'contactos' => $totalContactos,
            ]
        ]);
    }
}
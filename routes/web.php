<?php

use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\PaisController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - use o controller em vez de Route::inertia
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rotas de Países (Configurações)
    Route::resource('paises', PaisController::class);
    
    // Rotas de Entidades (Clientes/Fornecedores)
    Route::resource('entidades', EntidadeController::class);
    
    // Rota para validação de NIF via VIES
    Route::post('entidades/validate-nif', [EntidadeController::class, 'validateNif'])->name('entidades.validate-nif');
});

require __DIR__.'/settings.php';

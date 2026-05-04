<?php

use App\Http\Controllers\EntidadeController;
use App\Http\Controllers\PaisController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ArtigosController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\FaturaFornecedorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissaoController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\EmpresaConfigController;
 use App\Http\Controllers\CalendarioController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Configurações
    Route::resource('paises', PaisController::class);

    // Entidades
    Route::resource('entidades', EntidadeController::class);
    Route::post('entidades/validate-nif', [EntidadeController::class, 'validateNif'])->name('entidades.validate-nif');

    // Contactos
    Route::prefix('contactos')->group(function () {
        Route::get('/', [ContactoController::class, 'index'])->name('contactos.index');
        Route::get('/create', [ContactoController::class, 'create'])->name('contactos.create');
        Route::post('/', [ContactoController::class, 'store'])->name('contactos.store');
        Route::get('/{id}/edit', [ContactoController::class, 'edit'])->name('contactos.edit');
        Route::put('/{id}', [ContactoController::class, 'update'])->name('contactos.update');
        Route::delete('/{id}', [ContactoController::class, 'destroy'])->name('contactos.destroy');
    });

    // Artigos
    Route::resource('artigos', ArtigosController::class);

    // Propostas
    Route::resource('propostas', PropostaController::class);
    Route::get('/propostas/{proposta}/pdf', [PropostaController::class, 'gerarPdf'])->name('propostas.pdf');
    Route::post('/propostas/{proposta}/converter-encomenda', [PropostaController::class, 'converterParaEncomenda'])->name('propostas.converter-encomenda');

    // Encomendas
    Route::resource('encomendas', EncomendaController::class);
    Route::get('/encomendas/{encomenda}/pdf', [EncomendaController::class, 'gerarPdf'])->name('encomendas.pdf');
    Route::post('/encomendas/converter/{proposta}', [EncomendaController::class, 'converterDeProposta'])->name('encomendas.converter-proposta');
    Route::post('/encomendas/{encomenda}/converter-fornecedores', [EncomendaController::class, 'converterParaFornecedores'])->name('encomendas.converter-fornecedores');

    // Faturas Fornecedor
    Route::resource('faturas-fornecedor', FaturaFornecedorController::class);
    Route::get('/faturas-fornecedor/{faturas_fornecedor}/enviar-comprovativo', [FaturaFornecedorController::class, 'showEnviarComprovativo'])->name('faturas-fornecedor.show-comprovativo');
    Route::post('/faturas-fornecedor/{faturas_fornecedor}/enviar-comprovativo', [FaturaFornecedorController::class, 'enviarComprovativo'])->name('faturas-fornecedor.enviar-comprovativo');

    // Gestão de Acessos
    Route::resource('users', UserController::class);
    Route::resource('permissoes', PermissaoController::class);


    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');



    Route::get('/configuracoes/empresa', [EmpresaConfigController::class, 'index'])->name('empresa.config');
    Route::put('/configuracoes/empresa', [EmpresaConfigController::class, 'update'])->name('empresa.config.update');

    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::get('/calendario/eventos', [CalendarioController::class, 'getEventos'])->name('calendario.eventos');
    Route::post('/calendario', [CalendarioController::class, 'store'])->name('calendario.store');
    Route::put('/calendario/{calendario_atividade}', [CalendarioController::class, 'update'])->name('calendario.update');
    Route::delete('/calendario/{calendario_atividade}', [CalendarioController::class, 'destroy'])->name('calendario.destroy');
    Route::patch('/calendario/{calendario_atividade}/date', [CalendarioController::class, 'updateEventDate'])->name('calendario.update-date');
});

require __DIR__.'/settings.php';

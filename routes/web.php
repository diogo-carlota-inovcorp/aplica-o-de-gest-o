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
use App\Http\Controllers\LocatarioController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\AssinaturaController;
use App\Http\Controllers\CalendarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});
// API para planos
Route::get('/api/planos-disponiveis', [PlanoController::class, 'planosDisponiveis'])->name('api.planos-disponiveis');

Route::get('/api/meus-locatarios', [LocatarioController::class, 'meusLocatarios'])->name('api.meus-locatarios');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =============================================
    // MÓDULO MULTI-TENANT (LOCATÁRIOS)
    // =============================================

    // Trocar de locatário
    Route::get('/mudar-locatario/{slug}', [LocatarioController::class, 'mudarLocatario'])->name('locatarios.mudar');

    // API para buscar os meus locatários
    Route::get('/api/meus-locatarios', [LocatarioController::class, 'meusLocatarios'])->name('api.meus-locatarios');

    // Gestão de Locatários (para o proprietário)
    Route::prefix('locatarios')->group(function () {
        Route::get('/', [LocatarioController::class, 'index'])->name('locatarios.index');
        Route::get('/criar', [LocatarioController::class, 'create'])->name('locatarios.create');
        Route::post('/', [LocatarioController::class, 'store'])->name('locatarios.store');
        Route::get('/{locatario}/editar', [LocatarioController::class, 'edit'])->name('locatarios.edit');
        Route::put('/{locatario}', [LocatarioController::class, 'update'])->name('locatarios.update');
        Route::delete('/{locatario}', [LocatarioController::class, 'destroy'])->name('locatarios.destroy');

        // Convidar utilizador para o locatário
        Route::post('/{locatario}/convidar', [LocatarioController::class, 'convidar'])->name('locatarios.convidar');

        // Remover utilizador do locatário
        Route::delete('/{locatario}/utilizador/{user}', [LocatarioController::class, 'removerUtilizador'])->name('locatarios.remover-utilizador');

        // Onboarding
        Route::get('/{locatario}/onboarding', [LocatarioController::class, 'onboarding'])->name('locatarios.onboarding');
        Route::post('/{locatario}/finalizar-onboarding', [LocatarioController::class, 'finalizarOnboarding'])->name('locatarios.finalizar-onboarding');
    });

    // Gestão de Planos (apenas administradores do sistema)
    Route::prefix('planos')->middleware(['can:admin'])->group(function () {
        Route::get('/', [PlanoController::class, 'index'])->name('planos.index');
        Route::get('/criar', [PlanoController::class, 'create'])->name('planos.create');
        Route::post('/', [PlanoController::class, 'store'])->name('planos.store');
        Route::get('/{plano}/editar', [PlanoController::class, 'edit'])->name('planos.edit');
        Route::put('/{plano}', [PlanoController::class, 'update'])->name('planos.update');
        Route::delete('/{plano}', [PlanoController::class, 'destroy'])->name('planos.destroy');

    });

    // Gestão de Assinaturas (para o locatário atual)
 Route::prefix('assinaturas')->group(function () {
    Route::get('/', [AssinaturaController::class, 'index'])->name('assinaturas.index');
    Route::get('/planos', [AssinaturaController::class, 'planosDisponiveis'])->name('assinaturas.planos');
    Route::post('/assinar/{plano}', [AssinaturaController::class, 'assinar'])->name('assinaturas.assinar');
    Route::post('/upgrade/{planoId}', [AssinaturaController::class, 'upgrade'])->name('assinaturas.upgrade');
    Route::post('/cancelar', [AssinaturaController::class, 'cancelar'])->name('assinaturas.cancelar');
    Route::get('/fatura/{assinatura}', [AssinaturaController::class, 'fatura'])->name('assinaturas.fatura');
});
    // =============================================
    // MÓDULO CALENDÁRIO
    // =============================================

    Route::prefix('calendario')->group(function () {
        Route::get('/', [CalendarioController::class, 'index'])->name('calendario.index');
        Route::get('/eventos', [CalendarioController::class, 'getEventos'])->name('calendario.eventos');
        Route::post('/', [CalendarioController::class, 'store'])->name('calendario.store');
        Route::put('/{calendario_atividade}', [CalendarioController::class, 'update'])->name('calendario.update');
        Route::delete('/{calendario_atividade}', [CalendarioController::class, 'destroy'])->name('calendario.destroy');
        Route::patch('/{calendario_atividade}/date', [CalendarioController::class, 'updateEventDate'])->name('calendario.update-date');
    });

    // =============================================
    // MÓDULOS EXISTENTES
    // =============================================

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

    // Logs
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

    // Configurações da Empresa (Locatário atual)
    Route::get('/configuracoes/empresa', [LocatarioController::class, 'configuracoes'])->name('empresa.config');
    Route::put('/configuracoes/empresa', [LocatarioController::class, 'atualizarConfiguracoes'])->name('empresa.config.update');
});

require __DIR__.'/settings.php';

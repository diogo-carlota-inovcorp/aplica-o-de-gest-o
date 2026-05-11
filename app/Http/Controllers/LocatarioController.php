<?php

namespace App\Http\Controllers;

use App\Models\Locatario;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Helpers\ActivityLogger;

class LocatarioController extends Controller
{
    public function index()
    {
        $meusLocatarios = auth()->user()->locatarios()->with('assinatura.plano')->get();

        return Inertia::render('Locatarios/Index', [
            'locatarios' => $meusLocatarios,
            'locatarioAtual' => session('locatario_atual')
        ]);
    }

    public function create()
    {
        $planos = Plano::where('ativo', true)->get();

        return Inertia::render('Locatarios/Create', [
            'planos' => $planos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'plano_id' => 'required|exists:planos,id',
        ]);

        // Criar slug único
        $slug = Str::slug($validated['nome']);
        $originalSlug = $slug;
        $counter = 1;
        while (Locatario::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $plano = Plano::find($validated['plano_id']);

        $locatario = Locatario::create([
            'nome' => $validated['nome'],
            'slug' => $slug,
            'email' => $validated['email'],
            'estado' => 'ativo',
            'data_fim_teste' => $plano->tem_periodo_teste ? now()->addDays($plano->dias_teste) : null,
        ]);

        // Associar utilizador atual como proprietário
        $locatario->utilizadores()->attach(auth()->id(), [
            'funcao' => 'proprietario',
            'ativo' => true,
            'aceitou_em' => now(),
        ]);

        // Criar assinatura
        $locatario->assinatura()->create([
            'plano_id' => $plano->id,
            'ciclo_cobranca' => 'mensal',
            'estado' => $plano->preco_mensal > 0 ? 'ativa' : 'ativa',
            'data_inicio' => now(),
            'data_fim' => $plano->tem_periodo_teste ? now()->addDays($plano->dias_teste) : null,
        ]);

        ActivityLogger::log(
            "Criou novo locatário: {$locatario->nome}",
            'Locatários'
        );

        // Mudar para o novo locatário
        session(['locatario_atual' => $locatario->slug]);


    return redirect()->route('dashboard')->with('success', 'Empresa criada com sucesso!');
    }

    public function mudarLocatario($slug)
    {
        $locatario = Locatario::where('slug', $slug)
            ->whereHas('utilizadores', function($q) {
                $q->where('user_id', auth()->id());
            })->firstOrFail();

        session(['locatario_atual' => $locatario->slug]);

        return redirect()->back();
    }

    public function meusLocatarios()
{
    $locatarios = auth()->user()->locatarios()->select('id', 'nome', 'slug', 'email')->get();

    return response()->json($locatarios);
}

    public function onboarding(Locatario $locatario)
    {
        return Inertia::render('Locatarios/Onboarding', [
            'locatario' => $locatario,
            'passos' => [
                ['nome' => 'Perfil da Empresa', 'completo' => !empty($locatario->configuracoes)],
                ['nome' => 'Equipa', 'completo' => $locatario->utilizadores()->count() > 1],
                ['nome' => 'Primeiros Dados', 'completo' => false],
            ]
        ]);
    }

    public function finalizarOnboarding(Locatario $locatario)
    {
        $locatario->update(['configuracoes' => array_merge($locatario->configuracoes ?? [], [
            'onboarding_completo' => true,
            'onboarding_concluido_em' => now(),
        ])]);

        return redirect()->route('dashboard');
    }

    public function configuracoes()
    {
        $locatario = Locatario::where('slug', session('locatario_atual'))->firstOrFail();

        return Inertia::render('Configuracoes/Empresa/Index', [
            'config' => $locatario
        ]);
    }

    public function atualizarConfiguracoes(Request $request)
    {
        $locatario = Locatario::where('slug', session('locatario_atual'))->firstOrFail();

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string|max:20',
            'logotipo' => 'nullable|image|max:2048',
            'configuracoes' => 'nullable|array',
        ]);

        if ($request->hasFile('logotipo')) {
            $path = $request->file('logotipo')->store('locatarios', 'public');
            $validated['logotipo'] = $path;
        }

        $locatario->update($validated);

        return redirect()->back()->with('success', 'Configurações atualizadas!');
    }

    public function destroy(Locatario $locatario)
{
    // Verificar se o utilizador é proprietário
    if (!auth()->user()->isProprietarioDe($locatario)) {
        return redirect()->back()->with('error', 'Não tem permissão para apagar esta empresa.');
    }

    // Verificar se é o único locatário
    if (auth()->user()->locatarios()->count() <= 1) {
        return redirect()->back()->with('error', 'Não pode apagar a única empresa. Crie outra empresa primeiro.');
    }

    $nome = $locatario->nome;

    // Se for o locatário atual, mudar para outro antes de apagar
    if (session('locatario_atual') === $locatario->slug) {
        $outroLocatario = auth()->user()->locatarios()->where('locatario_id', '!=', $locatario->id)->first();
        if ($outroLocatario) {
            session(['locatario_atual' => $outroLocatario->slug]);
        }
    }

    // Apagar o locatário (cascade vai apagar assinaturas e relações)
    $locatario->delete();

    ActivityLogger::log(
        "Apagou a empresa: {$nome}",
        'Locatários'
    );

    return redirect()->route('dashboard')->with('success', 'Empresa apagada com sucesso!');
}
}

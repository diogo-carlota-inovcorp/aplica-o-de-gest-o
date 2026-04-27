<?php

namespace App\Http\Controllers;

use App\Models\Entidade;
use App\Models\Pais;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class EntidadeController extends Controller
{
    // Listagem
    public function index(Request $request)
{
    $tipo = $request->query('tipo', 'cliente');

    $entidades = Entidade::with('pais')
        ->where(function($query) use ($tipo) {
            if ($tipo === 'cliente') {
                $query->where('tipo', 'cliente')->orWhere('tipo', 'ambos');
            } elseif ($tipo === 'fornecedor') {
                $query->where('tipo', 'fornecedor')->orWhere('tipo', 'ambos');
            }
        })
        ->get();

    return Inertia::render('Entidades/Index', [
        'entidades' => $entidades,
        'tipo' => $tipo
    ]);
}

    // Formulário de criação
    public function create(Request $request)
    {
        $tipo = $request->query('tipo', 'cliente');
        $paises = Pais::all();

        return Inertia::render('Entidades/Create', [
            'tipo' => $tipo,
            'paises' => $paises
        ]);
    }

    // Validação NIF via VIES
    public function validateNif(Request $request)
    {
        $nif = $request->input('nif');
        $paisCode = $request->input('pais_code', 'PT');

        // Verificar se NIF já existe na base
        $exists = Entidade::where('nif', $nif)->exists();

        // Chamar API VIES (simplificado)
        try {
            $response = Http::timeout(5)->get('https://ec.europa.eu/taxation_customs/vies/rest-api/check-vat', [
                'countryCode' => $paisCode,
                'vatNumber' => $nif,
            ]);

            $viesData = $response->json();

            return response()->json([
                'exists' => $exists,
                'vies_valid' => $viesData['isValid'] ?? false,
                'vies_data' => $viesData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exists' => $exists,
                'vies_valid' => false,
                'error' => 'Não foi possível validar no VIES'
            ]);
        }
    }

    // Gravar
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:cliente,fornecedor,ambos',
            'nif' => 'required|string|max:20|unique:entidades',
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string',
            'codigo_postal' => 'nullable|string|regex:/^\d{4}-\d{3}$/',
            'localidade' => 'nullable|string|max:255',
            'pais_id' => 'nullable|exists:paises,id',
            'telefone' => 'nullable|string|max:20',
            'telemovel' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'consentimento_rgpd' => 'boolean',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        // Gerar número incremental
        $lastEntity = Entidade::orderBy('id', 'desc')->first();
        $validated['numero'] = 'E' . str_pad(($lastEntity?->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        Entidade::create($validated);

        return redirect()->route('entidades.index', ['tipo' => $validated['tipo']]);
    }

    // Editar
    public function edit(Entidade $entidade)
    {
        $paises = Pais::all();
        return Inertia::render('Entidades/Edit', [
            'entidade' => $entidade,
            'paises' => $paises
        ]);
    }

    // Atualizar
    public function update(Request $request, Entidade $entidade)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:cliente,fornecedor,ambos',
            'nif' => ['required', 'string', 'max:20', Rule::unique('entidades')->ignore($entidade->id)],
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string',
            'codigo_postal' => 'nullable|string|regex:/^\d{4}-\d{3}$/',
            'localidade' => 'nullable|string|max:255',
            'pais_id' => 'nullable|exists:paises,id',
            'telefone' => 'nullable|string|max:20',
            'telemovel' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'consentimento_rgpd' => 'boolean',
            'observacoes' => 'nullable|string',
            'estado' => 'boolean',
        ]);

        $entidade->update($validated);

        return redirect()->route('entidades.index', ['tipo' => $entidade->tipo]);
    }

    // Excluir
    public function destroy(Entidade $entidade)
    {
        $tipo = $entidade->tipo;
        $entidade->delete();

        return redirect()->route('entidades.index', ['tipo' => $tipo]);
    }
}

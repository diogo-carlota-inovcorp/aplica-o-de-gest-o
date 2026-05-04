<?php

namespace App\Http\Controllers;

use App\Models\EmpresaConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class EmpresaConfigController extends Controller
{
    public function index()
    {
        $config = EmpresaConfig::getConfig();

        return Inertia::render('Configuracoes/Empresa/Index', [
            'config' => $config
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:255',
            'nif' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $config = EmpresaConfig::getConfig();

        // Upload do logo
        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            $path = $request->file('logo')->store('empresa', 'public');
            $validated['logo'] = $path;
        }

        $config->update($validated);

        // Log da ação
        ActivityLogger::log(
            "Atualizou configurações da empresa",
            'Configurações'
        );

        return redirect()->route('empresa.config')
            ->with('success', 'Configurações atualizadas com sucesso!');
    }
}

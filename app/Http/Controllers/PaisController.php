<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index()
    {
        $paises = Pais::all();
        return Inertia::render('Configuracoes/Paises/Index', [
            'paises' => $paises
        ]);
    }

    public function create()
    {
        return Inertia::render('Configuracoes/Paises/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|size:2',
        ]);

        Pais::create($request->all());

        return redirect()->route('paises.index');
    }

    public function edit(Pais $pai)
    {
        return Inertia::render('Configuracoes/Paises/Edit', [
            'pais' => $pai
        ]);
    }

    public function update(Request $request, Pais $pai)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|size:2',
        ]);

        $pai->update($request->all());

        return redirect()->route('paises.index');
    }

    public function destroy(Pais $pai)
    {
        $pai->delete();
        return redirect()->route('paises.index');
    }
}

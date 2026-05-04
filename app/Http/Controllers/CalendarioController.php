<?php

namespace App\Http\Controllers;

use App\Models\CalendarioAtividade;
use App\Models\CalendarioTipo;
use App\Models\CalendarioAcao;
use App\Models\Entidade;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Helpers\ActivityLogger;

class CalendarioController extends Controller
{
    public function index()
    {
        $tipos = CalendarioTipo::where('estado', true)->get();
        $acoes = CalendarioAcao::where('estado', true)->get();
        $entidades = Entidade::where('estado', true)->get();
        $utilizadores = User::where('estado', true)->get();

        return Inertia::render('Calendario/Index', [
            'tipos' => $tipos,
            'acoes' => $acoes,
            'entidades' => $entidades,
            'utilizadores' => $utilizadores,
        ]);
    }

    public function getEventos(Request $request)
{
    $atividades = CalendarioAtividade::with(['user', 'entidade', 'tipo', 'acao'])->get();

    $eventos = [];
    foreach ($atividades as $atividade) {
        // Garantir que as horas estão no formato H:i:s (ex: 09:00:00)
        $hora_inicio = date('H:i:s', strtotime($atividade->hora_inicio));
        $hora_fim = date('H:i:s', strtotime($atividade->hora_fim));

        $eventos[] = [
            'id' => $atividade->id,
            'titulo' => $atividade->titulo,
            'data' => $atividade->data,
            'hora_inicio' => $hora_inicio,
            'hora_fim' => $hora_fim,
            'descricao' => $atividade->descricao,
            'partilha' => $atividade->partilha,
            'conhecimento' => $atividade->conhecimento,
            'estado' => $atividade->estado,
            'duracao' => $atividade->duracao,
            'entidade_id' => $atividade->entidade_id,
            'tipo_id' => $atividade->tipo_id,
            'acao_id' => $atividade->acao_id,
            'cor' => $atividade->tipo?->cor ?? '#3b82f6',
        ];
    }

    return response()->json($eventos);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required',
            'duracao' => 'required|integer|min:1',
            'partilha' => 'nullable|string|max:255',
            'conhecimento' => 'nullable|string|max:255',
            'entidade_id' => 'nullable|exists:entidades,id',
            'tipo_id' => 'nullable|exists:calendario_tipos,id',
            'acao_id' => 'nullable|exists:calendario_acoes,id',
            'estado' => 'required|in:pendente,concluida,cancelada',
        ]);

        $validated['user_id'] = auth()->id();

        $atividade = CalendarioAtividade::create($validated);

        ActivityLogger::log(
            "Criou atividade no calendário: {$atividade->titulo}",
            'Calendário'
        );

        return redirect()->back()->with('success', 'Atividade criada com sucesso!');
    }

    public function update(Request $request, CalendarioAtividade $calendario_atividade)
{
    $validated = $request->validate([
        'titulo' => 'required|string|max:255',
        'data' => 'required|date',
        'hora_inicio' => 'required',
        'hora_fim' => 'required',
        'duracao' => 'required|integer|min:1',
        'user_id' => 'required|exists:users,id',
        'entidade_id' => 'nullable|exists:entidades,id',
        'tipo_id' => 'nullable|exists:calendario_tipos,id',
        'acao_id' => 'nullable|exists:calendario_acoes,id',
        'descricao' => 'nullable|string',
        'estado' => 'required|in:pendente,concluida,cancelada',
    ]);

    $calendario_atividade->update($validated);

    return redirect()->back()->with('success', 'Atividade atualizada com sucesso!');
}

public function destroy(CalendarioAtividade $calendario_atividade)
{
    $calendario_atividade->delete();
    return redirect()->back()->with('success', 'Atividade removida com sucesso!');
}

    public function updateEventDate(Request $request, CalendarioAtividade $calendario_atividade)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'nullable|date',
        ]);

        $calendario_atividade->update([
            'data' => date('Y-m-d', strtotime($request->start)),
            'hora_inicio' => date('H:i:s', strtotime($request->start)),
            'hora_fim' => $request->end ? date('H:i:s', strtotime($request->end)) : $calendario_atividade->hora_fim,
        ]);

        return response()->json(['success' => true]);
    }
}

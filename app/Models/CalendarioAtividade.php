<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioAtividade extends Model
{
    protected $table = 'calendario_atividades';

    protected $fillable = [
        'user_id', 'entidade_id', 'tipo_id', 'acao_id', 'titulo', 'descricao',
        'data', 'hora_inicio', 'hora_fim', 'duracao', 'partilha', 'conhecimento', 'estado'
    ];

    protected $casts = [
        'data' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }

    public function tipo()
    {
        return $this->belongsTo(CalendarioTipo::class, 'tipo_id');
    }

    public function acao()
    {
        return $this->belongsTo(CalendarioAcao::class, 'acao_id');
    }
}

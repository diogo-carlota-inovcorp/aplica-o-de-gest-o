<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioAcao extends Model
{
    protected $table = 'calendario_acoes';

    protected $fillable = ['nome', 'estado'];

    protected $casts = ['estado' => 'boolean'];

    public function atividades()
    {
        return $this->hasMany(CalendarioAtividade::class, 'acao_id');
    }
}

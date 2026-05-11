<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioTipo extends Model
{
    protected $table = 'calendario_tipos';

    protected $fillable = ['nome', 'cor', 'estado'];

    protected $casts = ['estado' => 'boolean'];

    public function atividades()
    {
        return $this->hasMany(CalendarioAtividade::class, 'tipo_id');
    }
}

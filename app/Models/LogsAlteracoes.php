<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsAlteracoes extends Model
{
    protected $table = 'logs_alteracoes';

    protected $fillable = [
        'locatario_id', 'user_id', 'tipo', 'acao', 'dados_anteriores', 'dados_novos'
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

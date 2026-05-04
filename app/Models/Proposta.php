<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    protected $fillable = [
        'numero', 'data_proposta', 'validade', 'cliente_id', 'total', 'estado'
    ];

    protected $casts = [
        'data_proposta' => 'date',
        'validade' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'cliente_id');
    }

    public function linhas()
    {
        return $this->hasMany(PropostaLinha::class);
    }
}

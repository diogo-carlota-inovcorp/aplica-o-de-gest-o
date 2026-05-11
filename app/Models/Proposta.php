<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    use FiltrarPorLocatario;

    protected $fillable = [
        'numero', 'data_proposta', 'validade', 'cliente_id', 'total', 'estado',
        'locatario_id',
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

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }
}

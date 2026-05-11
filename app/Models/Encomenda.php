<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    use FiltrarPorLocatario;

    protected $fillable = [
        'numero', 'data_encomenda', 'cliente_id', 'total', 'estado', 'proposta_id',
        'locatario_id',
    ];

    protected $casts = [
        'data_encomenda' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'cliente_id');
    }

    public function linhas()
    {
        return $this->hasMany(EncomendaLinha::class);
    }

    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }
}

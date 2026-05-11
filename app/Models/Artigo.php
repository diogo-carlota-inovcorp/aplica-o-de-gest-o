<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;

class Artigo extends Model
{
    use FiltrarPorLocatario;

    protected $table = 'artigos';

    protected $fillable = [
        'referencia',
        'nome',
        'descricao',
        'preco',
        'iva_id',
        'foto',
        'observacoes',
        'estado',
        'locatario_id',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function iva()
    {
        return $this->belongsTo(Iva::class);
    }

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }
}

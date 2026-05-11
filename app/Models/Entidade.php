<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Entidade extends Model
{
    use Notifiable, FiltrarPorLocatario;

    protected $table = 'entidades';

    protected $fillable = [
        'tipo',
        'nif',
        'nome',
        'numero',
        'morada',
        'codigo_postal',
        'localidade',
        'pais_id',
        'telefone',
        'telemovel',
        'website',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
        'locatario_id',
    ];

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Entidade extends Model
{

 use Notifiable; // Adicione esta linha


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
    ];

     public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero',
        'entidade_id',
        'nome',
        'apelido',
        'funcao_id',
        'telefone',
        'telemovel',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado'
    ];

    protected $casts = [
        'consentimento_rgpd' => 'string',
        'estado' => 'string',
        'deleted_at' => 'datetime'
    ];

    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }

    public function funcao()
    {
        return $this->belongsTo(Funcao::class);
    }

    // Scope para contactos ativos
    public function scopeAtivo($query)
    {
        return $query->where('estado', 'ativo');
    }

    public function contarcontactos()
    {
        $totalContactos = Contacto::count();
        return $totalContactos;
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcao extends Model
{
    use SoftDeletes;

    // Especificar o nome correto da tabela
    protected $table = 'funcoes';  // ← IMPORTANTE: forçar o nome da tabela

    protected $fillable = [
        'nome',
        'descricao'
    ];

    protected $casts = [
        'deleted_at' => 'datetime'
    ];

    // Relação com Contactos
    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }
}

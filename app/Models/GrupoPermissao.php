<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoPermissao extends Model
{
    protected $table = 'grupos_permissao';

    protected $fillable = [
        'nome',
        'descricao',
        'permissoes',
        'estado',
    ];

    protected $casts = [
        'permissoes' => 'array',
        'estado' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'grupo_permissao_id');
    }
}

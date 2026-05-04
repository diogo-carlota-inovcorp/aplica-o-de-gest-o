<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iva extends Model
{
    protected $table = 'ivas';

    protected $fillable = [
        'nome',
        'percentagem',
        'ativo',
    ];

    protected $casts = [
        'percentagem' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function artigos()
    {
        return $this->hasMany(Artigo::class);
    }
}

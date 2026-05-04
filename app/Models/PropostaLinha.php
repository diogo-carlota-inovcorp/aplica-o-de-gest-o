<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropostaLinha extends Model
{
    protected $table = 'proposta_linhas';

    protected $fillable = [
        'proposta_id', 'artigo_id', 'fornecedor_id', 'preco_custo', 'quantidade', 'subtotal'
    ];

    public function artigo()
    {
        return $this->belongsTo(Artigo::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }
}

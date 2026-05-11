<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;

class PropostaLinha extends Model
{
    use FiltrarPorLocatario;

    protected $table = 'proposta_linhas';

    protected $fillable = [
        'proposta_id', 'artigo_id', 'fornecedor_id', 'preco_custo', 'quantidade', 'subtotal',
        'locatario_id',
    ];

    public function artigo()
    {
        return $this->belongsTo(Artigo::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }
}

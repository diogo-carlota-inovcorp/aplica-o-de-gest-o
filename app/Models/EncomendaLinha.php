<?php

namespace App\Models;

use App\Traits\FiltrarPorLocatario;
use Illuminate\Database\Eloquent\Model;

class EncomendaLinha extends Model
{
    use FiltrarPorLocatario;

    protected $table = 'encomenda_linhas';

    protected $fillable = [
        'encomenda_id', 'artigo_id', 'fornecedor_id', 'preco_custo', 'quantidade', 'subtotal',
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

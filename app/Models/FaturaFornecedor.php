<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaturaFornecedor extends Model
{
    protected $table = 'faturas_fornecedor';

    protected $fillable = [
        'numero',
        'data_fatura',
        'data_vencimento',
        'fornecedor_id',
        'encomenda_fornecedor_id',
        'valor_total',
        'documento',
        'comprovativo_pagamento',
        'estado'
    ];

    protected $casts = [
        'data_fatura' => 'date',
        'data_vencimento' => 'date',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }

    public function encomendaFornecedor()
    {
        return $this->belongsTo(EncomendaFornecedor::class, 'encomenda_fornecedor_id');
    }
}

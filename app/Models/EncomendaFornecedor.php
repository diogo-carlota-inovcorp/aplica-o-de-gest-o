<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncomendaFornecedor extends Model
{
    protected $table = 'encomenda_fornecedor';

    protected $fillable = [
        'encomenda_id',
        'fornecedor_id',
        'numero_fornecedor',
        'total',
        'estado'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    public function fornecedor()
    {
        return $this->belongsTo(Entidade::class, 'fornecedor_id');
    }

    public function faturas()
    {
        return $this->hasMany(FaturaFornecedor::class, 'encomenda_fornecedor_id');
    }
}

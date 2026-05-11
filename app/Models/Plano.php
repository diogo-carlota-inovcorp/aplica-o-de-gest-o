<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    protected $table = 'planos';

    protected $fillable = [
        'nome', 'slug', 'descricao', 'preco_mensal', 'preco_anual',
        'funcionalidades', 'limites', 'tem_periodo_teste', 'dias_teste',
        'ordem', 'ativo'
    ];

    protected $casts = [
        'funcionalidades' => 'array',
        'limites' => 'array',
        'preco_mensal' => 'decimal:2',
        'preco_anual' => 'decimal:2',
    ];

    public function assinaturas()
    {
        return $this->hasMany(Assinatura::class);
    }

    // Obter preço por ciclo
    public function getPreco($ciclo = 'mensal')
    {
        return $ciclo === 'anual' ? $this->preco_anual : $this->preco_mensal;
    }

    // Verificar limite específico
    public function getLimite($key, $default = null)
    {
        return data_get($this->limites, $key, $default);
    }
}

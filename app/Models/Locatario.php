<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locatario extends Model
{
    protected $table = 'locatarios';

    protected $fillable = [
        'nome', 'slug', 'email', 'logotipo', 'configuracoes',
        'estado', 'data_fim_teste', 'data_fim_subscricao'
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'data_fim_teste' => 'datetime',
        'data_fim_subscricao' => 'datetime',
    ];

    // Relacionamentos
    public function utilizadores()
    {
        return $this->belongsToMany(User::class, 'locatario_user')
            ->withPivot('funcao', 'permissoes', 'ativo')
            ->withTimestamps();
    }

    public function assinatura()
    {
        return $this->hasOne(Assinatura::class)->where('estado', 'ativa');
    }

    public function plano()
    {
        return $this->hasOneThrough(Plano::class, Assinatura::class);
    }

    // Verificar se está ativo
    public function isAtivo()
    {
        return $this->estado === 'ativo';
    }

    // Verificar período de teste
    public function isEmTeste()
    {
        return $this->data_fim_teste && $this->data_fim_teste->isFuture();
    }

    // Obter configuração específica
    public function getConfiguracao($key, $default = null)
    {
        return data_get($this->configuracoes, $key, $default);
    }
}

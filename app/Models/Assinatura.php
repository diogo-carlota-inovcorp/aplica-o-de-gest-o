<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assinatura extends Model
{
    protected $table = 'assinaturas';

    protected $fillable = [
        'locatario_id', 'plano_id', 'ciclo_cobranca', 'estado',
        'data_inicio', 'data_fim', 'data_cancelamento', 'metadados'
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'data_cancelamento' => 'datetime',
        'metadados' => 'array',
    ];

    public function locatario()
    {
        return $this->belongsTo(Locatario::class);
    }

    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    // Fazer upgrade de plano
    public function upgrade(Plano $novoPlano)
    {
        $this->plano_id = $novoPlano->id;
        $this->save();
    }

    // Cancelar assinatura
    public function cancelar()
    {
        $this->estado = 'cancelada';
        $this->data_cancelamento = now();
        $this->save();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaConfig extends Model
{
    protected $table = 'empresa_configs';

    protected $fillable = [
        'logo', 'nome', 'morada', 'codigo_postal', 'localidade',
        'nif', 'email', 'telefone', 'website'
    ];

    public static function getConfig()
    {
        $config = self::first();
        if (!$config) {
            $config = self::create([
                'nome' => config('app.name', 'Minha Empresa'),
            ]);
        }
        return $config;
    }
}

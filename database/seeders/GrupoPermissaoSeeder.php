<?php

namespace Database\Seeders;

use App\Models\GrupoPermissao;
use Illuminate\Database\Seeder;

class GrupoPermissaoSeeder extends Seeder
{
    public function run()
    {
        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Acesso total ao sistema',
                'permissoes' => ['*'],
                'estado' => true,
            ],
            [
                'nome' => 'Gestor',
                'descricao' => 'Gestão de clientes, artigos e propostas',
                'permissoes' => ['clientes.*', 'artigos.*', 'propostas.*'],
                'estado' => true,
            ],
            [
                'nome' => 'Operador',
                'descricao' => 'Apenas visualização de dados',
                'permissoes' => ['clientes.view', 'artigos.view'],
                'estado' => true,
            ],
        ];

        foreach ($grupos as $grupo) {
            GrupoPermissao::create($grupo);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\EmpresaConfig;
use Illuminate\Database\Seeder;

class EmpresaConfigSeeder extends Seeder
{
    public function run()
    {
        EmpresaConfig::firstOrCreate(
            ['id' => 1],
            [
                'nome' => 'Minha Empresa',
                'nif' => '123456789',
                'morada' => 'Rua Exemplo, 123',
                'codigo_postal' => '1000-001',
                'localidade' => 'Lisboa',
                'email' => 'geral@empresa.pt',
                'telefone' => '210000000',
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;
use Illuminate\Support\Facades\DB;

class PaisSeeder extends Seeder
{
    public function run()
    {
        
        
        $paises = [
            ['nome' => 'Brasil', 'codigo' => 'BR'],
            ['nome' => 'Portugal', 'codigo' => 'PT'],
            ['nome' => 'Angola', 'codigo' => 'AO'],
            ['nome' => 'Moçambique', 'codigo' => 'MZ'],
            ['nome' => 'Cabo Verde', 'codigo' => 'CV'],
            ['nome' => 'Guiné-Bissau', 'codigo' => 'GW'],
            ['nome' => 'São Tomé e Príncipe', 'codigo' => 'ST'],
            ['nome' => 'Timor-Leste', 'codigo' => 'TL'],
            ['nome' => 'Estados Unidos', 'codigo' => 'US'],
            ['nome' => 'Espanha', 'codigo' => 'ES'],
            ['nome' => 'França', 'codigo' => 'FR'],
            ['nome' => 'Alemanha', 'codigo' => 'DE'],
            ['nome' => 'Itália', 'codigo' => 'IT'],
            ['nome' => 'Reino Unido', 'codigo' => 'GB'],
            ['nome' => 'Canadá', 'codigo' => 'CA'],
        ];
        
        foreach ($paises as $pais) {
            Pais::updateOrCreate(
                ['codigo' => $pais['codigo']],
                ['nome' => $pais['nome']]
            );
        }
        
        $this->command->info('Países inseridos com sucesso!');
    }
}
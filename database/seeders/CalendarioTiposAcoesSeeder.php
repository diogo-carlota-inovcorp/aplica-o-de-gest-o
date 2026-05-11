<?php

namespace Database\Seeders;

use App\Models\CalendarioTipo;
use App\Models\CalendarioAcao;
use Illuminate\Database\Seeder;

class CalendarioTiposAcoesSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['nome' => 'Reunião', 'cor' => '#3b82f6'],
            ['nome' => 'Visita', 'cor' => '#10b981'],
            ['nome' => 'Telefonema', 'cor' => '#f59e0b'],
            ['nome' => 'Apresentação', 'cor' => '#8b5cf6'],
            ['nome' => 'Formação', 'cor' => '#ec4899'],
        ];

        foreach ($tipos as $tipo) {
            CalendarioTipo::create($tipo);
        }

        $acoes = ['Agendar', 'Confirmar', 'Cancelar', 'Reagendar', 'Concluir'];

        foreach ($acoes as $acao) {
            CalendarioAcao::create(['nome' => $acao]);
        }
    }
}

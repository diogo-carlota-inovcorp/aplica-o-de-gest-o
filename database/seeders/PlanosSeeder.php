<?php

namespace Database\Seeders;

use App\Models\Plano;
use Illuminate\Database\Seeder;

class PlanosSeeder extends Seeder
{
    public function run()
    {
        $planos = [
            [
                'nome' => 'Gratuito',
                'slug' => 'gratuito',
                'descricao' => 'Para pequenos negócios que estão a começar',
                'preco_mensal' => 0,
                'preco_anual' => 0,
                'funcionalidades' => [
                    'Até 5 utilizadores',
                    'Gestão de clientes',
                    'Artigos (até 50)',
                    'Propostas (até 10/mês)',
                    'Suporte por email',
                ],
                'limites' => [
                    'max_utilizadores' => 5,
                    'max_artigos' => 50,
                    'max_propostas_mes' => 10,
                ],
                'tem_periodo_teste' => false,
                'dias_teste' => 0,
                'ordem' => 0,
                'ativo' => true,
            ],
            [
                'nome' => 'Profissional',
                'slug' => 'profissional',
                'descricao' => 'Para empresas em crescimento',
                'preco_mensal' => 49.90,
                'preco_anual' => 499.00,
                'funcionalidades' => [
                    'Até 20 utilizadores',
                    'Gestão de clientes e fornecedores',
                    'Artigos ilimitados',
                    'Propostas ilimitadas',
                    'Encomendas',
                    'Relatórios avançados',
                    'Suporte prioritário',
                    'API completa',
                ],
                'limites' => [
                    'max_utilizadores' => 20,
                    'max_artigos' => null,
                    'max_propostas_mes' => null,
                ],
                'tem_periodo_teste' => true,
                'dias_teste' => 14,
                'ordem' => 1,
                'ativo' => true,
            ],
            [
                'nome' => 'Empresarial',
                'slug' => 'empresarial',
                'descricao' => 'Para grandes organizações',
                'preco_mensal' => 99.90,
                'preco_anual' => 999.00,
                'funcionalidades' => [
                    'Utilizadores ilimitados',
                    'Todas as funcionalidades Profissional',
                    'Suporte 24/7',
                    'SLA garantido',
                    'Treino personalizado',
                    'Onboarding dedicado',
                ],
                'limites' => [
                    'max_utilizadores' => null,
                    'max_artigos' => null,
                    'max_propostas_mes' => null,
                ],
                'tem_periodo_teste' => true,
                'dias_teste' => 30,
                'ordem' => 2,
                'ativo' => true,
            ],
        ];

        foreach ($planos as $plano) {
            Plano::create($plano);
        }
    }
}

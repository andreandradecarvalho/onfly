<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Desenvolvedor Júnior',
                'description' => 'Profissional em início de carreira, aprendendo e desenvolvendo habilidades básicas.',
                'level' => 'junior',
            ],
            [
                'name' => 'Desenvolvedor Pleno',
                'description' => 'Profissional com experiência intermediária, capaz de desenvolver soluções com menor supervisão.',
                'level' => 'mid',
            ],
            [
                'name' => 'Desenvolvedor Sênior',
                'description' => 'Profissional experiente, capaz de liderar projetos e mentorear desenvolvedores mais jovens.',
                'level' => 'senior',
            ],
            [
                'name' => 'Gerente de Tecnologia',
                'description' => 'Responsável pela estratégia e gestão da equipe de tecnologia.',
                'level' => 'executive',
            ],
            [
                'name' => 'Analista de Suporte',
                'description' => 'Responsável pelo suporte técnico e resolução de problemas.',
                'level' => 'junior',
            ],
            [
                'name' => 'Analista de Infraestrutura',
                'description' => 'Gerencia a infraestrutura de TI e sistemas.',
                'level' => 'mid',
            ],
            [
                'name' => 'Diretor de TI',
                'description' => 'Responsável pela direção estratégica de tecnologia da informação.',
                'level' => 'executive',
            ],
            // Recursos Humanos e Departamento Pessoal
            [
                'name' => 'Analista de Recursos Humanos',
                'description' => 'Responsável por processos de recrutamento, seleção e desenvolvimento de pessoas.',
                'level' => 'mid',
            ],
            [
                'name' => 'Coordenador de Departamento Pessoal',
                'description' => 'Gerencia processos administrativos de pessoal, folha de pagamento e benefícios.',
                'level' => 'mid',
            ],
            [
                'name' => 'Especialista em Treinamento e Desenvolvimento',
                'description' => 'Desenvolve programas de capacitação e desenvolvimento profissional.',
                'level' => 'senior',
            ],
            [
                'name' => 'Diretor de Recursos Humanos',
                'description' => 'Responsável pela estratégia global de gestão de pessoas e cultura organizacional.',
                'level' => 'executive',
            ],
            // Cargos de Diretoria
            [
                'name' => 'Diretor Financeiro',
                'description' => 'Responsável pela gestão financeira e estratégias econômicas da empresa.',
                'level' => 'executive',
            ],
            [
                'name' => 'Diretor Comercial',
                'description' => 'Lidera a estratégia de vendas e expansão de negócios.',
                'level' => 'executive',
            ],
            [
                'name' => 'Diretor de Operações',
                'description' => 'Coordena e otimiza os processos operacionais da organização.',
                'level' => 'executive',
            ],
            // Cargos Administrativos
            [
                'name' => 'Assistente Administrativo',
                'description' => 'Auxilia nas atividades administrativas e suporte operacional.',
                'level' => 'junior',
            ],
            [
                'name' => 'Secretária Executiva',
                'description' => 'Responsável pelo suporte direto à diretoria e gestão de agenda.',
                'level' => 'mid',
            ]
        ];

        // Insert positions with timestamps
        foreach ($positions as $position) {
            DB::table('position_companies')->insertOrIgnore([
                'name' => $position['name'],
                'description' => $position['description'],
                'level' => $position['level'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};

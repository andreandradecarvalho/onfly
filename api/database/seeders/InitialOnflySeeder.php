<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InitialOnflySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar empresa
        DB::table('companies')->insertOrIgnore([
            'name' => 'Onfly',
            'document' => '30342266000183',
            'address' => 'Rua Maranhão, 339 – 2º andar, Santa Efigênia, Belo Horizonte – MG',
            'email' => 'onfly@onfly.com.br',
            'responsibility' => 'Marcos',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Recuperar o ID da empresa
        $onflyCompanyId = DB::table('companies')
            ->where('name', 'Onfly')
            ->value('id');

        // Criar usuário
        DB::table('users')->insertOrIgnore([
            'name' => 'Onfly',
            'email' => 'onfly@onfly.com.br',
            'password' => Hash::make('123456'),
            'isSuperAdmin' => true,
            'isAdmin' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Recuperar o ID do usuário
        $onflyUserId = DB::table('users')
            ->where('email', 'onfly@onfly.com.br')
            ->value('id');

        // Linkar usuário à empresa na tabela de pivô
        DB::table('company_user')->insertOrIgnore([
            'user_id' => $onflyUserId,
            'company_id' => $onflyCompanyId,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Recuperar o ID do cargo 'Diretor de TI'
        $diretorDeTiId = DB::table('position_companies')
            ->where('name', 'Diretor de TI')
            ->value('id');

        // Atualizar o registro na tabela company_user com o ID do cargo
        DB::table('company_user')
            ->where('user_id', $onflyUserId)
            ->where('company_id', $onflyCompanyId)
            ->update([
                'position_companies_id' => $diretorDeTiId,
            ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OnflyEmployeesSeeder extends Seeder
{
    /**
     * Remove special characters from a string.
     *
     * @param string $string
     * @return string
     */
    private function removeSpecialChars(string $string): string
    {
        // Mapping of special characters to their non-special equivalents
        $specialChars = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C'
        ];

        return strtr($string, $specialChars);
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Retorna todos os IDs de cargos
        $positionIds = DB::table('position_companies')->pluck('id')->toArray();

        // Retorna o ID da empresa Onfly
        $onflyCompanyId = DB::table('companies')->where('name', 'Onfly - Viagens corporativas')->value('id');

        if ($onflyCompanyId) {
                // Cria 5 funcionários
                $employees = [];
                for ($i = 0; $i < 5; $i++) {
                    $firstName = $this->removeSpecialChars($faker->firstName);
                    $lastName = $this->removeSpecialChars($faker->lastName);

                // Cria usuário
                $userId = DB::table('users')->insertGetId([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => strtolower($firstName . '.' . $lastName . '@onfly.com.br'),
                    'password' => bcrypt('123456'),
                    'email_verified_at' => now(),
                    'is_super_admin' => false,
                    'is_admin' => (($i == 1) ? true : false),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Seleciona aleatoriamente um cargo
                $positionId = $positionIds[array_rand($positionIds)];

                // Vincula usuário à empresa
                DB::table('company_user')->insert([
                    'user_id' => $userId,
                    'company_id' => $onflyCompanyId,
                    'position_companies_id' => $positionId,
                    'is_primary' => $i === 0, // Primeiro funcionário é primário
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}

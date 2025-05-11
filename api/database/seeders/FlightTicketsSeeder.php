<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FlightTicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // Retorna todo os IDS de usuarios
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Cidades brasileiras para origem e destino
        $cities = [
            'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Brasília',
            'Salvador', 'Curitiba', 'Fortaleza', 'Recife', 'Porto Alegre',
            'Manaus', 'Belém', 'Goiânia', 'Campinas', 'São Luís'
        ];

        // Statuses
        $statuses = ['solicitado', 'aprovado', 'cancelado'];

        // Cria 7 bilhetes de voos
        for ($i = 0; $i < 7; $i++) {
            // Seleciona aleatoriamente usuarios para o bilhete
            $userId = $userIds[array_rand($userIds)];
            $requesterId = $userIds[array_rand($userIds)];

            // Garante que o solicitante e o usuario sejam diferentes
            while ($userId === $requesterId) {
                $requesterId = $userIds[array_rand($userIds)];
            }

            // Gera um ID de pedido unico
            $orderId = 'FT-' . strtoupper(substr(uniqid(), -6));

            // Seleciona aleatoriamente origem e destino
            $origin = $cities[array_rand($cities)];
            $destination = $cities[array_rand($cities)];

            // Garante que a origem e o destino sejam diferentes
            while ($origin === $destination) {
                $destination = $cities[array_rand($cities)];
            }

            // Gera datas
            $departureDate = $faker->dateTimeBetween('+1 month', '+6 months');
            $returnDate = $faker->boolean(60) ? $faker->dateTimeBetween($departureDate, $departureDate->modify('+2 weeks')) : null;

            // Insere o bilhete de voo
            DB::table('flight_tickets')->insert([
                'order_id' => $orderId,
                'user_id' => $userId,
                'requester_id' => $requesterId,
                'origin' => $origin,
                'destination' => $destination,
                'departure_date' => $departureDate,
                'return_date' => $returnDate,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}

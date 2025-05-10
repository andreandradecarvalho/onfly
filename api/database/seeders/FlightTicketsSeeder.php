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
        
        // Get all user IDs
        $userIds = DB::table('users')->pluck('id')->toArray();
        
        // Brazilian cities for origin and destination
        $cities = [
            'São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Brasília', 
            'Salvador', 'Curitiba', 'Fortaleza', 'Recife', 'Porto Alegre', 
            'Manaus', 'Belém', 'Goiânia', 'Campinas', 'São Luís'
        ];

        // Statuses
        $statuses = ['solicitado', 'aprovado', 'cancelado'];

        // Create 7 flight tickets
        for ($i = 0; $i < 7; $i++) {
            // Randomly select users for ticket
            $userId = $userIds[array_rand($userIds)];
            $requesterId = $userIds[array_rand($userIds)];

            // Ensure requester and user are different
            while ($userId === $requesterId) {
                $requesterId = $userIds[array_rand($userIds)];
            }

            // Generate unique order ID
            $orderId = 'FT-' . strtoupper(substr(uniqid(), -6));

            // Randomly select origin and destination
            $origin = $cities[array_rand($cities)];
            $destination = $cities[array_rand($cities)];

            // Ensure origin and destination are different
            while ($origin === $destination) {
                $destination = $cities[array_rand($cities)];
            }

            // Generate dates
            $departureDate = $faker->dateTimeBetween('+1 month', '+6 months');
            $returnDate = $faker->boolean(60) ? $faker->dateTimeBetween($departureDate, $departureDate->modify('+2 weeks')) : null;

            // Insert flight ticket
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

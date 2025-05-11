<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyAndEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria empresas
        $companies = [
            [
                'name' => 'Global Systems',
                'document' => '45.678.901/0001-63',
                'address' => 'Alameda dos Sistemas, 234 - Curitiba, PR',
                'email' => 'contato@globalsystems.com.br',
                'responsibility' => 'Ana Beatriz Rocha'
            ],
            [
                'name' => 'Quantum Enterprises',
                'document' => '56.789.012/0001-54',
                'address' => 'Praça da Inovação, 567 - Porto Alegre, RS',
                'email' => 'contato@quantumenterpises.com.br',
                'responsibility' => 'Lucas Fernandes Souza'
            ]
        ];

        // Deleta empresas existentes com os mesmos documentos
        foreach ($companies as $companyData) {
            Company::where('document', $companyData['document'])->delete();
        }

        // Cria empresas
        $createdCompanies = [];
        foreach ($companies as $companyData) {
            $company = Company::create($companyData);
            $createdCompanies[] = $company;
        }

        // Deleta funcionários existentes com o mesmo padrão de email
        foreach ($createdCompanies as $company) {
            for ($i = 1; $i <= 5; $i++) {
                $email = Str::lower(Str::slug($company->name)) . ".employee{$i}@example.com";
                User::where('email', $email)->delete();
            }
        }

        // Cria funcionários para cada empresa
        foreach ($createdCompanies as $company) {
            for ($i = 1; $i <= 5; $i++) {
                $email = Str::lower(Str::slug($company->name)) . ".employee{$i}@example.com";
                $user = User::create([
                    'name' => "Employee {$i} at {$company->name}",
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'email_verified_at' => now(),
                    'is_super_admin' => false,
                    'is_admin' => false,
                    'created_at' => now(),
                ]);

                // Adiciona usuário a empresa
                $user->companies()->attach($company->id);
            }
        }

        // Deleta admin existentes com o mesmo padrão de email
        foreach ($createdCompanies as $company) {
            $adminEmail = Str::lower(Str::slug($company->name)) . ".admin@example.com";
            User::where('email', $adminEmail)->delete();
        }



        // Cria um admin para cada empresa
        foreach ($createdCompanies as $company) {
            $adminEmail = Str::lower(Str::slug($company->name)) . ".superadmin@example.com";
            $adminUser = User::create([
                'name' => "Super Admin of {$company->name}",
                'email' => $adminEmail,
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'is_super_admin' => true,
                'is_admin' => false,
                'created_at' => now(),
            ]);

            // Adiciona admin a empresa
            $adminUser->companies()->attach($company->id);
        }

        // Cria um admin para cada empresa
        foreach ($createdCompanies as $company) {
            $adminEmail = Str::lower(Str::slug($company->name)) . ".admin@example.com";
            $adminUser = User::create([
                'name' => "Admin of {$company->name}",
                'email' => $adminEmail,
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'is_super_admin' => false,
                'is_admin' => true,
                'created_at' => now(),
            ]);

            // Adiciona admin a empresa
            $adminUser->companies()->attach($company->id);
        }
    }
}

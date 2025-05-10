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
        // Define company data
        $companies = [
            [
                'name' => 'Tech Innovations',
                'document' => '23.456.789/0001-81',
                'address' => 'Av. Inovação, 456 - Rio de Janeiro, RJ',
                'email' => 'contato@techinnovations.com.br',
                'responsibility' => 'Maria Oliveira Costa'
            ],
            [
                'name' => 'Digital Solutions',
                'document' => '34.567.890/0001-72',
                'address' => 'Rua da Transformação, 789 - Belo Horizonte, MG',
                'email' => 'contato@digitalsolutions.com.br',
                'responsibility' => 'Pedro Henrique Almeida'
            ],
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

        // Delete existing companies with the same documents
        foreach ($companies as $companyData) {
            Company::where('document', $companyData['document'])->delete();
        }

        // Create companies
        $createdCompanies = [];
        foreach ($companies as $companyData) {
            $company = Company::create($companyData);
            $createdCompanies[] = $company;
        }

        // Delete existing users with the same email pattern
        foreach ($createdCompanies as $company) {
            for ($i = 1; $i <= 4; $i++) {
                $email = Str::lower(Str::slug($company->name)) . ".employee{$i}@example.com";
                User::where('email', $email)->delete();
            }
        }

        // Create employees for each company
        foreach ($createdCompanies as $company) {
            for ($i = 1; $i <= 4; $i++) {
                $email = Str::lower(Str::slug($company->name)) . ".employee{$i}@example.com";
                $user = User::create([
                    'name' => "Employee {$i} at {$company->name}",
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_super_admin' => false,
                    'is_admin' => false,
                ]);

                // Attach user to company
                $user->companies()->attach($company->id);
            }
        }

        // Delete existing admin users with the same email pattern
        foreach ($createdCompanies as $company) {
            $adminEmail = Str::lower(Str::slug($company->name)) . ".admin@example.com";
            User::where('email', $adminEmail)->delete();
        }

        // Create a company admin for each company
        foreach ($createdCompanies as $company) {
            $adminEmail = Str::lower(Str::slug($company->name)) . ".admin@example.com";
            $adminUser = User::create([
                'name' => "Admin of {$company->name}",
                'email' => $adminEmail,
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_super_admin' => false,
                'is_admin' => true,
            ]);

            // Attach admin user to company
            $adminUser->companies()->attach($company->id);
        }
    }
}

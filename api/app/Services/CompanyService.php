<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompanyService
{
    /**
     * Get companies based on user role
     *
     * @param User $user
     * @param bool $isSuperAdmin
     * @return Collection
     */
    public function getCompaniesByUserRole(User $user, bool $isSuperAdmin): Collection
    {

        if ($isSuperAdmin) {
            // If super admin, return all companies
            return Company::all();
        }

        // If not super admin, return only the user's companies
        return $user->companies;
    }

        /**
         * Create a new company
         *
         * @param array $data
         * @return Company
         * @throws ValidationException
         */
        public function createCompany(array $data): Company
        {
            return DB::transaction(function () use ($data) {
                // Check for existing companies with same unique identifiers
                $existingCompany = Company::where('document', $data['document'])->first();
                if ($existingCompany) {
                    throw ValidationException::withMessages([
                        'document' => ['Documento da empresa já cadastrado.']
                    ]);
                }

                return Company::create($data);
            });
        }

        public function deleteCompany(int $id): void
            {
                $company = Company::findOrFail($id);

                // Optional: Add authorization check
                $user = Auth::user();
                if (!$user->isSuperAdmin() && !$user->companies->contains($company)) {
                    throw new \Exception('Unauthorized to delete this company');
                }

                $company->delete();
            }

            public function updateCompany(int $id, array $data): Company
            {
                $company = Company::findOrFail($id);

                // Optional: Add authorization check
                $user = Auth::user();
                if (!$user->isSuperAdmin() && !$user->companies->contains($company)) {
                    throw new \Exception('Unauthorized to update this company');
                }

                $company->update($data);
                return $company;
            }

            public function getCompanyById(int $id): Company
            {
                return Company::findOrFail($id);
            }


}

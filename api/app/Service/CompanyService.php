<?php

namespace App\Service;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

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
}

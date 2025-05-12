<?php

namespace App\Repositories;

use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(mixed $data)
    {
        $user = $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            // Add other user fields if necessary
            'is_admin' => $data['is_admin'] ?? false, // Default to false if not provided
            'is_super_admin' => $data['is_super_admin'] ?? false, // Default to false if not provided
        ]);

        // Attach to company
        if (!empty($data['company_id'])) {
            $pivotData = [];
            if (!empty($data['position_company_id'])) {
                $pivotData['position_companies_id'] = $data['position_company_id'];
            }
            $user->companies()->attach($data['company_id'], $pivotData);
        }

        return $this->getUserWithCompany($user->id);
    }

    public function getUserWithCompany(int $userId)
    {
        return $this->user->where('users.id', $userId)
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->leftJoin('position_companies', function($join) {
                $join->on('company_user.position_companies_id', '=', 'position_companies.id');
            })
            ->select(
                'users.*',
                'companies.name as company_name',
                'companies.id as company_id',
                'position_companies.name as position_name',
                'position_companies.id as position_id'
            )
            ->first();
    }

    public function getAllUsers()
    {
        return $this->user->all();
    }

    public function getAllUsersWithCompany($isSuperAdminFilter = null, $isAdminFilter = null, $requestingUser = null,  $company_name =null)
    {
                $query = $this->user
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->leftJoin('position_companies', function($join) {
                $join->on('company_user.position_companies_id', '=', 'position_companies.id');
            });

        // Apply filters based on query parameters
        $isQueryingForGlobalSuperAdmins = false;
        if ($isSuperAdminFilter !== null) {
            $isSuperAdminFilterBool = filter_var($isSuperAdminFilter, FILTER_VALIDATE_BOOLEAN);
            $query->where('users.is_super_admin', $isSuperAdminFilterBool);
            if ($isSuperAdminFilterBool) {
                $isQueryingForGlobalSuperAdmins = true; // User is specifically asking for super_admins globally
            }
        }
        if ($isAdminFilter !== null) {
            $query->where('users.is_admin', filter_var($isAdminFilter, FILTER_VALIDATE_BOOLEAN));
        }

        if ($company_name !== null) {
            $query->where(function ($subQuery) use ($company_name) {
                $subQuery->whereRaw('companies.name ILIKE ?', ['%' . $company_name . '%'])
                         ->orWhereRaw('users.name ILIKE ?', ['%' . $company_name . '%']);
            });
        }

        // Company scoping for non-super-admins:
        // If a requestingUser is provided, and they are an admin but NOT a super_admin,
        // AND we are NOT specifically querying for super_admins globally (who are outside company scope for this purpose),
        // then scope the query to their company.
        if ($requestingUser && $requestingUser->is_admin && !$requestingUser->is_super_admin) {
            if (!$isQueryingForGlobalSuperAdmins) {
                 $query->where('company_user.company_id', $requestingUser->company_id);
            }
        }

        return $query->select(
                'users.*',
                'companies.name as company_name',
                'companies.id as company_id',
                'position_companies.name as position_name',
                'position_companies.id as position_id'
            )
            ->get();
    }

    public function getUsersByCompanyId(int $companyId)
    {
        return $this->user->where('company_id', $companyId)->get();
    }

    public function getUsersByCompanyIdWithCompany(int $companyId)
    {
        return $this->user
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->leftJoin('position_companies', function($join) {
                $join->on('company_user.position_companies_id', '=', 'position_companies.id');
            })
            ->where('companies.id', $companyId)
            ->select(
                'users.*',
                'companies.name as company_name',
                'companies.id as company_id',
                'position_companies.name as position_name',
                'position_companies.id as position_id'
            )
            ->get();
    }

    public function getUserById(int $userId)
    {
        return $this->user->find($userId);
    }

    public function delete(int $userId): bool
    {
        $user = $this->user->find($userId);
        if ($user) {
            // Desvincula o usuário da empresa
            $user->companies()->detach();
            return $user->delete();
        }
        return false;
    }

    public function update(int $userId, array $data)
    {
        $user = $this->user->find($userId);
        if (!$user) {
            return null;
        }

        // Atualiza os campos do usuário
        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        if (isset($data['is_admin'])) {
            $user->is_admin = $data['is_admin'];
        }
        if (isset($data['is_super_admin'])) {
            $user->is_super_admin = $data['is_super_admin'];
        }

        $user->save();

        if (isset($data['company_id'])) {
            $pivotData = [];
            if (isset($data['position_company_id'])) {
                $pivotData['position_companies_id'] = $data['position_company_id'];
            } else {
                $pivotData['position_companies_id'] = null;
            }
            $user->companies()->sync([$data['company_id'] => $pivotData]);
        } elseif (isset($data['position_company_id']) && $user->companies()->exists()) {
            $company = $user->companies()->first();
            if ($company) {
                $user->companies()->updateExistingPivot($company->id, ['position_companies_id' => $data['position_company_id']]);
            }
        }

        return $this->getUserWithCompany($user->id);
    }
}
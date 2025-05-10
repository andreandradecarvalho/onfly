<?php

namespace App\Repository;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create(Request $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return $user;
    }

    /**
     * Get authenticated user with company information
     *
     * @param int $userId
     * @return mixed
     */
    public function getUserWithCompany(int $userId)
    {
        return $this->user->where('users.id', $userId)
            ->join('company_user', 'users.id', '=', 'company_user.user_id')
            ->join('companies', 'company_user.company_id', '=', 'companies.id')
            ->leftJoin('position_companies', function($join) {
                $join->on('company_user.id', '=', 'position_companies.id');
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
}

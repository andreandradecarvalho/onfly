<?php

namespace App\Repositories;

use App\Models\FlightTicket;
use App\Repositories\Interfaces\FlightTicketRepositoryInterface;

class FlightTicketRepository implements FlightTicketRepositoryInterface
{
    /**
     * Retorna os tickets conforme o perfil do usuário:
     * - Super Admin: todos os tickets
     * - Admin: tickets das empresas do usuário
     * - Comum: tickets do próprio usuário
     */
    public function getAllByUser($user)
    {
        if ($user->is_super_admin) {
            return FlightTicket::with(['user.companies'])->get();
        }
        if ($user->is_admin) {
            // Busca os IDs das empresas do usuário
            $companyIds = $user->companies()->pluck('companies.id');
            // Busca IDs dos usuários das mesmas empresas
            $userIds = \App\Models\User::whereHas('companies', function($q) use ($companyIds) {
                $q->whereIn('companies.id', $companyIds);
            })->pluck('id');
            return FlightTicket::with(['user.companies'])->whereIn('user_id', $userIds)->get();
        }
        // Usuário comum: apenas seus próprios tickets
        return FlightTicket::with(['user.companies'])->where('user_id', $user->id)->get();
    }
    public function getById($id)
    {
        return FlightTicket::with(['user.companies'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return FlightTicket::create($data);
    }

    public function update($id, array $data)
    {
        $ticket = FlightTicket::findOrFail($id);

        $ticket->update($data);
        // Retorna o ticket atualizado com user e companies carregados
        return FlightTicket::with(['user.companies'])->findOrFail($id);
    }

    public function delete($id)
    {
        $ticket = FlightTicket::findOrFail($id);
        $ticket->delete();
        return true;
    }
}

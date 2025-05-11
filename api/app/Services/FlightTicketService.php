<?php

namespace App\Services;

use App\Repositories\Interfaces\FlightTicketRepositoryInterface;

class FlightTicketService
{
    protected $repository;

    public function __construct(FlightTicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll($user)
    {
        return $this->repository->getAllByUser($user);
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}

<?php

namespace App\Repositories\Interfaces;

interface FlightTicketRepositoryInterface
{
    public function getAllByUser($user);
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

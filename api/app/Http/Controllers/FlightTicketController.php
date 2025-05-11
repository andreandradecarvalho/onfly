<?php

namespace App\Http\Controllers;

use App\Services\FlightTicketService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class FlightTicketController extends Controller
{
    protected $service;

    public function __construct(FlightTicketService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = auth()->user();
        return response()->json($this->service->getAll($user));
    }

    public function show($id)
    {
        return response()->json($this->service->getById($id));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $data['order_id'] = 'FT-' . strtoupper(Str::random(6));
        $data['requester_id'] = auth()->user()->id;

        return response()->json($this->service->create($data), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        return response()->json($this->service->update($id, $data));
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

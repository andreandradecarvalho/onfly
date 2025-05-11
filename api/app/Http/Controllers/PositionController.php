<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PositionCompany;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positionCompanies = PositionCompany::all();
        return response()->json($positionCompanies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $positionCompany = PositionCompany::create($request->all());
        return response()->json($positionCompany, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $positionCompany = PositionCompany::find($id);
        return response()->json($positionCompany);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $positionCompany = PositionCompany::find($id);
        $positionCompany->update($request->all());
        return response()->json($positionCompany);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $positionCompany = PositionCompany::find($id);
        $positionCompany->delete();
        return response()->json(['message' => "Posição com id {$id} deletada"]);
    }
}

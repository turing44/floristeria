<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $reservas = Reserva::all();
        return response()->json([
            "numero" => $reservas->count(),
            "data" => $reservas,
            
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservaRequest $request)
    {
        $reserva = Reserva::create($request->validated());

        return response()->json($reserva, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        return $reserva;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReservaRequest $request, Reserva $reserva)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        return response()->json($reserva->delete(), 204);
    }


    public function obtenerEliminadas() {
        return Reserva::onlyTrashed()->get();
    }

    public function obtenerReservaEliminada(Request $id) {
        return Reserva::onlyTrashed()->find($id);
    }

}

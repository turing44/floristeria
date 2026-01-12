<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\UpdateEntregaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntregaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "num_entregas" => Entrega::count("id"), 
            "entregas" => Entrega::with('pedido')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntregaRequest $request)
    {
        $entrega = Entrega::create($request->validated());

        return response()->json($entrega, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Entrega $entrega)
    {
        return $entrega;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntregaRequest $request, Entrega $entrega)
    {
        $entrega->update($request->validated());

        return $entrega;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entrega $entrega)
    {

        $entrega->delete();

        return response()->json([
            "message" => "Entrega borrada"
        ], 204);
    }
    
    public function obtenerEliminadas()
    {
        //return Entrega::onlyTrashed()->get();
        return Entrega::all();
    }

    public function obtenerEntregaEliminada(Request $id)
    {
        return Entrega::onlyTrashed()->where("id", $id)->get();
    }
}

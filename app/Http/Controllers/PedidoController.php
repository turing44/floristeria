<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Http\Requests\UpdatePedidoRequest;
use App\Models\Pedido;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PedidoController extends Controller
{

    public function index() {   return response()->json(Pedido::all());    }


    public function store(StorePedidoRequest $request)
    {
        $pedido = Pedido::create(
            $request->validated()
        );

        return response()
                ->json($pedido, Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    public function show($id)
    {
        $pedido = Pedido::findOrFail($id);
        return response()->json($pedido, Response::HTTP_OK);
    }

    public function pedidoByCliente($cliente)
    {
        $pedidos = Pedido::where('cliente', $cliente)->get(); 

        return response()->json($pedidos, Response::HTTP_OK);
    }

    public function pedidoByDireccion($direccion)
    {
        $pedidos = Pedido::where('direccion', $direccion)->get(); 

        return response()->json($pedidos, Response::HTTP_OK);
    }

    public function pedidoByFechaEntrega($fecha)
    {
        $pedidos = Pedido::where('fecha_entrega', $fecha)->get(); 

        return response()->json($pedidos, Response::HTTP_OK);
    }


    public function update(UpdatePedidoRequest $request, Pedido $pedido)
    {
        //
    }

}

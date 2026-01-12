<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GuestToken;
use App\Models\Entrega;
use App\Models\Reserva;
// Importa tus Requests para reutilizar la validación que ya tienes hecha
use App\Http\Requests\StoreEntregaRequest;
use App\Http\Requests\StoreReservaRequest;

class GuestOrderController extends Controller
{
    // 1. El Florista llama a esta funcion para crear el enlace
    public function generateLink(Request $request) {
        $request->validate(['type' => 'required|in:entrega,reserva']);

        $token = GuestToken::create([
            'token' => Str::random(32), 
            'type' => $request->type,
            'expires_at' => now()->addHours(48) // Caduca en 48 horas desde el momento de creación
        ]);

        // Devuelve el link para copiar y pegar en WhatsApp
        return response()->json([
            'link' => url('/pedido-magic/' . $token->token)
        ]);
    }

    // 2. El Cliente ve el formulario (GET)
    public function showForm($token) {
        $guestToken = GuestToken::where('token', $token)->firstOrFail();

        if (!$guestToken->isValid()) {
            return abort(403, 'Este enlace ya ha sido usado o ha caducado.');
        }

        // Retornas una vista simple de Blade, o un JSON si tu front es separado
        // Si usáis Blade: return view('guest.form', ['token' => $token, 'type' => $guestToken->type]);
        return response()->json(['message' => 'Token válido', 'type' => $guestToken->type]); 
    }

    // 3. El Cliente envía el formulario (POST)
    // NOTA: Aquí usamos "Request" genérico primero para validar el token, luego validamos datos
    public function storeEntrega(StoreEntregaRequest $request, $token) {
        $guestToken = GuestToken::where('token', $token)->firstOrFail();

        if (!$guestToken->isValid()) {
            // Mensaje limpio para el usuario
            return response()->json(['message' => 'Este enlace ya ha caducado.'], 403);
        }

        try {
            // Intentamos crear la entrega
            $entrega = Entrega::create($request->validated());

            // picamos el ticket para que no se pueda reutilizar
            $guestToken->update(['is_used' => true]);

            return response()->json($entrega, 201);

        } catch (\Exception $e) {
           
            \Illuminate\Support\Facades\Log::error("Error creando pedido invitado: " . $e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema técnico al guardar el pedido. Por favor, inténtalo de nuevo o contacta por teléfono.'
            ], 500);
        }
    }
    
    // Lo mismo para reserva...
    public function storeReserva(StoreReservaRequest $request, $token) {
        $guestToken = GuestToken::where('token', $token)->firstOrFail();

        if (!$guestToken->isValid()) {
            return response()->json(['message' => 'Este enlace ya ha caducado o ha sido usado.'], 403);
        }

        try {
            // Crear la reserva usando los datos validados
            $reserva = Reserva::create($request->validated());

            // picar el ticket para que no se pueda reutilizar
            $guestToken->update(['is_used' => true]);

            return response()->json($reserva, 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error creando reserva invitada: " . $e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema técnico al guardar la reserva. Por favor, inténtalo de nuevo o llama a la tienda.'
            ], 500);
        }
    }
}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-pink-50 min-h-screen py-10 px-4">

    <div class="max-w-lg mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-pink-600">🌷 Finalizar Pedido</h2>
            <p class="text-sm text-gray-500">Sin registros. Rápido y fácil.</p>
        </div>

        <form id="pedidoForm" onsubmit="enviarPedido(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tu Nombre</label>
                <input type="text" name="cliente" required class="mt-1 block w-full border-gray-300 rounded-md border p-2 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tu Teléfono</label>
                <input type="text" name="telf_cliente" required class="mt-1 block w-full border-gray-300 rounded-md border p-2 shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">¿Qué quieres pedir?</label>
                <input type="text" name="producto" placeholder="Ej: Ramo de 12 rosas" required class="mt-1 block w-full border-gray-300 rounded-md border p-2 shadow-sm">
            </div>

            <input type="hidden" name="fuente" value="WhatsApp Link">
            <input type="hidden" name="direccion" value="Calle Demo 123"> <input type="hidden" name="codigo_postal" value="00000">
            <input type="hidden" name="destinatario" value="Mismo cliente">
            <input type="hidden" name="telf_destinatario" value="000000000">
            <input type="hidden" name="fecha_entrega" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="precio" value="50">
            <input type="hidden" name="horario" value="MAÑANA">
            <input type="hidden" name="estado" value="PENDIENTE">

            <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 rounded-lg shadow transition">
                Confirmar Pedido ✅
            </button>
        </form>

        <div id="exito" class="hidden text-center mt-6">
            <div class="text-5xl mb-2">🎉</div>
            <h3 class="text-xl font-bold text-green-600">¡Pedido Recibido!</h3>
            <p class="text-gray-600">El florista ya tiene tu pedido.</p>
            <p class="text-xs text-red-400 mt-4">Este enlace ha caducado por seguridad.</p>
        </div>
    </div>

    <script>
        const token = "{{ $token }}"; // El token viene de la ruta de Laravel

        async function enviarPedido(e) {
            e.preventDefault();
            const form = document.getElementById('pedidoForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                // Llamada a la API real usando el token
                const response = await fetch(`/api/pedido-magic/${token}/entrega`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    form.classList.add('hidden');
                    document.getElementById('exito').classList.remove('hidden');
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + JSON.stringify(errorData));
                }
            } catch (error) {
                console.error(error);
                alert('Hubo un error de conexión');
            }
        }
    </script>
</body>
</html>
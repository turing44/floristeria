<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Florista - Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h1 class="text-2xl font-bold text-green-700 mb-4">🌻 Floristería Antonia Bueno</h1>
        <p class="text-gray-600 mb-6">Generar enlace rápido para WhatsApp</p>

        <div class="space-y-4">
            <select id="tipo" class="w-full p-2 border rounded">
                <option value="entrega">Pedido a Domicilio</option>
                <option value="reserva">Recogida en Tienda</option>
            </select>

            <button onclick="generarLink()" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                Generar Enlace
            </button>
        </div>

        <div id="resultado" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded">
            <p class="text-sm text-gray-500 mb-2">Copia y pega esto en WhatsApp:</p>
            <textarea id="linkUrl" class="w-full p-2 text-sm bg-white border rounded" readonly></textarea>
            <a id="botonIr" href="#" target="_blank" class="block text-center text-blue-600 underline mt-2 text-sm">Probar enlace yo mismo</a>
        </div>
    </div>

    <script>
        async function generarLink() {
            const tipo = document.getElementById('tipo').value;
            
            try {
                // OJO: Ajusta la URL si tu ruta está en api.php o web.php
                // Si usas web.php recuerda el CSRF token, si es api.php no hace falta para la demo rápida
                const response = await fetch('/api/admin/generar-link', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: tipo })
                });

                const data = await response.json();

                if (data.link) {
                    // TRUCO DEL ALMENDRUCO:
                    // Como el link que genera el back apunta a la API, vamos a cambiarlo visualmente 
                    // para que apunte a nuestra VISTA de demostración.
                    // Supongamos que el token es lo último de la URL
                    const token = data.link.split('/').pop();
                    const viewLink = window.location.origin + '/pedido-magic-view/' + token;

                    document.getElementById('resultado').classList.remove('hidden');
                    document.getElementById('linkUrl').value = viewLink;
                    document.getElementById('botonIr').href = viewLink;
                }
            } catch (error) {
                alert('Error al generar el token. Revisa la consola (F12).');
                console.error(error);
            }
        }
    </script>
</body>
</html>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <title>floristeria-front</title>
    @vite(['resources/css/formulario.css', 'resources/js/app.js'])

  </head>
  <body>
    <div id="contenedorPrincipal">
        


    <form id="pedidoForm">
        <h1>Registrar Pedido</h1>

        <input type="text" name="producto" placeholder="Producto" required>
        <input type="text" name="destinatario" placeholder="Destinatario" required>
        <input type="text" name="destinatario_telf" placeholder="Teléfono destinatario" required>
        <input type="text" name="cliente" placeholder="Cliente" required>
        <input type="text" name="cliente_telf" placeholder="Teléfono cliente" required>
        <input type="date" name="fecha_entrega" required>
        <select name="horario">
            <option value="">Horario</option>
            <option value="Mañana">Mañana</option>
            <option value="Tarde">Tarde</option>
        </select>
        <input type="text" name="observaciones" placeholder="Observaciones">
        <input type="text" name="mensaje" placeholder="Mensaje de la tarjeta">
        <button type="submit">Enviar Pedido</button>
    </form>
    </div>
  </body>
</html>
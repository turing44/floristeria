from __future__ import annotations

from pathlib import Path


HTML = """<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Documentacion del backend de floristeria</title>
  <style>
    @page {
      size: A4;
      margin: 2.2cm 2cm;
    }

    body {
      font-family: "Liberation Serif", Georgia, serif;
      color: #1f1f1f;
      line-height: 1.45;
      font-size: 11.5pt;
    }

    h1, h2, h3 {
      font-family: "Liberation Sans", Arial, sans-serif;
      color: #7a2e1f;
      margin-top: 0;
    }

    h1 {
      font-size: 22pt;
      margin-bottom: 0.35cm;
    }

    h2 {
      font-size: 15pt;
      margin-top: 0.8cm;
      margin-bottom: 0.25cm;
      border-bottom: 1px solid #d5c3b7;
      padding-bottom: 0.08cm;
    }

    h3 {
      font-size: 12pt;
      margin-top: 0.45cm;
      margin-bottom: 0.1cm;
    }

    p {
      margin: 0 0 0.28cm 0;
      text-align: justify;
    }

    ul {
      margin: 0.1cm 0 0.35cm 0.65cm;
    }

    li {
      margin-bottom: 0.08cm;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0.2cm 0 0.45cm 0;
      font-size: 10.2pt;
    }

    th, td {
      border: 1px solid #b8aca2;
      padding: 0.16cm 0.18cm;
      vertical-align: top;
    }

    th {
      background: #f2e7df;
      text-align: left;
      font-family: "Liberation Sans", Arial, sans-serif;
    }

    .hero {
      border: 1.5px solid #c9b3a6;
      padding: 0.9cm 1cm;
      background: #fcf8f5;
      margin-bottom: 0.8cm;
    }

    .subtitle {
      font-size: 12.5pt;
      color: #4a4a4a;
      margin-bottom: 0.35cm;
    }

    .meta {
      margin-top: 0.45cm;
      font-size: 10.5pt;
    }

    .small {
      font-size: 10pt;
      color: #555;
    }

    .page-break {
      page-break-before: always;
    }

    .mono {
      font-family: "Liberation Mono", monospace;
      font-size: 9.8pt;
    }
  </style>
</head>
<body>
  <div class="hero">
    <h1>Documentacion tecnica breve del backend de floristeria</h1>
    <p class="subtitle">Memoria descriptiva resumida del sistema backend desarrollado con Laravel</p>
    <p>Este documento sintetiza la arquitectura, el modelo de datos, la API REST y las consideraciones de despliegue del backend del proyecto <b>floristeria</b>. El texto se ha elaborado a partir del codigo fuente actual del repositorio y se orienta a un contexto academico de TFG.</p>
    <div class="meta">
      <p><b>Tecnologias principales:</b> Laravel 12, PHP 8.2, SQLite, Node.js y Playwright</p>
      <p><b>Alcance:</b> Backend de pedidos, entregas, reservas, contratos de formulario y generacion de PDF</p>
    </div>
  </div>

  <h2>1. Introduccion</h2>
  <p>El backend de <i>floristeria</i> se ha disenado como una API orientada a la gestion de pedidos de una floristeria. El sistema diferencia dos tipos de servicio: las entregas a domicilio y las reservas para recogida en tienda. Sobre esta base, el backend ofrece operaciones de consulta, alta, modificacion, archivado, restauracion y generacion de documentos PDF.</p>
  <p>Desde el punto de vista tecnologico, la aplicacion se apoya en <b>Laravel 12</b> como framework principal y en <b>SQLite</b> como motor de persistencia. Para la produccion de documentos PDF se emplea un flujo complementario con <b>Node.js</b> y <b>Playwright</b>, que permite renderizar plantillas HTML del servidor y devolverlas como archivos imprimibles.</p>

  <h2>2. Arquitectura del backend</h2>
  <p>La arquitectura sigue una separacion por capas relativamente clara. En la capa de entrada se situan los controladores HTTP; a continuacion aparecen los objetos <i>Request</i>, que centralizan la validacion; despues se encuentran los servicios de dominio; y finalmente los modelos Eloquent y los recursos JSON se encargan de la persistencia y la serializacion de la respuesta.</p>

  <table>
    <tr>
      <th>Capa</th>
      <th>Responsabilidad principal</th>
      <th>Elementos destacados</th>
    </tr>
    <tr>
      <td>Controladores</td>
      <td>Recibir la peticion HTTP, delegar la logica y construir la respuesta</td>
      <td>EntregaController, ReservaController, ContratoPedidoController, MensajeController, GoogleImportController</td>
    </tr>
    <tr>
      <td>Requests</td>
      <td>Aplicar validacion y normalizacion de datos antes de llegar a la capa de servicio</td>
      <td>BasePedidoRequest, StoreEntregaRequest, UpdateEntregaRequest, StoreReservaRequest, UpdateReservaRequest</td>
    </tr>
    <tr>
      <td>Servicios</td>
      <td>Encapsular reglas de negocio, transacciones, listados y generacion de PDF</td>
      <td>ServicioPedidos, ServicioEntregas, ServicioReservas, ServicioListadoPedidos, ServicioPdfPedidos</td>
    </tr>
    <tr>
      <td>Contratos</td>
      <td>Definir campos, reglas, mensajes y estructura del formulario que consume el frontend</td>
      <td>ServicioContratoPedidos y sus definiciones de campos</td>
    </tr>
    <tr>
      <td>Persistencia y recursos</td>
      <td>Representar entidades y serializar datos de salida</td>
      <td>Pedido, Entrega, Reserva, GuestToken, EntregaResource y ReservaResource</td>
    </tr>
  </table>

  <h3>2.1 Papel de los controladores</h3>
  <p>Los controladores de entregas y reservas ofrecen un CRUD clasico, ademas de endpoints para recuperar registros archivados, restaurarlos y obtener sus PDF. Por otra parte, <b>ContratoPedidoController</b> expone el contrato de formulario que utiliza el frontend para construir dinamicamente las pantallas de alta y edicion. Esta decision reduce duplicidad entre backend y frontend, ya que la definicion de campos vive principalmente en el servidor.</p>

  <h3>2.2 Capa de servicios</h3>
  <p>La logica central de creacion y actualizacion se apoya en <b>ServicioPedidos</b>, que ejecuta transacciones de base de datos y separa los datos comunes del pedido base frente a los datos especificos de entrega o reserva. Encima de este servicio comun aparecen <b>ServicioEntregas</b> y <b>ServicioReservas</b>, que especializan la operacion segun el tipo de pedido y disparan la regeneracion del PDF asociado.</p>
  <p>El servicio <b>ServicioListadoPedidos</b> agrupa la logica de busqueda, filtros, ordenacion y calculo de resumenes. De este modo se evita duplicar el comportamiento de listado entre entregas y reservas y se consigue una API coherente para ambos tipos de entidad.</p>

  <h2>3. Modelo de datos y dominio funcional</h2>
  <p>El dominio gira en torno a la entidad <b>Pedido</b>, que almacena la informacion comun de cualquier encargo: cliente, producto, precio, fecha, horario y observaciones. A partir de ese pedido base se asocia una y solo una entidad de tipo <b>Entrega</b> o <b>Reserva</b>. El sistema utiliza borrado logico, lo que permite archivar registros sin perderlos definitivamente.</p>

  <table>
    <tr>
      <th>Entidad</th>
      <th>Funcion en el dominio</th>
      <th>Campos relevantes</th>
    </tr>
    <tr>
      <td>Pedido</td>
      <td>Nucleo comun del encargo</td>
      <td>tipo_pedido, fuente, nombre_cliente, telefono_cliente, producto, precio, fecha, horario, observaciones</td>
    </tr>
    <tr>
      <td>Entrega</td>
      <td>Datos especificos de un envio a domicilio</td>
      <td>direccion, codigo_postal, telefono_destinatario</td>
    </tr>
    <tr>
      <td>Reserva</td>
      <td>Datos especificos de una recogida en tienda</td>
      <td>hora_recogida, dinero_pendiente</td>
    </tr>
    <tr>
      <td>GuestToken</td>
      <td>Token auxiliar para integracion externa e importacion opcional</td>
      <td>token, tipo, fecha_exp, is_used</td>
    </tr>
  </table>

  <p>En cuanto a relaciones, <b>Pedido</b> puede pertenecer opcionalmente a un usuario y a un token invitado, y mantiene una relacion uno a uno con <b>Entrega</b> o con <b>Reserva</b>. En el sentido inverso, tanto <b>Entrega</b> como <b>Reserva</b> pertenecen a un unico pedido. Esta estructura facilita tratar el pedido como objeto principal y especializar solo aquello que cambia entre modalidades de servicio.</p>

  <h3>3.1 Importacion opcional desde Google</h3>
  <p>El backend incorpora un modulo opcional de integracion con Google, activable mediante configuracion. Su finalidad es generar enlaces con token e importar pedidos desde una hoja de calculo publicada como CSV. Aunque esta funcionalidad no forma parte del flujo CRUD principal, si amplía el alcance del sistema al permitir la entrada de pedidos generados fuera de la interfaz local.</p>

  <h2>4. API REST y flujo de negocio</h2>
  <p>La API se organiza en tres bloques. El primero gestiona los <b>contratos de formulario</b>, necesarios para que el frontend conozca los campos, reglas y destino de envio. El segundo bloque agrupa las operaciones sobre <b>entregas</b>. El tercero hace lo mismo para <b>reservas</b>. Adicionalmente existe un endpoint para generar el PDF de un mensaje suelto y, de forma condicional, dos rutas de integracion con Google.</p>

  <table>
    <tr>
      <th>Grupo</th>
      <th>Rutas principales</th>
      <th>Proposito</th>
    </tr>
    <tr>
      <td>Contratos</td>
      <td class="mono">GET /api/contratos/entregas<br>GET /api/contratos/reservas</td>
      <td>Entregar al frontend la definicion dinamica del formulario para crear o actualizar</td>
    </tr>
    <tr>
      <td>Entregas</td>
      <td class="mono">GET, POST, PUT, DELETE /api/entregas<br>GET /api/entregas/pdf/{id}<br>POST /api/entregas/restaurar/{id}</td>
      <td>Gestion completa de pedidos a domicilio</td>
    </tr>
    <tr>
      <td>Reservas</td>
      <td class="mono">GET, POST, PUT, DELETE /api/reservas<br>GET /api/reservas/pdf/{id}<br>POST /api/reservas/restaurar/{id}</td>
      <td>Gestion completa de recogidas en tienda</td>
    </tr>
    <tr>
      <td>Mensajes PDF</td>
      <td class="mono">POST /api/mensaje/pdf</td>
      <td>Generar una tarjeta PDF a partir de un mensaje y un destinatario</td>
    </tr>
  </table>

  <h3>4.1 Validacion y normalizacion</h3>
  <p>La validacion de altas y modificaciones no se define directamente en cada controlador, sino a traves de <b>BasePedidoRequest</b>. Este request abstracto delega en <b>ServicioContratoPedidos</b> la obtencion de reglas, mensajes, normalizacion de entrada y validaciones adicionales. Como consecuencia, las reglas del formulario y las reglas del backend comparten una misma fuente de verdad.</p>
  <p>Este mecanismo presenta dos ventajas. En primer lugar, reduce divergencias entre interfaz y servidor. En segundo lugar, permite construir formularios dinamicos, ya que el propio backend expone las secciones, los campos ocultos, el metodo HTTP y la ruta de envio correspondiente. El frontend solo necesita interpretar esta estructura y renderizarla.</p>

  <h3>4.2 Flujo de creacion y actualizacion</h3>
  <p>Cuando el cliente envia una peticion de alta o de edicion, el request correspondiente valida los datos y los entrega al servicio especializado. Despues, <b>ServicioPedidos</b> separa la informacion comun del pedido y la informacion especifica de la modalidad de servicio. La operacion completa se ejecuta dentro de una transaccion, de forma que los cambios en el pedido y en la entidad hija queden sincronizados.</p>
  <p>Una vez persistido el registro, la respuesta JSON no se construye manualmente campo por campo, sino mediante <b>EntregaResource</b> o <b>ReservaResource</b>. Ambos recursos vuelven a apoyarse en <b>ServicioContratoPedidos</b> para serializar el modelo de salida con una estructura coherente con la definicion del contrato.</p>

  <h3>4.3 Listados, archivado y restauracion</h3>
  <p>Los listados permiten filtrar por fecha, texto de busqueda y criterios especificos como horario o pedidos pendientes de pago. Ademas del conjunto de registros, la API devuelve metadatos de resumen, por ejemplo el numero de archivadas o la cantidad correspondiente al dia actual. El archivado utiliza borrado logico y existe una operacion posterior de restauracion para recuperar tanto la entidad hija como el pedido asociado.</p>

  <h2>5. Generacion de documentos PDF</h2>
  <p>La produccion de PDF se resuelve mediante <b>ServicioPdfPedidos</b>. Este servicio renderiza una plantilla Blade HTML y delega la conversion a PDF en <b>EjecutorPlaywrightPdf</b>, que ejecuta un script de Node.js. Los archivos se almacenan en disco usando el almacenamiento configurado por Laravel y se reutilizan cuando ya existe una copia previa.</p>
  <p>La misma idea se aplica tanto a los albaranes de entrega como a las reservas y al PDF de mensajes. En el flujo de entregas y reservas, el sistema intenta regenerar el PDF tras crear o actualizar un registro. Si el proceso falla, el error se registra en log, pero no se invalida necesariamente la operacion principal de negocio.</p>

  <h2>6. Despliegue y dependencias operativas</h2>
  <p>Para funcionar correctamente, el backend requiere <b>PHP 8.2</b>, las extensiones habituales de Laravel y soporte para <b>SQLite</b>. La instalacion descrita en el proyecto incorpora tambien <b>Node.js</b>, <b>npm</b>, <b>Playwright</b> y el navegador Chromium, necesarios para la generacion de PDF. La configuracion propuesta de despliegue contempla un servidor Nginx que reenvia la ruta <span class="mono">/api/*</span> hacia Laravel.</p>
  <p>Desde el punto de vista operativo, resulta especialmente importante asegurar permisos de escritura sobre <span class="mono">database.sqlite</span>, su directorio contenedor, <span class="mono">storage/</span> y <span class="mono">bootstrap/cache/</span>. Dado que la aplicacion emplea SQLite y cache de Laravel, un error de permisos puede provocar que las peticiones GET funcionen mientras que las operaciones POST o PUT fallen al intentar escribir.</p>
  <ul>
    <li>Framework principal: Laravel 12</li>
    <li>Lenguaje: PHP 8.2</li>
    <li>Base de datos: SQLite</li>
    <li>Motor de PDF: Node.js + Playwright + Chromium</li>
    <li>Servidor recomendado: Nginx + PHP-FPM</li>
  </ul>

  <h2>7. Conclusiones</h2>
  <p>El backend de <i>floristeria</i> presenta una arquitectura compacta pero bien organizada para un proyecto de TFG. La decision de centralizar en el servidor tanto la validacion como la definicion de formularios aporta coherencia al sistema y reduce errores de sincronizacion con el frontend. Asimismo, la separacion entre <b>Pedido</b> y las modalidades <b>Entrega</b> y <b>Reserva</b> facilita la extensibilidad del dominio.</p>
  <p>En conjunto, se trata de un backend orientado a un caso de uso concreto, con una API clara, soporte de archivado logico, generacion documental y una linea de integracion externa opcional. Todo ello lo convierte en una base adecuada para un sistema de gestion de pedidos con alcance academico y potencial de evolucion futura.</p>

  <p class="small">Documento generado a partir del codigo del backend disponible en el repositorio local del proyecto.</p>
</body>
</html>
"""


def main() -> None:
    repo_root = Path(__file__).resolve().parents[2]
    output_dir = repo_root / "tmp" / "docs"
    output_dir.mkdir(parents=True, exist_ok=True)
    html_path = output_dir / "documentacion_backend_floristeria.html"
    html_path.write_text(HTML, encoding="utf-8")
    print(html_path)


if __name__ == "__main__":
    main()

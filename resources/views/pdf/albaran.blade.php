<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
    <base href="{{ url('/') }}"> 
    
    <style>
        @font-face {
        font-family: "Lucida Calligraphy";
        src: url("{{ asset('fonts/LucidaUnicodeCalligraphyBold.ttf') }}") format("truetype"),
        url("{{ asset('fonts/LucidaUnicodeCalligraphy.ttf') }}") format("truetype");
        font-weight: normal;
        font-style: normal;
        }


        .body{
            background-color: red;
        }
        .segmento{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start, stretch;
            width: 100%;
            gap: 40px;
            /*margin-top: 10px;*/
            height: 99mm; /* cada uno ocupa un tercio de la hoja visible */
            box-sizing: border-box;
            page-break-inside: avoid; /* evita que se corten entre páginas */
        }
        .segmentoMensaje{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 40px;
            height: 99mm; /* cada uno ocupa un tercio de la hoja visible */
            box-sizing: border-box;
            page-break-inside: avoid; /* evita que se corten entre páginas */
        }
        .bloqueContacto{
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex: 1;
        }
        .bloqueContacto p {
        margin: 4px 0; /* Reduce espacio vertical entre etiquetas */
        padding-top: 5px;
        }
        .idPedido{
            display: flex;
            flex-direction: column;
            justify-content: left;
            align-items: flex-start;
            justify-content: flex-start;
            flex:1;
            /*padding-left: 40px;*/
        }
        .bloqueFecha {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        flex: 1;
        }
        .infoPedido{
            display: flex;
            flex-direction: column;
        }
        .direccion{
            display: flex;
            flex-direction: row;
        }
        .producto{
            display: flex;
            flex-direction: row;
        }
        .fuente{
            display: flex;
            flex-direction: row;
        }
        .textarea{
            width: 370px;
            height: 70px;
            border: 1px solid #ccc;
            padding: 10px;
            resize: none;
        }
        .fecha{
            display: flex;
        }
        .fechaGigante{
            display: flex;
            justify-content: flex-start;
            color: red;
            font-size: 75px;
        }
        .fechaGigante p{
            margin: 7px 0;
        }
        .horario{
            display: flex;
            justify-content: flex-start;
            color: red;
            font-size: 30px;
        }
        .horario p{
            margin: 7px 0;
        }
        .checkbox input[type="checkbox"]{
            margin: 36px 0;
            transform: scale(6);
        }
        .confirmacionEntrega{
        margin: 0%;
        font-size: 50px;
        }
        .divVacio {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 0;
        }

        .mensajeDeLado{
            display: flex;
            height: 250px; 
            align-items: center;
            justify-content: center;
            text-orientation: mixed;
            writing-mode: vertical-rl;   
            overflow-wrap: break-word;
            word-break: break-word;      
            white-space: normal;         
            overflow: hidden;            
            transform: rotate(180deg);
            page-break-inside: avoid;
            transform-origin: center; 
            text-align: center;
            font-family: "Lucida Calligraphy";
            flex: 1;
            min-width: 0;
        }
        .nombreDestinatario{
            display: flex;
            height: 200px;
            justify-content: center;
            align-items: center;
            writing-mode: vertical-rl;   
            text-orientation: mixed;
            overflow-wrap: break-word;
            transform: rotate(180deg);
            page-break-inside: avoid;
            transform-origin: center;
            font-size: 30px; 
            text-align: center;
            flex: 1;
            min-width: 0;
        }
        #colorAzul{
        color: blue;
        }
        .lineasFijas {
            position: fixed;        
            left: 0;
            bottom: 25px;
            width: 100%;
            height: 80mm;
            pointer-events: none;
            z-index: 1000;
        }

        .lineasFijas::before,
        .lineasFijas::after {
            content: "";
            position: absolute;
            top: 10mm;            
            bottom: 10mm;          
            width: 0.3px;
            background: #000;
        }

        .lineasFijas::before {
            left: 69mm;            
        }

        .lineasFijas::after {
            right: 69mm;          
        }
        @media print {
        @page {
            size: A4;
            /*margin-left: 0;
            margin-right: 0;*/
            margin: 0;
        }

        html,body {
            margin: 0;
            padding: 0;
            height: 297mm;
            box-sizing: border-box;

        }
        .segmento {
            padding: 5mm;
            page-break-inside: avoid; /* Evita que se corte entre páginas */
        }
        }

     </style>
</head>

<body>
    @php
        use Carbon\Carbon;
        // 🛠️ PUENTE DE SEGURIDAD (MERRICK FIX):
        // El controlador nos manda 'entrega', pero esta vista antigua usa 'pedido'.
        // Definimos 'pedido' aquí sacándolo de la entrega para que el resto del HTML funcione igual.
        if(isset($entrega)) {
            $pedido = $entrega->pedido;
            // Aseguramos que la relación inversa esté cargada para evitar bucles raros
            $pedido->setRelation('entrega', $entrega); 
        }
    @endphp

    <div class="lineasFijas"></div>

    <div>
        <div class="segmento">
            
            <div class="bloqueContacto">
                <p>Destinatario:</p>
                <p id="colorAzul">{{ $pedido->entrega->destinatario_nombre ?? 'Recogida Tienda' }}</p>
                
                <p>Teléfono Destinatario:</p>
                <p id="colorAzul">{{ $pedido->entrega->destinatario_telf ?? '-' }}</p>
                
                <p>Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_nombre }}</p>
                
                <p>Teléfono Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>
            </div>
            
            <div class="idPedido">
                <div class="infoPedido">
                    <div class="direccion">
                        <p>Dirección: </p>
                        <p id="colorAzul">{{ $pedido->entrega->direccion ?? 'Recogida Local' }} {{ $pedido->entrega->codigo_postal ?? '' }}</p>
                    </div>
                    
                    <div class="producto">
                        <p>Producto: </p>
                        <p id="colorAzul">{{ $pedido->descripcion }}</p>
                    </div>
                    
                    <div class="fuente">
                        <p>Fuente: </p>
                        <p id="colorAzul">{{ $pedido->entrega->fuente ?? 'Tienda' }}</p>
                    </div>
                </div>
                
                <div class="observaciones">
                    <p>Observaciones: </p>
                    <textarea id="colorAzul" readonly class="textarea">{{ $pedido->observaciones }}</textarea>
                </div>
            </div>
            
            <div class="bloqueFecha">
                <div id="colorAzul">
                    <p>{{ $pedido->entrega->fecha_entrega ? Carbon::parse($pedido->entrega->fecha_entrega)->format('d/m/Y') : '--/--' }}</p>
                </div>
                <div class="fechaGigante">
                    <p>{{ $pedido->entrega->fecha_entrega ? Carbon::parse($pedido->entrega->fecha_entrega)->format('d') : '?' }}</p>
                </div>
                <div class="horario">
                    {{-- OJO: El horario ahora está en la entrega, no en el pedido --}}
                    <p>{{$pedido->entrega->horario ?? $pedido->horario}}</p>
                </div>
                <div id="colorAzul" style="text-align: center;">
                    <p>Entregado</p>
                    <p class="confirmacionEntrega">SÍ / NO</p>
                </div>
            </div>
        </div>

        <div class="segmento">
            
            <div class="bloqueContacto">
                <p>Destinatario:</p>
                <p id="colorAzul">{{ $pedido->entrega->destinatario_nombre ?? 'Recogida Tienda' }}</p>
                
                <p>Teléfono Destinatario:</p>
                <p id="colorAzul">{{ $pedido->entrega->destinatario_telf ?? '-' }}</p>
                
                <p>Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_nombre }}</p>
                
                <p>Teléfono Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>
            </div>
            
            <div class="idPedido">
                <div class="infoPedido">
                    <div class="direccion">
                        <p>Dirección: </p>
                        <p id="colorAzul">{{ $pedido->entrega->direccion ?? 'Recogida Local' }} {{ $pedido->entrega->codigo_postal ?? '' }}</p>
                    </div>
                    
                    <div class="producto">
                        <p>Producto: </p>
                        <p id="colorAzul">{{ $pedido->descripcion }}</p>
                    </div>
                    
                    <div class="fuente">
                        <p>Fuente: </p>
                        <p id="colorAzul">{{ $pedido->entrega->fuente ?? 'Tienda' }}</p>
                    </div>
                </div>
                
                <div class="observaciones">
                    <p>Observaciones: </p>
                    <textarea id="colorAzul" readonly class="textarea">{{ $pedido->observaciones }}</textarea>
                </div>
            </div>
            
            <div class="bloqueFecha">
                <div id="colorAzul">
                    <p>{{ $pedido->entrega->fecha_entrega ? Carbon::parse($pedido->entrega->fecha_entrega)->format('d/m/Y') : '--/--' }}</p>
                </div>
                <div class="fechaGigante">
                    <p>{{ $pedido->entrega->fecha_entrega ? Carbon::parse($pedido->entrega->fecha_entrega)->format('d') : '?' }}</p>
                </div>
                <div class="horario">
                   <p>{{$pedido->entrega->horario ?? $pedido->horario}}</p>
                </div>
                <div class="checkbox">
                        <label>
                            <input type="checkbox">
                        </label>
                </div>
            </div>
        </div>

        <div class="segmentoMensaje">
            <div class="mensajeDeLado">
                <p>
                    @if($pedido->entrega && $pedido->entrega->mensaje_dedicatoria)
                        "{{ $pedido->entrega->mensaje_dedicatoria }}"
                    @elseif($pedido->reserva && $pedido->reserva->texto_mensaje)
                        "{{ $pedido->reserva->texto_mensaje }}"
                    @else
                        - Sin Mensaje -
                    @endif
                </p>
            </div>
            <div class="divVacio"></div>
            <div class="nombreDestinatario">
               <p>{{ $pedido->entrega->destinatario_nombre ?? $pedido->cliente_nombre }}</p> 
            </div>
        </div>
    </div>
</body>
</html>
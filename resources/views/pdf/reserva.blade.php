@php
    $path = public_path('fonts/LucidaUnicodeCalligraphy.ttf');
    
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64Font = 'data:font/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64Font = ''; 
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: "Lucida Calligraphy";
            src: url("{!! $base64Font !!}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        .body{ background-color: red; }
        .segmento{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start, stretch;
            width: 100%;
            gap: 40px;
            height: 99mm; 
            box-sizing: border-box;
            page-break-inside: avoid; 
        }
        .segmentoMensaje{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 40px;
            height: 99mm; 
            box-sizing: border-box;
            page-break-inside: avoid; 
        }
        .bloqueContacto{
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex: 1;
            overflow-wrap: anywhere;
            word-break: break-word;
            min-width: 0;
        }
        .bloqueContacto p {
            margin: 4px 0; 
            padding-top: 5px;
        }
        .idPedido{
            display: flex;
            flex-direction: column;
            justify-content: left;
            align-items: flex-start;
            justify-content: flex-start;
            flex:1;
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
        .direccion, .producto, .fuente{
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
        .fecha{ display: flex; }
        .fechaGigante{
            display: flex;
            justify-content: flex-start;
            color: red;
            font-size: 75px;
        }
        .fechaGigante p{ margin: 7px 0; }
        .horario{
            display: flex;
            justify-content: flex-start;
            color: red;
            font-size: 30px;
        }
        .horario p{ margin: 7px 0; }
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
            overflow-wrap: anywhere;
            word-break: break-word;
            min-width: 0;
            transform: rotate(180deg);
            page-break-inside: avoid;
            transform-origin: center;
            font-size: 30px; 
            text-align: center;
            flex: 1;
            min-width: 0;
            font-family: "Lucida Calligraphy";
        }
        #colorAzul{ color: blue; }
        .lineasFijas {
            position: fixed;        
            left: 0;
            bottom: 25px;
            width: 100%;
            height: 80mm;
            pointer-events: none;
            z-index: 1000;
        }
        .lineasFijas::before, .lineasFijas::after {
            content: "";
            position: absolute;
            top: 10mm;            
            bottom: 10mm;          
            width: 0.3px;
            background: #000;
        }
        .lineasFijas::before { left: 69mm; }
        .lineasFijas::after { right: 69mm; }
        .identificador{
            position: fixed;
            top: 1.5mm;
            right: 3mm;
            font-size:40px; 
        }
        @media print {
            @page {
                size: A4;
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
                page-break-inside: avoid; 
            }
        }
     </style>
</head>

<body>
    
    @php
        use Carbon\Carbon;
        // Obtenemos el pedido desde la reserva
        if(isset($reserva)) {
            $pedido = $reserva->pedido;
            // Aseguramos relación inversa
            $pedido->setRelation('reserva', $reserva); 
        }
    @endphp

    <div class="lineasFijas"></div>
    <div class="identificador">{{$pedido->reserva->id}}</div>
    <div>

        <div class="segmento">
            
            <div class="bloqueContacto">
                <p>Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_nombre }}</p>
                
                <p>Teléfono Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>

                <p>Precio Total:</p>
                <p id="colorAzul">{{ $pedido->precio ?? 'Recogida Tienda' }}</p>
                
                <p>Dinero a Contado:</p>
                <p id="colorAzul">{{ $pedido->reserva->dinero_a_cuenta ?? '-' }}</p>
                
            </div>
            
            <div class="idPedido">
                <div class="infoPedido">
                    <div class="producto">
                        <p>Producto: </p>
                        <p id="colorAzul">{{ $pedido->producto }}</p>
                    </div>
                </div>
                <div class="observaciones">
                    <p>Observaciones: </p>
                    <textarea id="colorAzul" readonly class="textarea">{{ $pedido->observaciones }}</textarea>
                </div>
            </div>
            
            <div class="bloqueFecha">
                <div id="colorAzul">
                    <p>{{ $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d/m/Y') : ' ' }}</p>
                </div>
                <div class="fechaGigante">
                    <p>{{ $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d') : ' ' }}</p>
                </div>
                <div class="horario">
                    <p>{{ $pedido->reserva->estado_pago }}</p>
                </div>
            </div>
        </div>

        <div class="segmento">
            
            <div class="bloqueContacto">
                <p>Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_nombre }}</p>
                
                <p>Teléfono Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>

                <p>Precio Total:</p>
                <p id="colorAzul">{{ $pedido->precio ?? 'Recogida Tienda' }}</p>
                
                <p>Dinero a Contado:</p>
                <p id="colorAzul">{{ $pedido->reserva->dinero_a_cuenta ?? '-' }}</p>
                
            </div>
            
            <div class="idPedido">
                <div class="infoPedido">
                    <div class="producto">
                        <p>Producto: </p>
                        <p id="colorAzul">{{ $pedido->producto }}</p>
                    </div>
                </div>
                <div class="observaciones">
                    <p>Observaciones: </p>
                    <textarea id="colorAzul" readonly class="textarea">{{ $pedido->observaciones }}</textarea>
                </div>
            </div>
            
            <div class="bloqueFecha">
                <div id="colorAzul">
                    <p>{{ $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d/m/Y') : ' ' }}</p>
                </div>
                <div class="fechaGigante">
                    <p>{{ $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d') : ' ' }}</p>
                </div>
                <div class="horario">
                    <p>{{ $pedido->reserva->estado_pago }}</p>
                </div>
            </div>
        </div>

        <div class="segmentoMensaje">
            <div class="mensajeDeLado">
                <p>
                    {{-- FIX: Mensaje unificado en Pedido --}}
                    "{{ $pedido->texto_mensaje ?? ' ' }}"
                </p>
            </div>
            <div class="divVacio"></div>
            <div class="nombreDestinatario">
               <p>{{ $pedido->nombre_mensaje ?? " " }}</p> 
            </div>
        </div>
    </div>
</body>
</html>
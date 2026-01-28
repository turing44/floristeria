<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
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
            color: rgb(206, 0, 206);
            font-size: 75px;
        }
        .fechaGigante p{ margin: 7px 0; }
        .horario{
            display: flex;
            justify-content: flex-start;
            color: rgb(255, 0, 206);
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
        #colorAzul{ color: blue; }
        .identificadorPrimeraParte{
            position: fixed;
            top: 1.5mm;
            right: 3mm;
            font-size:35px; 
        }
        .identificadorSegundaParte{
            position: fixed;
            top: 100.5mm;
            right: 3mm;
            font-size:35px; 
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
    <div class="identificadorPrimeraParte">R{{$pedido->reserva->id}}</div>
    <div class="identificadorSegundaParte">R{{$pedido->reserva->id}}</div>
    <div>

        <div class="segmento">
            
            <div class="bloqueContacto">
                <p>Cliente:</p>
                <p id="colorAzul">{{ $pedido->cliente_nombre }}</p>
    
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>

                <p>Precio Total:</p>
                <p id="colorAzul">{{ $pedido->precio ?? 'No definido' }}</p>
                
                <p>Pendiente por pagar:</p>
                <p id="colorAzul">{{ $pedido->reserva->dinero_a_cuenta ?? '0' }}</p>
                
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
                
                <p id="colorAzul">{{ $pedido->cliente_telf }}</p>

                <p>Precio Total:</p>
                <p id="colorAzul">{{ $pedido->precio ?? 'No definido' }}</p>
                
                <p>Dinero a Contado:</p>
                <p id="colorAzul">{{ $pedido->reserva->dinero_a_cuenta ?? '0' }}</p>
                
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
    </div>
</body>
</html>
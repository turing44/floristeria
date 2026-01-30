<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .body { background-color: red; }

        .segmentoTicket {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            gap: 40px;
            height: 99mm;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .bloqueCliente{
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex: 1;
            overflow-wrap: anywhere;
            word-break: break-word;
            min-width: 0;
        }
        .bloqueCliente p {
            margin: 4px 0;
            padding-top: 5px;
        }

        .bloquePedido{
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            flex: 1;
        }

        .bloqueFecha {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            flex: 1;
        }

        .infoPedido{
            display: flex;
            flex-direction: column;
        }

        .filaDireccion, .filaProducto, .filaFuente{
            display: flex;
            flex-direction: row;
        }

        .cajaObservaciones{
            width: 370px;
            height: 120px;
            border: 1px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
            overflow: hidden;
            white-space: pre-wrap;
        }

        .filaFecha{ display: flex; }

        .diaGigante{
            display: flex;
            justify-content: flex-start;
            color: rgb(206, 0, 206);
            font-size: 75px;
        }
        .diaGigante p{ margin: 7px 0; }

        .lineaEstado{
            display: flex;
            justify-content: flex-start;
            color: rgb(255, 0, 206);
            font-size: 30px;
        }
        .lineaEstado p{ margin: 7px 0; }

        .checkbox input[type="checkbox"]{
            margin: 36px 0;
            transform: scale(6);
        }
        .confirmacionEntrega{
            margin: 0%;
            font-size: 50px;
        }

        .textoAzul{ 
            color: blue; 
        }

        .idReservaSuperior{
            position: fixed;
            top: 1.5mm;
            right: 3mm;
            font-size: 35px;
        }
        .idReservaInferior{
            position: fixed;
            top: 100.5mm;
            right: 3mm;
            font-size: 35px;
        }
        .logoEmpresa{
            left: 5mm;
        }
        .logoEmpresa img{
            width: 50mm;
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
            .segmentoTicket {
                padding: 5mm;
                page-break-inside: avoid;
            }
        }
     </style>
</head>

<body>
    @php
        use Carbon\Carbon;

        if(isset($reserva)) {
            $pedido = $reserva->pedido;
            $pedido->setRelation('reserva', $reserva);
        }

        $fechaFormateada = $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d/m/Y') : ' ';
        $diaFormateado   = $pedido->fecha ? Carbon::parse($pedido->fecha)->format('d') : ' ';
        $dineroACuenta   = $pedido->precio - $pedido->reserva->dinero_pendiente;

        //buscar ruta imagen
        $path = resource_path('images/logoPdfReservas.png'); 

        if (file_exists($path)) {
            //mirar extendion
            $type = pathinfo($path, PATHINFO_EXTENSION);
            //cogemos la imagen
            $data = file_get_contents($path);
            //lo ponemos en base64
            $imagenBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $imagenBase64 = null;
        }
    @endphp

    <div class="lineasFijas"></div>
    <div class="idReservaSuperior">R{{$pedido->reserva->id}}</div>
    <div class="idReservaInferior">R{{$pedido->reserva->id}}</div>

    <div>
        @for($i = 0; $i < 2; $i++)
            <div class="segmentoTicket">
                <div class="bloqueCliente">
                    <div class="logoEmpresa">
                        @if($imagenBase64)
                            <img src="{{ $imagenBase64 }}">
                        @else
                            <p>Error: No se encontró la imagen en resources/images</p>
                        @endif
                    </div>
                    <div class="textoAzul">
                        <p>{{ $fechaFormateada }}</p>
                    </div>
                    <p>Cliente:</p>
                    <p class="textoAzul">{{ $pedido->cliente_nombre }}</p>

                    <p class="textoAzul">{{ $pedido->cliente_telf }}</p>

                     <p>Hora Recogida:</p>
                    <p class="textoAzul">{{$pedido->reserva->hora_recogida}}</p>

                </div>

                <div class="bloquePedido">
                    <div class="infoPedido">
                        <div class="filaProducto">
                            <p>Producto: </p>
                            <p class="textoAzul">{{ $pedido->producto }}</p>
                        </div>
                    </div>

                    <div class="observaciones">
                        <p>Observaciones: </p>
                        <div class="cajaObservaciones textoAzul">{{ $pedido->observaciones }}</div>
                    </div>
                </div>

                <div class="bloqueFecha">
                    
                    <div class="diaGigante">
                        <p>{{ $diaFormateado }}</p>
                    </div>

                    <div class="lineaEstado">
                        <p>{{ $pedido->reserva->estado_pago }}</p>
                    </div>
                    
                    <p class="textoAzul">Precio Total:{{ $pedido->precio ?? 'No definido' }}€</p>
                    <p class="textoAzul">Dinero Pendiente:{{ $pedido->reserva->dinero_pendiente ?? '0' }}€</p>
                    <p class="textoAzul">Dinero A Cuenta:{{ $dineroACuenta }}€</p>
                </div>

            </div>
        @endfor
    </div>
</body>
</html>


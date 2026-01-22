@php
    $path = public_path('fonts/LucidaUnicodeCalligraphy.ttf');
    
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataFont = file_get_contents($path);
        $base64Font = 'data:font/' . $type . ';base64,' . base64_encode($dataFont);
    } else {
        $base64Font = ''; 
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <base href="{{ url('/') }}"> 
    <style>
        @font-face {
            font-family: "Lucida Calligraphy";
            src: url("{!! $base64Font !!}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }

        @page {
            size: A4;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 297mm; /* Forzamos altura completa A4 */
            background-color: white;
        }
        
        /* --- ESTO ES LO NUEVO: EL CONTENEDOR QUE EMPUJA TODO ABAJO --- */
        .contenedor-inferior {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 99mm; /* La altura exacta de la tarjeta */
            /* Border opcional para ver donde acaba si quieres depurar: border-top: 1px dashed blue; */
        }

        .segmentoMensaje {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%; /* Ocupa todo el alto del contenedor inferior */
            gap: 40px;
            box-sizing: border-box;
        }
        
        .divVacio {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 0;
        }
        
        .mensajeDeLado {
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
            transform: rotate(180deg); /* Se mantiene tu rotación original */
            transform-origin: center; 
            text-align: center;
            font-family: "Lucida Calligraphy";
            flex: 1;
            min-width: 0;
            font-size: 16px;
        }
        
        .nombreDestinatario {
            display: flex;
            height: 200px;
            justify-content: center;
            align-items: center;
            writing-mode: vertical-rl;   
            text-orientation: mixed;
            overflow-wrap: break-word;
            transform: rotate(180deg);
            transform-origin: center;
            font-size: 30px; 
            text-align: center;
            flex: 1;
            min-width: 0;
            font-family: "Lucida Calligraphy";
        }
        
        /* Líneas de corte */
        .lineasFijas {
            position: absolute;        
            left: 0;
            bottom: 25px; /* Ajustado para que cuadre visualmente */
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
     </style>
</head>

<body>
    <div class="contenedor-inferior">
        
        <div class="lineasFijas"></div>

        <div class="segmentoMensaje">
            
            {{-- Mensaje Izquierda --}}
            <div class="mensajeDeLado">
                <p>
                    "{{ $data['texto_mensaje'] }}"
                </p>
            </div>
            
            {{-- Espacio Centro --}}
            <div class="divVacio"></div>
            
            {{-- Nombre Derecha --}}
            <div class="nombreDestinatario">
                <p>{{ $data['nombre_mensaje'] ?? '' }}</p> 
            </div>
            
        </div>
    </div>
</body>
</html>
<?php

return [
    'google_habilitado' => env('FLORISTERIA_GOOGLE_HABILITADO', false),

    'pdfs' => [
        'disk' => env('FLORISTERIA_PDF_DISK', 'local'),
        'carpeta' => env('FLORISTERIA_PDF_CARPETA', 'pdfs/pedidos'),
    ],
];

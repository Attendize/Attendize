<?php

return [

    'debug'       => env('APP_DEBUG_PDF', true),
    'binpath'     => env('WKHTML2PDF_BIN_PATH', '/usr/bin/'),
    'binfile'     => env('WKHTML2PDF_BIN_FILE', 'wkhtmltopdf'),
    'output_mode' => 'I',
];

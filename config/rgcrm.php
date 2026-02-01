<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UI · Badge variants
    |--------------------------------------------------------------------------
    | Clases Tailwind para badges del sidebar y otras vistas.
    | Centraliza colores y estilos visuales.
    |--------------------------------------------------------------------------
    */

    'badge_variants' => [
        'red' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
        'green' => 'bg-green-50 text-green-700 ring-1 ring-green-200',
        'gray' => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200',
        'default' => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200',
    ],

    /*
    |--------------------------------------------------------------------------
    | Usuarios
    |--------------------------------------------------------------------------
    */

    'default_role' => env('RCGCRM_DEFAULT_ROLE', 'Ejecutivo'),

];

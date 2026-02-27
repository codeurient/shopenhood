<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sensitive Data Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used exclusively for encrypting sensitive personal and tax
    | identity fields (FIN, national ID, tax registration numbers, etc.) stored
    | in the database.  It is intentionally separate from APP_KEY so that a leak
    | of APP_KEY alone cannot expose this data.
    |
    | Generate a key with:
    |   php artisan tinker --execute="echo base64_encode(random_bytes(32));"
    |
    */

    'key' => env('SENSITIVE_DATA_KEY'),

];

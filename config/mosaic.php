<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Mosaic Public API
    |--------------------------------------------------------------------------
    |
    | base_url : Mosaic panel adresi (ornek: https://www.mosaic.net.tr)
    | api_key  : Kuruma ozel read-only API key. Panelden
    |            "Kurumlar -> Duzenle -> Public API" bolumunden uretilir.
    |
    */

    'base_url' => env('MOSAIC_BASE_URL', 'https://www.mosaic.net.tr'),

    'api_key' => env('MOSAIC_API_KEY'),

    // Guzzle istek zaman asimi (saniye)
    'timeout' => env('MOSAIC_TIMEOUT', 10),

);

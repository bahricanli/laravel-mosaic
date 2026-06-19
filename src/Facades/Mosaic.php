<?php

namespace BahriCanli\Mosaic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array overview()
 * @method static array followers()
 * @method static array followersHistory($platform = null, $days = 30)
 * @method static array posts($platform = null, $limit = 20)
 *
 * @see \BahriCanli\Mosaic\MosaicClient
 */
class Mosaic extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mosaic';
    }
}

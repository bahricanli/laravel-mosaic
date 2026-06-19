<?php

namespace BahriCanli\Mosaic;

use Illuminate\Support\ServiceProvider;

class MosaicServiceProvider extends ServiceProvider
{
    /**
     * Config publish.
     */
    public function boot()
    {
        $this->publishes(array(
            __DIR__ . '/../config/mosaic.php' => $this->configPath('mosaic.php'),
        ), 'mosaic-config');
    }

    /**
     * Servisleri kaydet.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mosaic.php', 'mosaic');

        $this->app->singleton('mosaic', function ($app) {
            $config = $app['config']['mosaic'];

            return new MosaicClient(
                isset($config['base_url']) ? $config['base_url'] : 'https://www.mosaic.net.tr',
                isset($config['api_key']) ? $config['api_key'] : null,
                isset($config['timeout']) ? $config['timeout'] : 10
            );
        });

        $this->app->alias('mosaic', MosaicClient::class);
    }

    /**
     * config_path() helper'i bazi eski kurulumlarda olmayabilir; guvenli yol.
     *
     * @param string $file
     * @return string
     */
    protected function configPath($file)
    {
        if (function_exists('config_path')) {
            return config_path($file);
        }

        return $this->app->basePath() . '/config/' . $file;
    }

    /**
     * @return array
     */
    public function provides()
    {
        return array('mosaic', MosaicClient::class);
    }
}

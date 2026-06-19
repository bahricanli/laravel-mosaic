<?php

namespace BahriCanli\Mosaic;

use BahriCanli\Mosaic\Exceptions\MosaicException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Mosaic Public API istemcisi.
 *
 * Tum istekler kuruma ozel API key ile dogrulanir ve salt-okunur veri doner.
 * PHP 7.0 / Laravel 5.5+ uyumlu (Guzzle 6+).
 */
class MosaicClient
{
    /** @var string */
    protected $baseUrl;

    /** @var string|null */
    protected $apiKey;

    /** @var int */
    protected $timeout;

    /** @var GuzzleClient */
    protected $http;

    /**
     * @param string            $baseUrl Mosaic panel adresi (https://www.mosaic.net.tr)
     * @param string|null       $apiKey  Kuruma ozel API key
     * @param int               $timeout Istek zaman asimi (saniye)
     * @param GuzzleClient|null $http    Test/ozellestirme icin Guzzle istemcisi
     */
    public function __construct($baseUrl, $apiKey = null, $timeout = 10, $http = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->timeout = (int) $timeout;
        // PHP 7.0 uyumu icin tip ipucu yerine instanceof kontrolu
        $this->http    = ($http instanceof GuzzleClient) ? $http : new GuzzleClient();
    }

    /**
     * Kurum bilgisi + her platformun guncel takipcisi + birlesik ozet.
     *
     * @return array
     */
    public function overview()
    {
        return $this->get('overview');
    }

    /**
     * Her platformun en guncel takipci sayisi.
     *
     * @return array
     */
    public function followers()
    {
        return $this->get('followers');
    }

    /**
     * Tarih bazli takipci serisi (grafik icin).
     *
     * @param string|null $platform Tek platform (ornek: instagram). Bossa tum platformlar.
     * @param int         $days     Kac gunluk gecmis (varsayilan 30, en fazla 365).
     * @return array
     */
    public function followersHistory($platform = null, $days = 30)
    {
        $query = array('days' => $days);
        if ($platform !== null && $platform !== '') {
            $query['platform'] = $platform;
        }

        return $this->get('followers/history', $query);
    }

    /**
     * Cekilen postlar + etkilesim metrikleri.
     *
     * @param string|null $platform Tek platforma filtrele.
     * @param int         $limit    Maksimum post sayisi (varsayilan 20, en fazla 50).
     * @return array
     */
    public function posts($platform = null, $limit = 20)
    {
        $query = array('limit' => $limit);
        if ($platform !== null && $platform !== '') {
            $query['platform'] = $platform;
        }

        return $this->get('posts', $query);
    }

    /**
     * Bu istemcinin kullandigi API key'i degistirir (akici kullanim).
     *
     * @param string $apiKey
     * @return $this
     */
    public function withApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * GET istegi atar, JSON cozer.
     *
     * @param string $path
     * @param array  $query
     * @return array
     *
     * @throws MosaicException
     */
    protected function get($path, array $query = array())
    {
        if (empty($this->apiKey)) {
            throw new MosaicException('Mosaic API key tanimli degil (MOSAIC_API_KEY).');
        }

        $url = $this->baseUrl . '/api/public/v1/' . ltrim($path, '/');

        try {
            $response = $this->http->request('GET', $url, array(
                'headers' => array(
                    'X-Api-Key' => $this->apiKey,
                    'Accept'    => 'application/json',
                ),
                'query'       => $query,
                'timeout'     => $this->timeout,
                'http_errors' => true,
            ));
        } catch (GuzzleException $e) {
            throw new MosaicException('Mosaic API istegi basarisiz: ' . $e->getMessage(), $e->getCode(), $e);
        }

        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MosaicException('Mosaic API gecersiz JSON dondu.');
        }

        return $data;
    }
}

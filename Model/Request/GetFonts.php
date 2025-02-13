<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Request;

use InvalidArgumentException;
use PiyRibbons\PiyOnline\Model\Cache\Type\DashboardApi;

class GetFonts extends AbstractRequest
{
    public const ENDPOINT = 'fonts';
    public const METHOD = 'get';

    /**
     * @inheritcoc
     */
    protected function getCachedResponse()
    {
        $cachedResponse = $this->cache->load(DashboardApi::CACHE_KEY_FONTS);

        if ($cachedResponse) {
            try {
                return $this->json->unserialize($cachedResponse);
            } catch (InvalidArgumentException $e) {
                $this->cache->remove(DashboardApi::CACHE_KEY_FONTS);
            }
        }

        return false;
    }

    /**
     * @param $response array
     *
     * @inheritcoc
     */
    protected function cacheResponse(array $response): void
    {
        $cacheData = $this->json->serialize($response);
        $this->cache->save(
            $cacheData,
            DashboardApi::CACHE_KEY_FONTS,
            [DashboardApi::CACHE_TAG],
            DashboardApi::CACHE_LIFETIME_FONTS
        );
    }
}

<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Request;

use InvalidArgumentException;
use PiyRibbons\PiyOnline\Model\Cache\Type\DashboardApi;

class GetTemplates extends AbstractRequest
{
    public const ENDPOINT = 'templates';
    public const METHOD = 'get';

    /**
     * @inheritcoc
     */
    public function getCachedResponse()
    {
        $cachedResponse = $this->cache->load(DashboardApi::CACHE_KEY_TEMPLATES);

        if ($cachedResponse) {
            try {
                return $this->json->unserialize($cachedResponse);
            } catch (InvalidArgumentException $e) {
                $this->cache->remove(DashboardApi::CACHE_KEY_TEMPLATES);
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
            DashboardApi::CACHE_KEY_TEMPLATES,
            [DashboardApi::CACHE_TAG],
            DashboardApi::CACHE_LIFETIME_TEMPLATES
        );
    }
}

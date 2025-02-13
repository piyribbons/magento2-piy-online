<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Cache\Type;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class DashboardApi extends TagScope
{
    public const TYPE_IDENTIFIER = 'piy_dashboard_api';
    public const CACHE_TAG = 'PIY_DASHBOARD_API';


    public const CACHE_KEY_FONTS = self::CACHE_TAG . '_FONTS';
    public const CACHE_LIFETIME_FONTS = 604800;

    public const CACHE_KEY_TEMPLATES = self::CACHE_TAG . '_TEMPLATES';
    public const CACHE_LIFETIME_TEMPLATES = 604800;

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(FrontendPool $cacheFrontendPool)
    {
        parent::__construct(
            $cacheFrontendPool->get(self::TYPE_IDENTIFIER),
            self::CACHE_TAG
        );
    }
}

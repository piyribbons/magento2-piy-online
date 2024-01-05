<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AllowRibbon implements OptionSourceInterface
{
    public const ALLOW_ALL = 1;
    public const ALLOW_CATEGORIES = 2;
    public const ALLOW_ATTRIBUTE_SET = 3;
    public const ALLOW_PRODUCT = 4;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            self::ALLOW_ALL => __('Allow for all Products'),
            self::ALLOW_CATEGORIES => __('Allow for the following Categories'),
            self::ALLOW_ATTRIBUTE_SET => __('Allow for the following Attribute sets'),
            self::ALLOW_PRODUCT => __('Allow on product level')
        ];
    }
}

<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\ViewModel\Cart\Form;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Config\Source\Font;
use PiyRibbons\PiyOnline\ViewModel\BaseRibbon;

class Ribbon extends BaseRibbon
{

    /**
     * @param Config $config
     * @param Font $font
     */
    public function __construct(
        Config $config,
        Font $font
    ) {
        parent::__construct($font, $config);
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    public function shouldRender(ProductInterface $product): bool
    {
        return $this->config->shouldRender($product);
    }

    /**
     * @param AbstractItem $item
     * @return string
     */
    public function getExistingValue(AbstractItem $item): string
    {
        $buyRequest = $item->getBuyRequest();
        return $buyRequest->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME) ?? '';
    }

    /**
     * @param AbstractItem $item
     * @return int|null
     */
    public function getExistingFont(AbstractItem $item): ?int
    {
        $buyRequest = $item->getBuyRequest();
        return $buyRequest->getData(Config::PIY_RIBBON_FONT_INPUT_NAME) ? (int) $buyRequest->getData(Config::PIY_RIBBON_FONT_INPUT_NAME) : null;
    }
}

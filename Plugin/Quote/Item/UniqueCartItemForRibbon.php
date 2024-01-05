<?php

declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Plugin\Quote\Item;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item;
use PiyRibbons\PiyOnline\Model\Config;

/**
 * This plugin products with ribbon data are added as unique cart items
 */
class UniqueCartItemForRibbon
{
    /**
     * @param Item $subject
     * @param bool $result
     * @param Product $product
     * @return bool
     */
    public function afterRepresentProduct(
        Item $subject,
        bool $result,
        Product $product
    ): bool {
        $buyRequest = $subject->getBuyRequest();
        if ($buyRequest->getData(Config::PIY_RIBBON_FONT_INPUT_NAME) &&
            $buyRequest->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)) {
            return false;
        }

        if ($product->getData(Config::PIY_RIBBON_FONT_INPUT_NAME) &&
            $product->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)
        ) {
            return false;
        }

        return $result;
    }
}

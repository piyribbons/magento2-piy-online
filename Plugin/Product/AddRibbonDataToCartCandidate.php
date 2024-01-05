<?php

declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Plugin\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use PiyRibbons\PiyOnline\Model\Config;

class AddRibbonDataToCartCandidate
{
    /**
     * @param AbstractType $subject
     * @param array $result
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string|null $processMode
     * @return array
     */
    public function afterPrepareForCartAdvanced(
        AbstractType $subject,
        array $result,
        DataObject $buyRequest,
        Product$product,
        ?string $processMode = null
    ): array {

        foreach ($result as $preparedProduct) {
            $preparedProduct->addData([
                Config::PIY_RIBBON_FONT_INPUT_NAME => $buyRequest->getData(Config::PIY_RIBBON_FONT_INPUT_NAME),
                Config::PIY_RIBBON_TEXT_INPUT_NAME => $buyRequest->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)
            ]);
        }

        return $result;
    }
}

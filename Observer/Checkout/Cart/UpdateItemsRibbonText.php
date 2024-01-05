<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Observer\Checkout\Cart;

use Magento\Checkout\Model\Cart;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use PiyRibbons\PiyOnline\Model\Config;

/**
 * Observer used to store the ribbon text as a quote item option
 */
class UpdateItemsRibbonText implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @var Json
     */
    private Json $serializer;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param Json $serializer
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        Json $serializer
    ) {
        $this->cartRepository = $cartRepository;
        $this->serializer = $serializer;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var Cart $cart */
        $cart = $event->getData('cart');

        /** @var DataObject $infoDataObject */
        $infoDataObject = $event->getData('info');

        foreach ($infoDataObject->toArray() as $itemId => $itemInfo) {
            if (!isset($itemInfo[Config::PIY_RIBBON_TEXT_INPUT_NAME])) {
                continue;
            }

            $quote = $cart->getQuote();
            $item = $quote->getItemById($itemId);
            if (!$item) {
                continue;
            }

            $buyRequestOption = $item->getOptionByCode('info_buyRequest');
            if (!$buyRequestOption) {
                continue;
            }

            $buyRequest = $this->serializer->unserialize($buyRequestOption->getValue());

            $ribbonTextValue = $itemInfo[Config::PIY_RIBBON_TEXT_INPUT_NAME];
            if ($ribbonTextValue) {
                $buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME] = $ribbonTextValue;
            } elseif (isset($buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME])) {
                unset($buyRequest[Config::PIY_RIBBON_TEXT_INPUT_NAME]);
            }

            $ribbonFontValue = $itemInfo[Config::PIY_RIBBON_FONT_INPUT_NAME];
            if ($ribbonFontValue) {
                $buyRequest[Config::PIY_RIBBON_FONT_INPUT_NAME] = $ribbonFontValue;
            } elseif (isset($buyRequest[Config::PIY_RIBBON_FONT_INPUT_NAME])) {
                unset($buyRequest[Config::PIY_RIBBON_FONT_INPUT_NAME]);
            }

            $buyRequestOption->setValue($this->serializer->serialize($buyRequest));
            $quote->updateItem($itemId, $buyRequest);

            $this->cartRepository->save($quote);
        }
    }

    /**
     * @param Item $item
     * @return array
     */
    private function getBuyRequest(Item $item): array
    {
        $option = $item->getOptionByCode('info_buyRequest');
        return $option ? (array) $this->serializer->unserialize($option->getValue()) : [];
    }

    /**
     * @param Item $item
     * @param array $updatedBuyRequest
     * @return void
     * @throws LocalizedException
     */
    private function setBuyRequest(Item $item, array $updatedBuyRequest)
    {
        $existingBuyRequest = $item->getOptionByCode('info_buyRequest');
        if ($existingBuyRequest) {
            $existingBuyRequest->setValue($this->serializer->serialize($updatedBuyRequest));
        } else {
            $item->addOption([
                'code' => 'info_buyRequest',
                'value' =>$this->serializer->serialize($updatedBuyRequest)
            ]);
        }
    }
}

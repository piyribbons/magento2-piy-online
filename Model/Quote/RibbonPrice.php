<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Quote;

use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Pricing\Adjustment;

class RibbonPrice extends AbstractTotal
{
    public const COLLECTOR_TYPE_CODE = 'piy-ribbon';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Adjustment
     */
    private Adjustment $priceAdjustment;

    /**
     * Custom constructor.
     */
    public function __construct(
        Config $config,
        Adjustment $priceAdjustment
    ) {
        $this->config = $config;
        $this->priceAdjustment = $priceAdjustment;
        $this->setCode(self::COLLECTOR_TYPE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $items = $shippingAssignment->getItems();
        if (!count($items) || !$this->config->isPriceAdjustmentEnabled()) {
            return $this;
        }

        $amount = 0;
        foreach($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getBuyRequest()->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)) {
                $amount += $quoteItem->getQty() * $this->priceAdjustment->getAdjustmentValue();
            }
        }

        $total->setTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, $amount);
        $total->setCustomAmount($amount);
        $total->setBaseCustomAmount($amount);
        $total->setGrandTotal($total->getGrandTotal() + $amount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $amount);

        return $this;
    }

    /**
     * @param Total $total
     */
    protected function clearValues(Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount(self::COLLECTOR_TYPE_CODE, 0);
        $total->setBaseTotalAmount(self::COLLECTOR_TYPE_CODE, 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(
        Quote $quote,
        Total $total
    ) {
        $amount = 0;
        $count = 0;

        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($quoteItem->getBuyRequest()->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)) {
                $amount += $quoteItem->getQty() * $this->priceAdjustment->getAdjustmentValue();
                $count += $quoteItem->getQty();
            }
        }

        return [
            'code' => $this->getCode(),
            'title' => __('Personalised Ribbon Total'),
            'value' => $amount
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Personalised Ribbon');
    }
}

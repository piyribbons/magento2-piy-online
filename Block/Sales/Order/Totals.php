<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Block\Sales\Order;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Quote\RibbonPrice;
use PiyRibbons\PiyOnline\Pricing\Adjustment;

class Totals extends Template
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Adjustment
     */
    private Adjustment $adjustment;

    /**
     * @param Context $context
     * @param Config $config
     * @param Adjustment $adjustment
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Adjustment $adjustment,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->adjustment = $adjustment;
    }

    /***
     * @return Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        if (!$this->config->isPriceAdjustmentEnabled() || !$this->adjustment->getAdjustmentValue()) {
            return $this;
        }

        $totalRibbonCost = 0;
        $items = $this->getSource()->getAllItems();
        $store = $this->getSource()->getStore();

        foreach ($items as $item) {
            if ($item->getBuyRequest()->getData(Config::PIY_RIBBON_TEXT_INPUT_NAME)) {
                $totalRibbonCost += $this->adjustment->getAdjustmentValue() * $item->getQtyOrdered();
            }
        }
        if ($totalRibbonCost) {
            $totals = $this->getParentBlock()->getTotals();
            $total = new DataObject(
                [
                    'code' => RibbonPrice::COLLECTOR_TYPE_CODE,
                    'label' => __('Personalised ribbon(s)'),
                    'value' => $totalRibbonCost,
                    'base_value' => $totalRibbonCost
                ]
            );

            if (isset($totals['grand_total_incl'])) {
                $this->getParentBlock()->addTotalBefore($total, 'grand_total');
            } else {
                $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
            }
        }
        return $this;
    }
}

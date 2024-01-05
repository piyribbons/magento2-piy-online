<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Pricing;

use Magento\Framework\Pricing\Adjustment\AdjustmentInterface;
use Magento\Framework\Pricing\SaleableInterface;
use PiyRibbons\PiyOnline\Model\Config;

class Adjustment implements AdjustmentInterface
{

    public const ADJUSTMENT_CODE = 'piy_ribbon';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var float|null
     */
    private ?float $adjustmentValue = null;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }


    /**
     * @inheritDoc
     */
    public function getAdjustmentCode()
    {
        return self::ADJUSTMENT_CODE;
    }

    /**
     * @inheritDoc
     */
    public function isIncludedInBasePrice()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isIncludedInDisplayPrice()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function extractAdjustment($amount, SaleableInterface $saleableItem, $context = [])
    {
        if (!$this->config->isPriceAdjustmentEnabled() || !$this->getAdjustmentValue()) {
            return 0;
        }

        $adjustmentValue = $this->getAdjustmentValue() * $saleableItem->getQty();

        return $adjustmentValue - $amount;
    }

    /**
     * @inheritDoc
     */
    public function applyAdjustment($amount, SaleableInterface $saleableItem, $context = [])
    {
        if (!$this->config->isPriceAdjustmentEnabled() || !$this->getAdjustmentValue()) {
            return $amount;
        }

        $adjustmentValue = $this->getAdjustmentValue() * $saleableItem->getQty();

        return $amount + $adjustmentValue;
    }

    /**
     * @inheritDoc
     */
    public function isExcludedWith($adjustmentCode)
    {
        return $this->getAdjustmentCode() === $adjustmentCode;
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return 50;
    }

    /**
     * @return float
     */
    public function getAdjustmentValue(): float
    {
        if ($this->adjustmentValue === null) {
            $this->adjustmentValue = $this->config->getPriceAdjustmentValue();
        }

        return $this->adjustmentValue;
    }
}

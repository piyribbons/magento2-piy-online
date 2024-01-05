<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PiyRibbons\PiyOnline\Model\Config\Source\AllowRibbon;
use PiyRibbons\PiyOnline\Setup\Patch\Data\AddAllowRibbonProductAttribute;

class Config
{
    public const PIY_RIBBON_TEXT_INPUT_NAME = 'piy_ribbon_text';
    public const PIY_RIBBON_FONT_INPUT_NAME = 'piy_ribbon_font';
    public const CONFIG_PATH_API_KEY = 'piy_ribbons/dashboard/api_key';
    public const CONFIG_PATH_ENABLED = 'piy_ribbons/general/enabled';
    public const CONFIG_PATH_DEFAULT_TEMPLATE = 'piy_ribbons/general/default_template';
    public const CONFIG_PATH_DEFAULT_FONT = 'piy_ribbons/general/default_font';
    public const CONFIG_PATH_ALLOW_FOR = 'piy_ribbons/general/allow_for';
    public const CONFIG_PATH_ALLOW_FOR_CATEGORIES = 'piy_ribbons/general/allow_for_categories';
    public const CONFIG_PATH_ALLOW_FOR_ATTRIBUTE_SETS = 'piy_ribbons/general/allow_for_attribute_sets';
    public const CONFIG_PATH_ALLOW_EMOJIS = 'piy_ribbons/general/allow_emojis';
    public const CONFIG_PATH_ENABLE_EMOJI_PICKER = 'piy_ribbons/general/enable_emoji_picker';
    public const CONFIG_PATH_ORDER_PUSH_STATUS = 'piy_ribbons/orders/push_status';
    public const CONFIG_PATH_ENABLE_PRICE_ADJUSTMENT = 'piy_ribbons/pricing/enable_adjustment';
    public const CONFIG_PATH_PRICE_ADJUSTMENT_VALUE = 'piy_ribbons/pricing/adjustment_value';
    public const CONFIG_PATH_HTACCESS_USER = 'piy_ribbons/development/htaccess_user';
    public const CONFIG_PATH_HTACCESS_PASSWORD = 'piy_ribbons/development/htaccess_password';
    public const CONFIG_PATH_API_BASE_URL = 'piy_ribbons/development/api_base_url';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLED) ?? false;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_API_KEY) ?? '';
    }

    /**
     * @return string
     */
    public function getDefaultTemplate(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_TEMPLATE) ?? '';
    }

    /**
     * @return string
     */
    public function getDefaultFont(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_DEFAULT_FONT) ?? '';
    }

    /**
     * @return int
     */
    public function getAllowFor(): int
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOW_FOR);
    }

    /**
     * @return string
     */
    public function getAllowedCategories(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOW_FOR_CATEGORIES) ?? '';
    }

    /**
     * @return string
     */
    public function getAllowedAttributeSets(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ALLOW_FOR_ATTRIBUTE_SETS) ?? '';
    }

    /**
     * @return bool
     */
    public function isEmojiAllowed(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ALLOW_EMOJIS);
    }

    /**
     * @return bool
     */
    public function isEmojiPickerEnabled(): bool
    {
        return $this->isEmojiAllowed() && $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLE_EMOJI_PICKER);
    }

    /**
     * @return string
     */
    public function getOrderPushStatus(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_ORDER_PUSH_STATUS) ?? '';
    }

    /**
     * @return bool
     */
    public function isPriceAdjustmentEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_ENABLE_PRICE_ADJUSTMENT);
    }

    /**
     * @return float
     */
    public function getPriceAdjustmentValue(): float
    {
        return (float) ($this->scopeConfig->getValue(self::CONFIG_PATH_PRICE_ADJUSTMENT_VALUE) ?? 0);
    }

    /**
     * @return string
     */
    public function getHtaccessUser(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_HTACCESS_USER) ?? '';
    }

    /**
     * @return string
     */
    public function getHtaccessPassword(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_HTACCESS_PASSWORD) ?? '';
    }


    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_API_BASE_URL) ?? '';
    }

    /**
     * Check if the ribbon input should be rendered for the given product.
     *
     * @param ProductInterface $product
     * @return bool
     */
    public function shouldRender(ProductInterface $product): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        return match ($this->getAllowFor()) {
            AllowRibbon::ALLOW_ALL => true,
            AllowRibbon::ALLOW_CATEGORIES => $this->isProductInAllowedCategory($product),
            AllowRibbon::ALLOW_ATTRIBUTE_SET => $this->isProductInAllowedAttributeSet($product),
            AllowRibbon::ALLOW_PRODUCT => (bool)$product->getData(AddAllowRibbonProductAttribute::ALLOW_RIBBON_ATTR_CODE),
        };
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    private function isProductInAllowedCategory(ProductInterface $product): bool
    {
        $allowedCategories = explode(',', $this->getAllowedCategories());
        $productCategoryIds = $product->getCategoryIds();

        return (bool) count(array_intersect($allowedCategories, $productCategoryIds));
    }

    /**
     * @param ProductInterface $product
     * @return bool
     */
    private function isProductInAllowedAttributeSet(ProductInterface $product): bool
    {
        $allowedAttributeSets = explode(',', $this->getAllowedAttributeSets());
        return in_array($product->getAttributeSetId(), $allowedAttributeSets);
    }
}

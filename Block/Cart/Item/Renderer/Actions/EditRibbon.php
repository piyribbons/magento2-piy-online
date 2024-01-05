<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Framework\View\Element\Template\Context;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Config\Source\Font;

class EditRibbon extends Generic
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var Font
     */
    private Font $font;

    /**
     * @param Context $context
     * @param Config $config
     * @param Font $font
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Font $font,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->font = $font;
    }

    /**
     * @return bool
     */
    public function shouldRender(): bool
    {
        return $this->isProductVisibleInSiteVisibility() && $this->config->shouldRender(
            $this->getItem()->getProduct()
        );
    }

    /**
     * @return array
     */
    public function getFontOptions(): array
    {
        return $this->font->toOptionArray();
    }

    /**
     * @return array
     */
    public function getFontSources(): array
    {
        return $this->font->toSourcesOptionArray();
    }
}

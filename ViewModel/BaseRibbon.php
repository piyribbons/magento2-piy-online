<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Config\Source\Font;

class BaseRibbon implements ArgumentInterface
{
    /**
     * @var Font
     */
    protected Font $font;

    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @param Font $font
     * @param Config $config
     */
    public function __construct(
        Font $font,
        Config $config
    ) {
        $this->font = $font;
        $this->config = $config;
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

    /**
     * @return bool
     */
    public function isEmojiAllowed(): bool
    {
        return $this->config->isEmojiAllowed();
    }

    /**
     * @return bool
     */
    public function isEmojiPickerEnabled(): bool
    {
        return $this->config->isEmojiPickerEnabled();
    }
}

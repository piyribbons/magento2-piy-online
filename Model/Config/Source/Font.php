<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PiyRibbons\PiyOnline\Model\Request\GetFonts;

class Font implements OptionSourceInterface
{
    /**
     * @var GetFonts
     */
    private GetFonts $getFonts;

    /**
     * @param GetFonts $getFonts
     */
    public function __construct(GetFonts $getFonts)
    {
        $this->getFonts = $getFonts;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $fonts = $this->getFonts->execute();
        $options = [];

        foreach ($fonts as $font) {
            $options[$font['id']] = $font['family'];
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toSourcesOptionArray(): array
    {
        $fonts = $this->getFonts->execute();
        $options = [];

        foreach ($fonts as $font) {
            $options[$font['id']] = $font['source'];
        }

        return $options;
    }
}

<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use PiyRibbons\PiyOnline\Model\Request\GetTemplates;

class Template extends AbstractSource
{
    /**
     * @var GetTemplates
     */
    private GetTemplates $getTemplates;

    /**
     * @param GetTemplates $getTemplates
     */
    public function __construct(GetTemplates $getTemplates)
    {
        $this->getTemplates = $getTemplates;
    }

    /**
     * @return string[][]
     */
    public function getAllOptions()
    {
        $result = [
            ['value' => 0, 'label' => __('-- Use Default --')]
        ];
        $templates = $this->getTemplates->execute();

        foreach ($templates as $template) {
            $result[] = ['value' => $template['id'], 'label' => $template['name']];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}

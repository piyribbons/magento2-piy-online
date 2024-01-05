<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class Category implements OptionSourceInterface
{
    public const SYSTEM_CATEGORY_ID = 1;
    public const ROOT_LEVEL = 1;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $categoryCollectionFactory;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function toArray(): array
    {
        return $this->getChildren(self::SYSTEM_CATEGORY_ID, self::ROOT_LEVEL);
    }

    /**
     * @param int $parentCategoryId
     * @param int $level
     * @return array
     * @throws LocalizedException
     */
    private function getChildren(int $parentCategoryId, int $level): array
    {
        $options = [];
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToFilter('level', $level);
        $collection->addAttributeToFilter('parent_id', $parentCategoryId);
        $collection->addAttributeToFilter('is_active', 1);
        $collection->setOrder('position', 'asc');

        foreach ($collection as $category) {
            if ($category->getLevel() > self::ROOT_LEVEL) {
                $options[$category->getId()] =
                    str_repeat(". ", max(0, ($category->getLevel() - 2) * 3)) . $category->getName();
            }
            if ($category->hasChildren()) {
                $options = array_replace($options, $this->getChildren((int) $category->getId(), ((int) $category->getLevel()) + 1));
            }
        }

        return $options;
    }
}

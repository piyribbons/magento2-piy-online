<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Model\Config\Source;

use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class AttributeSet implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $attributeSetCollectionFactory;

    /**
     * @param CollectionFactory $attributeSetCollectionFactory
     */
    public function __construct(
        CollectionFactory $attributeSetCollectionFactory
    ) {
        $this->attributeSetCollectionFactory = $attributeSetCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $collection = $this->attributeSetCollectionFactory->create();
        $collection->setEntityTypeFilter(CategorySetup::CATALOG_PRODUCT_ENTITY_TYPE_ID);
        return $collection->toOptionArray();
    }
}

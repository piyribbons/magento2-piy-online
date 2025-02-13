<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validator\ValidateException;
use PiyRibbons\PiyOnline\Model\Cache\Type\DashboardApi;

class EnableDashboardApiCache implements DataPatchInterface
{
    /**
     * @var Manager $cacheManager
     */
    private Manager $cacheManager;

    /**
     * @param Manager $cacheManager
     */
    public function __construct(Manager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return DataPatchInterface
     */
    public function apply()
    {
        $this->cacheManager->setEnabled([DashboardApi::TYPE_IDENTIFIER], true);
        return $this;
    }

    /**
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }
}

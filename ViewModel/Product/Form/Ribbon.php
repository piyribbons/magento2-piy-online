<?php
declare(strict_types=1);

namespace PiyRibbons\PiyOnline\ViewModel\Product\Form;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Context;
use PiyRibbons\PiyOnline\Model\Config;
use PiyRibbons\PiyOnline\Model\Config\Source\Font;
use PiyRibbons\PiyOnline\ViewModel\BaseRibbon;

class Ribbon extends BaseRibbon
{
    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Config $config
     * @param Font $font
     */
    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        Config $config,
        Font $font
    ) {
        $this->context = $context;
        $this->productRepository = $productRepository;

        parent::__construct($font, $config);
    }

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface
    {
        $id = $this->context->getParam('id');

        if (!$id) {
            return null;
        }

        try {
            return $this->productRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function shouldRender(): bool
    {
        $product = $this->getProduct();
        if (!$product) {
            return false;
        }

        return $this->config->shouldRender($product);
    }
}

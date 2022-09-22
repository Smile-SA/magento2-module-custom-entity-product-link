<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Catalog\Controller\Adminhtml\Product\Initialization;

use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\Catalog\Model\Product;
use Smile\CustomEntityProductLink\Helper\Data;

/**
 * Plugin for the product save data initialization.
 */
class HelperPlugin
{
    private Data $helper;

    /**
     * Constructor.
     *
     * @param Data $helper Custom entity helper.
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Clean custom entity input of the product edit form.
     *
     * @param Helper $helper Original helper.
     * @param Product $product Product.
     * @param array $productData Post product data.
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    // @codingStandardsIgnoreLine
    public function beforeInitializeFromData(
        Helper $helper,
        Product $product,
        array $productData
    ): ?array {
        foreach ($this->helper->getCustomEntityProductAttributes() as $attribute) {
            if (!isset($productData[$attribute->getAttributeCode()])) {
                $productData[$attribute->getAttributeCode()] = [];
            }
        }

        return [$product, $productData];
    }
}

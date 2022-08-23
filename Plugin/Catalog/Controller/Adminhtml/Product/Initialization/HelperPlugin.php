<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Catalog\Controller\Adminhtml\Product\Initialization;

/**
 * Plugin for the product save data initialization.
 */
class HelperPlugin
{
    /**
     * @var \Smile\CustomEntityProductLink\Helper\Data
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param \Smile\CustomEntityProductLink\Helper\Data $helper Custom entity helper.
     */
    public function __construct(\Smile\CustomEntityProductLink\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Clean custom entity input of the product edit form.
     *
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $helper      Original helper.
     * @param \Magento\Catalog\Model\Product                                      $product     Product.
     * @param array                                                               $productData Post product data.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    // @codingStandardsIgnoreLine
    public function beforeInitializeFromData(
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $helper,
        \Magento\Catalog\Model\Product $product,
        array $productData
    ) {
        foreach ($this->helper->getCustomEntityProductAttributes() as $attribute) {
            if (!isset($productData[$attribute->getAttributeCode()])) {
                $productData[$attribute->getAttributeCode()] = [];
            }
        }

        return [$product, $productData];
    }
}

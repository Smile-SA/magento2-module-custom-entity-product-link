<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Custom entity helper.
 */
class Data extends AbstractHelper
{
    /**
     * @var ProductAttributeInterface[]
     */
    private $customEntityProductAttributes;

    /**
     * Constructor.
     *
     * @param Context $context Context.
     * @param ProductAttributeCollectionFactory $productAttributeCollectionFactory Product attribute collection factory.
     */
    public function __construct(
        Context $context,
        ProductAttributeCollectionFactory $productAttributeCollectionFactory
    ) {
        parent::__construct($context);
        $this->initAttributes($productAttributeCollectionFactory);
    }

    /**
     * List of product attributes using custom entities as frontend input.
     *
     * @return ProductAttributeInterface[]
     */
    public function getCustomEntityProductAttributes(): array
    {
        return $this->customEntityProductAttributes;
    }

    /**
     * Init attribute list.
     *
     * @param ProductAttributeCollectionFactory $attributeCollectionFactory Product attribute collection factory.
     *
     * @return void
     */
    private function initAttributes(ProductAttributeCollectionFactory $attributeCollectionFactory): void
    {
        $attributeCollection = $attributeCollectionFactory->create();
        $attributeCollection->addFieldToFilter(ProductAttributeInterface::FRONTEND_INPUT, 'smile_custom_entity');

        $this->customEntityProductAttributes = $attributeCollection->getItems();
    }
}

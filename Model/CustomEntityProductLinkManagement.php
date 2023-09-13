<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Smile\CustomEntityProductLink\Helper\Data;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;

/**
 * Custom entity product link management implementation.
 */
class CustomEntityProductLinkManagement implements CustomEntityProductLinkManagementInterface
{
    private Data $helper;
    private CustomEntityCollectionFactory $customEntityCollectionFactory;
    private AttributeCollectionFactory $attributeCollectionFactory;
    private array $customEntityAttribute = [];

    /**
     * Constructor.
     *
     * @param Data $helper Custom entity helper.
     * @param CustomEntityCollectionFactory $customEntityCollectionFactory
     * @param AttributeCollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        Data $helper,
        CustomEntityCollectionFactory $customEntityCollectionFactory,
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->helper = $helper;
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Return custom entities assigned to a product.
     *
     * @param ProductInterface $product Product.
     * @return CustomEntityInterface[][]|null
     */
    public function getCustomEntities(ProductInterface $product): ?array
    {
        /** @var CustomEntityInterface[][] $entities */
        $entityIds = [];
        $entities = [];

        foreach ($this->helper->getCustomEntityProductAttributes() as $customEntityAttribute) {
            $customEntityAttributeCode = $customEntityAttribute->getAttributeCode();
            if ($product->getData($customEntityAttributeCode)) {
                foreach (explode(',', $product->getData($customEntityAttributeCode)) as $entityId) {
                    $entityIds[$entityId][] = $customEntityAttributeCode;
                }
            }
        }

        if (!empty($entityIds)) {
            $collection = $this->customEntityCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', ['in' => array_keys($entityIds)]);

            foreach ($collection->getItems() as $customEntity) {
                foreach ($entityIds[$customEntity->getId()] as $customEntityAttributeCode) {
                    $entities[$customEntityAttributeCode][] = $customEntity;
                }
            }
        }

        return $entities;
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;
use Smile\CustomEntityProductLink\Helper\Data;

/**
 * Custom entity product link management implementation.
 */
class CustomEntityProductLinkManagement implements CustomEntityProductLinkManagementInterface
{
    private Data $helper;
    private CustomEntityCollectionFactory $customEntityCollectionFactory;

    /**
     * Constructor.
     */
    public function __construct(
        Data $helper,
        CustomEntityCollectionFactory $customEntityCollectionFactory
    ) {
        $this->helper = $helper;
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
    }

    /**
     * Return custom entities assigned to a product.
     *
     * @return CustomEntityInterface[][]|null
     * @throws LocalizedException
     */
    public function getCustomEntities(Product $product): ?array
    {
        /** @var CustomEntityInterface[][] $entities */
        $entities = [];
        $entityIds = [];

        foreach ($this->helper->getCustomEntityProductAttributes() as $customEntityAttribute) {
            $customEntityAttributeCode = $customEntityAttribute->getAttributeCode();
            $productCustomEntityIds = $product->getData($customEntityAttributeCode);
            if ($productCustomEntityIds) {
                if (is_string($productCustomEntityIds)) {
                    $productCustomEntityIds = explode(',', $productCustomEntityIds);
                }
                foreach ($productCustomEntityIds ?? [] as $entityId) {
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

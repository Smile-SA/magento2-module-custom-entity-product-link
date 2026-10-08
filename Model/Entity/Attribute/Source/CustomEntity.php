<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Store\Model\StoreManagerInterface;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;

/**
 * Custom entity frontend model attribute.
 */
class CustomEntity extends AbstractSource
{
    private CustomEntityCollectionFactory $customEntityCollectionFactory;
    private StoreManagerInterface $storeManager;

    /** @var array<string, list<array{value: string, label: ?string}>> */
    private array $optionsCache = [];

    public function __construct(
        CustomEntityCollectionFactory $customEntityCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Get all options
     *
     * @return list<array{value: string, label: ?string}>
     */
    public function getAllOptions(): array
    {
        $attributeSetId = (int) $this->getAttribute()->getCustomEntityAttributeSetId();
        $storeId = $this->getAttribute()->getStoreId();
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $storeId = (int) $storeId;

        $cacheKey = sprintf('%d_%d', $attributeSetId, $storeId);
        if (!isset($this->optionsCache[$cacheKey])) {
            $options = [];
            $collection = $this->customEntityCollectionFactory->create()
                ->setStoreId($storeId)
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('attribute_set_id', $attributeSetId);

            foreach ($collection->getItems() as $customEntity) {
                $options[] = [
                    'value' => $customEntity->getId(),
                    'label' => $customEntity->getName(),
                ];
            }

            $this->optionsCache[$cacheKey] = $options;
        }

        return $this->optionsCache[$cacheKey];
    }
}

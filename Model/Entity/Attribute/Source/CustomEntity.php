<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;

/**
 * Custom entity frontend model attribute.
 */
class CustomEntity extends AbstractSource
{
    private CustomEntityCollectionFactory $customEntityCollectionFactory;

    public function __construct(CustomEntityCollectionFactory $customEntityCollectionFactory)
    {
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
    }

    /**
     * Get all options
     */
    public function getAllOptions(): array
    {
        $options = [];
        $collection = $this->customEntityCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('attribute_set_id', $this->getAttribute()->getCustomEntityAttributeSetId());

        foreach ($collection->getItems() as $customEntity) {
            $options[] =  [
                'value' => $customEntity->getId(),
                'label' => $customEntity->getName(),
            ];
        }

        return $options;
    }
}

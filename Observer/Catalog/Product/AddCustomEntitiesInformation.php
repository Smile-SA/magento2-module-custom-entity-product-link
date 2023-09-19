<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;

/**
 * Add custom entities information on product collection.
 */
class AddCustomEntitiesInformation implements ObserverInterface
{
    private Config $catalogConfig;
    private CustomEntityCollectionFactory $customEntityCollectionFactory;

    public function __construct(
        Config $catalogConfig,
        CustomEntityCollectionFactory $customEntityCollectionFactory
    ) {
        $this->catalogConfig = $catalogConfig;
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
    }

    /**
     * Add custom entities information on product collection.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(Observer $observer): void
    {
        /** @var Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $attributeCodes = $this->getCustomEntityProductAttribute();
        $productCustomEntityList = [];
        $customEntityIds = [];

        if (empty($attributeCodes)) {
            return;
        }

        foreach ($collection as $product) {
            foreach ($attributeCodes as $attributeCode) {
                if (!$product->hasData($attributeCode)) {
                    continue;
                }
                $productCustomEntityIds = $product->getData($attributeCode);
                if (is_string($productCustomEntityIds)) {
                    $productCustomEntityIds = explode(',', $productCustomEntityIds);
                }
                foreach ($productCustomEntityIds ?? [] as $productCustomEntityId) {
                    $productCustomEntityList[$product->getId()][$attributeCode][] = $productCustomEntityId;
                    $customEntityIds[$productCustomEntityId] = $productCustomEntityId;
                }
            }
        }

        if (empty($customEntityIds)) {
            return;
        }

        $customEntityList = $this->getCustomEntityByIds($customEntityIds);

        foreach (array_keys($productCustomEntityList) as $productId) {
            $product = $collection->getItemById($productId);
            $productCustomEntities = [];
            foreach ($productCustomEntityList[$product->getId()] as $attributeCode => $customEntityIds) {
                foreach ($customEntityIds as $customEntityId) {
                    $customEntity = $customEntityList[$customEntityId];
                    $customEntity->setProductAttributeCode($attributeCode);
                    $productCustomEntities[] = $customEntity;
                }
            }
            $attributeValues = $productCustomEntityList[$product->getId()];
            $product->addData($attributeValues);

            $entityExtension = $product->getExtensionAttributes();
            $entityExtension->setCustomEntities($productCustomEntities);
            $product->setExtensionAttributes($entityExtension);
        }
    }

    /**
     * Return an array of custom entity matching the given ids, index by the ids.
     */
    private function getCustomEntityByIds(array $customEntityIds): array
    {
        $customEntityList = [];
        $customEntityCollection = $this->customEntityCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => $customEntityIds]);

        foreach ($customEntityCollection->getItems() as $customEntity) {
            $customEntityList[$customEntity->getId()] = $customEntity;
        }

        return $customEntityList;
    }

    /**
     * Get custom entity product attribute code.
     *
     * @return string[]
     */
    private function getCustomEntityProductAttribute(): array
    {
        return array_keys(array_filter(
            $this->catalogConfig->getAttributesUsedInProductListing(),
            function (AbstractAttribute $attribute) {
                return $attribute->getFrontendInput() == 'smile_custom_entity';
            }
        ));
    }
}

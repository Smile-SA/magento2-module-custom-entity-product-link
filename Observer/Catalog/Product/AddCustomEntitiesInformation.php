<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as CustomEntityCollectionFactory;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;

/**
 * Add custom entities information on product collection.
 */
class AddCustomEntitiesInformation implements ObserverInterface
{
    private CustomEntityProductLinkManagementInterface $customEntityProductLinkManagement;
    private Config $catalogConfig;
    private CustomEntityCollectionFactory $customEntityCollectionFactory;

    /**
     * AddCustomEntitiesInformation constructor.
     *
     * @param CustomEntityProductLinkManagementInterface $customEntityProductLinkManagement Custom entity product link.
     * @param Config $catalogConfig Catalog config.
     */
    public function __construct(
        CustomEntityProductLinkManagementInterface $customEntityProductLinkManagement,
        Config $catalogConfig,
        CustomEntityCollectionFactory $customEntityCollectionFactory
    ) {
        $this->customEntityProductLinkManagement = $customEntityProductLinkManagement;
        $this->catalogConfig = $catalogConfig;
        $this->customEntityCollectionFactory = $customEntityCollectionFactory;
    }

    /**
     * Add custom entities information on product collection.
     *
     * @param Observer $observer Observer.
     */
    public function execute(Observer $observer): void
    {
        /** @var Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $attributeCodes = array_keys(array_filter(
            $this->catalogConfig->getAttributesUsedInProductListing(),
            function (AbstractAttribute $attribute) {
                return $attribute->getFrontendInput() == 'smile_custom_entity';
            }
        ));

        if (!empty($attributeCodes)) {
            $customEntityIds = [];
            $productCustomEntityList = [];
            foreach ($collection as $product) {
                foreach ($attributeCodes as $attributeCode) {
                    if (!$product->hasData($attributeCode)) {
                        continue;
                    }
                    $productCustomEntityIds = explode(',', $product->getData($attributeCode));
                    foreach ($productCustomEntityIds as $productCustomEntityId) {
                        $productCustomEntityList[$product->getId()][$attributeCode][] = $productCustomEntityId;
                    }
                    $customEntityIds = array_merge($customEntityIds, $productCustomEntityIds);
                }
            }
            $customEntityIds = array_unique($customEntityIds);

            if (!empty($customEntityIds)) {
                $customEntityList = [];
                $customEntityCollection = $this->customEntityCollectionFactory->create()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in' => $customEntityIds]);

                foreach ($customEntityCollection->getItems() as $customEntity) {
                    $customEntityList[$customEntity->getId()] = $customEntity;
                }

                foreach ($collection as $product) {
                    if (array_key_exists($product->getId(), $productCustomEntityList)) {
                        $attributeValues = [];
                        $productCustomEntities = [];
                        foreach ($productCustomEntityList[$product->getId()] as $attributeCode => $customEntityIds) {
                            foreach ($customEntityIds as $customEntityId) {
                                $customEntity = $customEntityList[$customEntityId];
                                $customEntity->setProductAttributeCode($attributeCode);
                                $attributeValues[$attributeCode][] = $customEntity->getId();
                                $productCustomEntities[] = $customEntity;
                            }
                        }
                        $product->addData($attributeValues);

                        $entityExtension = $product->getExtensionAttributes();
                        $entityExtension->setCustomEntities($productCustomEntities);
                        $product->setExtensionAttributes($entityExtension);
                    }
                }
            }
        }
    }
}

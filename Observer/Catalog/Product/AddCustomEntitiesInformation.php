<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface as CustomEntityProductLinkManagementInterfaceAlias;

/**
 * Add custom entities information on product collection.
 */
class AddCustomEntitiesInformation implements ObserverInterface
{
    private CustomEntityProductLinkManagementInterfaceAlias $customEntityProductLinkManagement;

    private Config $catalogConfig;

    /**
     * AddCustomEntitiesInformation constructor.
     *
     * @param CustomEntityProductLinkManagementInterfaceAlias $customEntityProductLinkManagement Custom entity product link management.
     * @param Config $catalogConfig Catalog config.
     */
    public function __construct(
        CustomEntityProductLinkManagementInterfaceAlias $customEntityProductLinkManagement,
        Config                                          $catalogConfig
    ) {
        $this->customEntityProductLinkManagement = $customEntityProductLinkManagement;
        $this->catalogConfig = $catalogConfig;
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

        $customEntitiesByCode = $this->customEntityProductLinkManagement->getCustomEntitiesByProductIds(
            $collection->getLoadedIds(),
            $attributeCodes
        );
        foreach ($collection as $product) {
            if (!array_key_exists($product->getId(), $customEntitiesByCode)) {
                continue;
            }
            $attributeValues = [];
            $productCustomEntities = [];
            // @todo refactoring this (duplicate from readHandler)
            foreach ($customEntitiesByCode[$product->getId()] as $attributeCode => $customEntities) {
                foreach ($customEntities as $customEntity) {
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

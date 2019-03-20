<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\CustomEntityProductLink
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;

/**
 * Add custom entities information on product collection.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class AddCustomEntitiesInformation implements ObserverInterface
{
    /**
     * @var CustomEntityProductLinkManagementInterface
     */
    private $customEntityProductLinkManagement;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    private $catalogConfig;

    /**
     * AddCustomEntitiesInformation constructor.
     *
     * @param CustomEntityProductLinkManagementInterface $customEntityProductLinkManagement Custom entity product link management.
     * @param \Magento\Catalog\Model\Config              $catalogConfig                     Catalog config.
     */
    public function __construct(
        CustomEntityProductLinkManagementInterface $customEntityProductLinkManagement,
        \Magento\Catalog\Model\Config $catalogConfig
    ) {
        $this->customEntityProductLinkManagement = $customEntityProductLinkManagement;
        $this->catalogConfig = $catalogConfig;
    }

    /**
     * Add custom entities information on product collection.
     *
     * @param Observer $observer Observer.
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $observer->getEvent()->getData('collection');
        $attributeCodes = array_keys(array_filter(
            $this->catalogConfig->getAttributesUsedInProductListing(),
            function (\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute) {
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

<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\CustomEntityProductLink
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\CustomEntityProductLink\Model\Product\CustomEntity;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;

/**
 * Custom entity product link read handler.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var CustomEntityProductLinkManagementInterface
     */
    private $customEntityLinkManager;

    /**
     * Constructor.
     *
     * @param CustomEntityProductLinkManagementInterface $customEntityLinkManager Custom entities link manager.
     */
    public function __construct(
        CustomEntityProductLinkManagementInterface $customEntityLinkManager
    ) {
        $this->customEntityLinkManager = $customEntityLinkManager;
    }

    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $customEntitiesByCode = $this->customEntityLinkManager->getCustomEntities($entity);
            $attributeValues = [];
            $productCustomEntities = [];

            foreach ($customEntitiesByCode as $attributeCode => $customEntities) {
                foreach ($customEntities as $customEntity) {
                    $customEntity->setProductAttributeCode($attributeCode);
                    $attributeValues[$attributeCode][] = $customEntity->getId();
                    $productCustomEntities[] = $customEntity;
                }
            }

            $entity->addData($attributeValues);

            $entityExtension = $entity->getExtensionAttributes();
            $entityExtension->setCustomEntities($productCustomEntities);
            $entity->setExtensionAttributes($entityExtension);
        }

        return $entity;
    }
}

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
 * Custom entity product link save handler.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class SaveHandler implements ExtensionInterface
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
            $this->customEntityLinkManager->saveCustomEntities($entity);
        }

        return $entity;
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Product\CustomEntity;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;

/**
 * Custom entity product link save handler.
 */
class SaveHandler implements ExtensionInterface
{
    private CustomEntityProductLinkManagementInterface $customEntityLinkManager;

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
     * @inheritDoc
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

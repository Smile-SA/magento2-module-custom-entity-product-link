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

namespace Smile\CustomEntityProductLink\Model;

use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Smile\CustomEntity\Api\CustomEntityRepositoryInterface;

/**
 * Custom entity product link management implementation.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class CustomEntityProductLinkManagement implements CustomEntityProductLinkManagementInterface
{
    /**
     * @var ResourceModel\CustomEntityProductLinkManagement
     */
    private $resourceModel;

    /**
     * @var CustomEntityRepositoryInterface
     */
    private $customEntityRepository;

    /**
     * @var \Smile\CustomEntityProductLink\Helper\Data
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param ResourceModel\CustomEntityProductLinkManagement         $resourceModel          Resource model.
     * @param \Smile\CustomEntity\Api\CustomEntityRepositoryInterface $customEntityRepository Custom entity repository.
     * @param \Smile\CustomEntityProductLink\Helper\Data              $helper                 Custom entity helper.
     */
    public function __construct(
        ResourceModel\CustomEntityProductLinkManagement $resourceModel,
        \Smile\CustomEntity\Api\CustomEntityRepositoryInterface $customEntityRepository,
        \Smile\CustomEntityProductLink\Helper\Data $helper
    ) {
        $this->resourceModel          = $resourceModel;
        $this->customEntityRepository = $customEntityRepository;
        $this->helper                 = $helper;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomEntities(ProductInterface $product)
    {
        $entities = [];

        foreach ($this->resourceModel->loadCustomEntityData($product->getId()) as $linkData) {
            $customEntity = $this->customEntityRepository->get($linkData['custom_entity_id'], $product->getStoreId());
            $entities[$linkData['attribute_code']][] = $customEntity;
        }

        return $entities;
    }

    /**
     * {@inheritDoc}
     */
    public function saveCustomEntities(ProductInterface $product)
    {
        foreach ($this->helper->getCustomEntityProductAttributes() as $attribute) {
            $entityIds = $product->getData($attribute->getAttributeCode());

            if (!$entityIds) {
                $entityIds = [];
            }

            $this->resourceModel->saveLinks($product->getId(), $attribute->getId(), $entityIds);
        }

        return $product;
    }
}

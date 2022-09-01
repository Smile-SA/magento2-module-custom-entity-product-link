<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Smile\CustomEntity\Api\CustomEntityRepositoryInterface;

/**
 * Custom entity product link management implementation.
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
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * Constructor.
     *
     * @param ResourceModel\CustomEntityProductLinkManagement         $resourceModel                Resource model.
     * @param \Smile\CustomEntity\Api\CustomEntityRepositoryInterface $customEntityRepository       Custom entity repository.
     * @param \Smile\CustomEntityProductLink\Helper\Data              $helper                       Custom entity helper.
     * @param SearchCriteriaBuilderFactory                            $searchCriteriaBuilderFactory Search critieria builder.
     */
    public function __construct(
        ResourceModel\CustomEntityProductLinkManagement $resourceModel,
        \Smile\CustomEntity\Api\CustomEntityRepositoryInterface $customEntityRepository,
        \Smile\CustomEntityProductLink\Helper\Data $helper,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->resourceModel          = $resourceModel;
        $this->customEntityRepository = $customEntityRepository;
        $this->helper                 = $helper;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomEntities(ProductInterface $product)
    {
        $entities = [];

        foreach ($this->resourceModel->loadCustomEntityData($product->getId()) as $linkData) {
            // @todo use collection
            $customEntity = $this->customEntityRepository->get($linkData['custom_entity_id'], $product->getStoreId());
            $entities[$linkData['attribute_code']][] = $customEntity;
        }

        return $entities;
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomEntitiesByProductIds(array $productIds, array $attributeCodes = [])
    {
        $linksData = $this->resourceModel->loadCustomEntityDataByProductIds($productIds, $attributeCodes);
        $customEntityIds = array_map(function (array $linkData) {
            return $linkData['custom_entity_id'];
        }, $linksData);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(
            'entity_id',
            $customEntityIds,
            'in'
        );
        $customEntities = $this->customEntityRepository->getList($searchCriteriaBuilder->create());
        $customEntitiesByCode = [];
        foreach ($linksData as $linkData) {
            $customEntity = $customEntities->getItems()[$linkData['custom_entity_id']] ?? null;
            if (null === $customEntity) {
                continue;
            }
            $customEntitiesByCode[$linkData['product_id']][$linkData['attribute_code']][] = $customEntity;
        }

        return $customEntitiesByCode;
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

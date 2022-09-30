<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\AbstractModel\Stub;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Smile\CustomEntity\Api\CustomEntityRepositoryInterface;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntityProductLink\Api\CustomEntityProductLinkManagementInterface;
use Smile\CustomEntityProductLink\Helper\Data;

/**
 * Custom entity product link management implementation.
 */
class CustomEntityProductLinkManagement implements CustomEntityProductLinkManagementInterface
{
    private ResourceModel\CustomEntityProductLinkManagement $resourceModel;

    private CustomEntityRepositoryInterface $customEntityRepository;

    private Data $helper;

    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    /**
     * Constructor.
     *
     * @param ResourceModel\CustomEntityProductLinkManagement $resourceModel Resource model.
     * @param CustomEntityRepositoryInterface $customEntityRepository Custom entity repository.
     * @param Data $helper Custom entity helper.
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory Search critieria builder.
     */
    public function __construct(
        ResourceModel\CustomEntityProductLinkManagement $resourceModel,
        CustomEntityRepositoryInterface $customEntityRepository,
        Data $helper,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->customEntityRepository = $customEntityRepository;
        $this->helper = $helper;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * Return custom entities assigned to a product.
     *
     * @param ProductInterface $product Product.
     * @return CustomEntityInterface[]|null
     */
    public function getCustomEntities(ProductInterface $product): ?array
    {
        /** @var CustomEntityInterface[] $entities */
        $entities = [];

        foreach ($this->resourceModel->loadCustomEntityData($product->getId()) as $linkData) {
            // @todo use collection
            /** @var Stub $product */
            $customEntity = $this->customEntityRepository->get($linkData['custom_entity_id'], $product->getStoreId());
            $entities[$linkData['attribute_code']][] = $customEntity;
        }

        return $entities;
    }

    /**
     * Return custom entities assigned to all product ids.
     *
     * @param array $productIds     Product ids.
     * @param array $attributeCodes Attribute codes filter.
     * @return mixed
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
     * Persists custom entities product links.
     *
     * @param ProductInterface $product Product.
     */
    public function saveCustomEntities(ProductInterface $product): ?ProductInterface
    {
        foreach ($this->helper->getCustomEntityProductAttributes() as $attribute) {
            /** @var DataObject $product */
            $entityIds = $product->getData($attribute->getAttributeCode());

            if (!$entityIds) {
                $entityIds = [];
            }

            /** @var AbstractModel $attribute */
            $this->resourceModel->saveLinks($product->getId(), $attribute->getId(), $entityIds);
        }

        return $product;
    }
}

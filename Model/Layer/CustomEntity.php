<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Layer;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\ContextInterface;
use Magento\Catalog\Model\Layer\StateFactory;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Custom entity view layer model.
 */
class CustomEntity extends Layer
{
    /**
     * Constructor.
     *
     * @param ContextInterface $context Context.
     * @param StateFactory $layerStateFactory Layer state factory.
     * @param AttributeCollectionFactory $attributeCollectionFactory Attribute collection factory.
     * @param Product $catalogProduct Catalog product resource model.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param Registry $registry Registry.
     * @param CategoryRepositoryInterface $categoryRepository Category repository.
     * @param array $data Data.
     */
    public function __construct(
        ContextInterface $context,
        StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        Product $catalogProduct,
        StoreManagerInterface $storeManager,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Layer;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;

/**
 * Custom entity view layer model.
 */
class CustomEntity extends Layer
{
    /**
     * Constructor.
     *
     * @param Layer\ContextInterface                       $context                    Context.
     * @param Layer\StateFactory                           $layerStateFactory          Layer state factory.
     * @param AttributeCollectionFactory                   $attributeCollectionFactory Attribute collection factory.
     * @param \Magento\Catalog\Model\ResourceModel\Product $catalogProduct             Catalog product resource model.
     * @param \Magento\Store\Model\StoreManagerInterface   $storeManager               Store manager.
     * @param \Magento\Framework\Registry                  $registry                   Registry.
     * @param CategoryRepositoryInterface                  $categoryRepository         Category repository.
     * @param array                                        $data                       Data.
     */
    public function __construct(
        Layer\ContextInterface $context,
        Layer\StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
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

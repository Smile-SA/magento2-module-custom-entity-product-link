<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Layer\CustomEntity;

use Magento\Catalog\Model\Layer\Category\CollectionFilter as BaseCollectionFilter;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Framework\Registry;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntityProductLink\Helper\Product as ProductHelper;
use Smile\ElasticsuiteCore\Helper\Mapping;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * Custom entity view layer collection filter model.
 */
class CollectionFilter extends BaseCollectionFilter implements CollectionFilterInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * @var \Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory
     */
    private $queryFactory;

    /**
     * CollectionFilter constructor.
     *
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility Product visibility model.
     * @param \Magento\Catalog\Model\Config             $catalogConfig     Catalog config.
     * @param ProductHelper                             $productHelper     Product helper.
     * @param Registry                                  $registry          Registry.
     * @param Mapping                                   $mappingHelper     Mapping helper.
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        ProductHelper $productHelper,
        Registry $registry,
        QueryFactory $queryFactory
    ) {
        parent::__construct($productVisibility, $catalogConfig);
        $this->productHelper = $productHelper;
        $this->registry = $registry;
        $this->queryFactory  = $queryFactory;
    }

    /**
     * Filter product collection.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection Collection.
     * @param \Magento\Catalog\Model\Category                         $category   Category.
     *
     * @return void
     */
    public function filter($collection, \Magento\Catalog\Model\Category $category)
    {
        parent::filter($collection, $category);
        $currentCustomEntity = $this->getCurrentCustomEntity();
        if (null !== $currentCustomEntity && $currentCustomEntity->getId() && $this->getAttributeCode()) {
            $query = $this->queryFactory->create(
                QueryInterface::TYPE_TERM,
                ['field' => $this->getAttributeCode(), 'value' => $currentCustomEntity->getId()]
            );

            $collection->addQueryFilter($query);
        }
    }

    /**
     * Return current custom entity interface.
     *
     * @return CustomEntityInterface|null
     */
    private function getCurrentCustomEntity()
    {
        return $this->registry->registry('current_custom_entity');
    }

    /**
     * Return attribute code link to current custom entity.
     *
     * @return string
     */
    private function getAttributeCode()
    {
        return $this->productHelper->getFilterableAttributeCode($this->getCurrentCustomEntity());
    }
}

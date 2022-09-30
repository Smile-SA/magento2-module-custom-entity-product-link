<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Layer\CustomEntity;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer\Category\CollectionFilter as BaseCollectionFilter;
use Magento\Catalog\Model\Layer\CollectionFilterInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Registry;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntityProductLink\Helper\Product as ProductHelper;
use Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection;
use Smile\ElasticsuiteCore\Search\Request\Query\QueryFactory;
use Smile\ElasticsuiteCore\Search\Request\QueryInterface;

/**
 * Custom entity view layer collection filter model.
 */
class CollectionFilter extends BaseCollectionFilter implements CollectionFilterInterface
{
    private Registry $registry;

    private ProductHelper $productHelper;

    private QueryFactory $queryFactory;

    /**
     * CollectionFilter constructor.
     *
     * @param Visibility $productVisibility Product visibility model.
     * @param Config $catalogConfig Catalog config.
     * @param ProductHelper $productHelper Product helper.
     * @param Registry $registry Registry.
     * @param QueryFactory $queryFactory Query factory.
     */
    public function __construct(
        Visibility $productVisibility,
        Config $catalogConfig,
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
     * @inheritdoc
     */
    public function filter($collection, Category $category): void
    {
        parent::filter($collection, $category);
        $currentCustomEntity = $this->getCurrentCustomEntity();
        if (null !== $currentCustomEntity && $currentCustomEntity->getId() && $this->getAttributeCode()) {
            $query = $this->queryFactory->create(
                QueryInterface::TYPE_TERM,
                ['field' => $this->getAttributeCode(), 'value' => $currentCustomEntity->getId()]
            );

            /** @var Collection $collection */
            $collection->addQueryFilter($query);
        }
    }

    /**
     * Return current custom entity interface.
     */
    private function getCurrentCustomEntity(): ?CustomEntityInterface
    {
        return $this->registry->registry('current_custom_entity');
    }

    /**
     * Return attribute code link to current custom entity.
     */
    private function getAttributeCode(): ?string
    {
        return $this->productHelper->getFilterableAttributeCode($this->getCurrentCustomEntity());
    }
}

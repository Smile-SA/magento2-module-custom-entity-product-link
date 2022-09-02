<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\ResourceModel\Search;

use Magento\CatalogSearch\Model\ResourceModel\Search\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

/**
 * Extends Magento CatalogSearch Collection
 */
class CustomCollection extends Collection
{

    /**
     * This method has been rewritten with the purpose of removing all smile custom entities from the attribute
     * collection. This enables the product grid search to create the correct sql request in the method
     * _getSearchEntityIdsSql
     *
     * @return AbstractDb
     */
    protected function _getAttributesCollection(): AbstractDb
    {
        if (!$this->_attributesCollection) {
            $this->_attributesCollection = $this->_attributeCollectionFactory
                ->create()
                ->addSearchableAttributeFilter()
                ->addFieldToFilter('frontend_input', ['neq' => 'smile_custom_entity'])
                ->load();

            foreach ($this->_attributesCollection as $attribute) {
                $attribute->setEntity($this->getEntity());
            }
        }
        return $this->_attributesCollection;
    }

    /**
     * We have extend this method in order to add the smile custom entities sql request and therefore this makes the
     * search on the smile_custom_entity_varchar work correctly
     *
     * @param mixed $query                    Query
     * @param bool  $searchOnlyInCurrentStore Search only in current store or in all stores
     *
     * @return string|null
     */
    protected function _getSearchEntityIdsSql($query, $searchOnlyInCurrentStore = true): ?string
    {
        $sql = parent::_getSearchEntityIdsSql($query, $searchOnlyInCurrentStore);

        $selects = $this->_getSmileCustomSql($query);
        $sql = $sql->union($selects, \Magento\Framework\DB\Select::SQL_UNION_ALL);

        return $sql;
    }

    /**
     * This method has the purpose of creating an sql request correctly linking the catalog_product_custom_entity_link
     * table with smile_custom_entity_varchar and catalog_product_entity tables
     *
     * @param mixed $query Query
     *
     * @return array|null
     * @throws LocalizedException
     */
    protected function _getSmileCustomSql($query): ?array
    {
        $smileCustomCollection = $this->_attributeCollectionFactory
            ->create()
            ->addSearchableAttributeFilter()
            ->addFieldToFilter('frontend_input', ['eq' => 'smile_custom_entity'])
            ->load();

        $smileCustomEntityAttributeIds = [];
        foreach ($smileCustomCollection as $attribute) {
            $smileCustomEntityAttributeIds[] = $attribute->getId();
        }

        if ($smileCustomEntityAttributeIds) {
            $selects[] = $this->getConnection()->select()->from(
                ['cpe' => 'catalog_product_entity'],
                $this->getEntity()->getLinkField()
            )->joinInner(
                ['cpcel' => 'catalog_product_custom_entity_link'],
                'cpcel.product_id = cpe.entity_id',
                []
            )->joinLeft(
                ['scev' =>'smile_custom_entity_varchar'],
                'cpcel.custom_entity_id = scev.entity_id',
                []
            )->where(
                'cpcel.attribute_id IN (?)',
                $smileCustomEntityAttributeIds
            )->where(
                'scev.value LIKE ?',
                '%' . $this->_searchQuery . '%'
            );
        }

        return $selects;
    }
}

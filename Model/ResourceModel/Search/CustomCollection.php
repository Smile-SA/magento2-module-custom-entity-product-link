<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\ResourceModel\Search;

use Magento\CatalogSearch\Model\ResourceModel\Search\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;

/**
 * Extends Magento CatalogSearch Collection
 */
class CustomCollection extends Collection
{
    /**
     * @inheritdoc
     */
    protected function _getAttributesCollection()
    {
        /** @var AbstractDb $attributesCollection */
        $attributesCollection = $this->_attributesCollection;

        if (!$this->_attributesCollection) {
            /** @var array $attributesCollection */
            $attributesCollection = $this->_attributeCollectionFactory
                ->create()
                ->addSearchableAttributeFilter()
                ->addFieldToFilter('frontend_input', ['neq' => 'smile_custom_entity'])
                ->load();

            $this->_attributesCollection = $attributesCollection;

            foreach ($this->_attributesCollection as $attribute) {
                $attribute->setEntity($this->getEntity());
            }
        }

        return $attributesCollection;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD)
     */
    protected function _getSearchEntityIdsSql($query, $searchOnlyInCurrentStore = true)
    {
        /** @var Select $sql */
        $sql = parent::_getSearchEntityIdsSql($query, $searchOnlyInCurrentStore);

        $selects = $this->_getSmileCustomSql($query);
        $sql = $sql->union($selects, Select::SQL_UNION_ALL);

        return (string) $sql;
    }

    /**
     * Linking the catalog_product_custom_entity_link with smile_custom_entity_varchar and catalog_product_entity tables
     *
     * @param mixed $query Query
     * @return array|null
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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

        $selects = [];
        if ($smileCustomEntityAttributeIds) {
            $selects[] = $this->getConnection()->select()->from(
                ['cpe' => 'catalog_product_entity'],
                $this->getEntity()->getLinkField()
            )->joinInner(
                ['cpcel' => 'catalog_product_custom_entity_link'],
                'cpcel.product_id = cpe.entity_id',
                []
            )->joinLeft(
                ['scev' => 'smile_custom_entity_varchar'],
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

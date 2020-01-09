<?php
/**
 * Admin Catalog Custom Collection
 *
 * @category  Smile
 * @package   Smile\CustomEntityProductLink
 * @author    Maxime CORTYL <maxime.cortyl@smile.fr>
 * @copyright 2019 Smile
 */

namespace Smile\CustomEntityProductLink\Model\ResourceModel\Search;

use Magento\CatalogSearch\Model\ResourceModel\Search\Collection ;

/**
 * Rewrite Magento CatalogSearch Collection
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime CORTYL <maxime.cortyl@smile.fr>
 */
class CustomCollection extends Collection
{
    /**
     * @inheritDoc
     */
    protected function _getSearchEntityIdsSql($query, $searchOnlyInCurrentStore = true)
    {
        $tables = [];
        $selects = [];
        $smileCustomEntityAttributeIds = [];

        $likeOptions = ['position' => 'any'];

        $linkField = $this->getEntity()->getLinkField();

        /**
         * Collect tables and attribute ids of attributes with string values
         */
        foreach ($this->_getAttributesCollection() as $attribute) {
            /** @var \Magento\Catalog\Model\Entity\Attribute $attribute */
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_isAttributeTextAndSearchable($attribute)) {
                $table = $attribute->getBackendTable();
                if (!isset($tables[$table]) && $attribute->getBackendType() != 'static') {
                    $tables[$table] = [];
                }

                if ($attribute->getBackendType() == 'static') {
                    if ($attribute->getFrontendInput() == 'smile_custom_entity') {
                        $smileCustomEntityAttributeIds[] = $attribute->getId();
                        continue;
                    }
                    $selects[] = $this->getConnection()->select()->from(
                        $table,
                        $linkField
                    )->where(
                        $this->_resourceHelper->getCILike($attributeCode, $this->_searchQuery, $likeOptions)
                    );
                } else {
                    $tables[$table][] = $attribute->getId();
                }
            }
        }

        if ($searchOnlyInCurrentStore) {
            $joinCondition = $this->getConnection()->quoteInto(
                "t1.{$linkField} = t2.{$linkField} AND t1.attribute_id = t2.attribute_id AND t2.store_id = ?",
                $this->getStoreId()
            );
        } else {
            $joinCondition = "t1.{$linkField} = t2.{$linkField} AND t1.attribute_id = t2.attribute_id";
        }

        $ifValueId = $this->getConnection()->getIfNullSql('t2.value', 't1.value');
        foreach ($tables as $table => $attributeIds) {
            $selects[] = $this->getConnection()->select()->from(
                ['t1' => $table],
                $linkField
            )->joinLeft(
                ['t2' => $table],
                $joinCondition,
                []
            )->where(
                't1.attribute_id IN (?)',
                $attributeIds
            )->where(
                't1.store_id = ?',
                0
            )->where(
                $this->_resourceHelper->getCILike($ifValueId, $this->_searchQuery, $likeOptions)
            );
        }

        if ($smileCustomEntityAttributeIds) {
            $selects[] = $this->getConnection()->select()->from(
                ['cpe' => 'catalog_product_entity'],
                $linkField
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

        $sql = $this->_getSearchInOptionSql($query);
        if ($sql) {
            // phpcs:ignore Magento2.SQL.RawQuery
            $selects[] = "SELECT * FROM ({$sql}) AS inoptionsql"; // inherent unions may be inside
        }

        $sql = $this->getConnection()->select()->union($selects, \Magento\Framework\DB\Select::SQL_UNION_ALL);
        return $sql;
    }
}

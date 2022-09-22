<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Custom entity product link management resource model.
 */
class CustomEntityProductLinkManagement extends AbstractDb
{
    public const RELATION_TABLE_NAME = 'catalog_product_custom_entity_link';

    /**
     * Get custom entity data.
     *
     * @param int|string $productId Product id
     * @return array|null
     */
    public function loadCustomEntityData($productId): ?array
    {
        return $this->loadCustomEntityDataByProductIds([$productId]);
    }

    /**
     * Get custom entity data from product ids. Return an associative array with at first level product id.
     *
     * @param array $productIds Product Ids
     * @param array $attributeCodes Attribute codes filter.
     * @return array|null
     */
    public function loadCustomEntityDataByProductIds(array $productIds, array $attributeCodes = []): ?array
    {
        $select = $this->getConnection()->select()->from(['e' => $this->getTable(self::RELATION_TABLE_NAME)])
            ->join(['a' => $this->getTable('eav_attribute')], 'e.attribute_id = a.attribute_id', ['attribute_code'])
            ->where('product_id in (?)', $productIds);
        if (!empty($attributeCodes)) {
            $select->where('attribute_code in (?)', $attributeCodes);
        }

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Persist links.
     *
     * @param int|string $productId Product id.
     * @param int|string $attributeId Attribute id.
     * @param array $entityIds Linked entities ids.
     */
    public function saveLinks($productId, $attributeId, array $entityIds): CustomEntityProductLinkManagement
    {
        $table = $this->getTable(self::RELATION_TABLE_NAME);

        $deleteWhereConditions = [
            $this->getConnection()->quoteInto('product_id = ?', (int) $productId),
            $this->getConnection()->quoteInto('attribute_id = ?', (int) $attributeId),
        ];

        $this->getConnection()->delete($table, implode(' AND ', $deleteWhereConditions));

        $insertData = [];

        foreach ($entityIds as $entityId) {
            $insertData[] = [$productId, $attributeId, $entityId];
        }

        if (!empty($insertData)) {
            $this->getConnection()->insertArray(
                $table,
                ['product_id', 'attribute_id', 'custom_entity_id'],
                $insertData
            );
        }

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\ResourceModel\Product\Indexer\Fulltext\Datasource;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\Attribute\CollectionFactory;
use Smile\ElasticsuiteCatalog\Model\ResourceModel\Eav\Indexer\Fulltext\Datasource\AbstractAttributeData;
use Zend\Db\Sql\ExpressionFactory;

/**
 * Custom entity datasource resource model.
 */
class CustomEntity extends AbstractAttributeData
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ExpressionFactory
     */
    private $expressionFactory;

    /**
     * CustomEntity constructor.
     *
     * @param ResourceConnection $resource Resource connection.
     * @param StoreManagerInterface $storeManager Store manager.
     * @param MetadataPool $metadataPool Metadata pool.
     * @param CollectionFactory $collectionFactory Custom entity attribute collection factory.
     * @param ExpressionFactory $expressionFactory Zend db expression factory.
     * @param string|null $entityType Entity type.
     */
    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        CollectionFactory $collectionFactory,
        ExpressionFactory $expressionFactory,
        string $entityType = null
    ) {
        parent::__construct($resource, $storeManager, $metadataPool, $entityType);
        $this->collectionFactory = $collectionFactory;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * Return custom entity row data.
     *
     * @param int|string   $storeId      Store id.
     * @param array $entityIds    Entity ids.
     * @param array $attributeIds Attribute ids.
     *
     * @return array
     */
    public function loadCustomEntity($storeId, array $entityIds, array $attributeIds): array
    {
        $select = $this->getConnection()->select()
            ->from(
                ['custom_entity' => $this->getTable('catalog_product_custom_entity_link')],
                ['product_id', 'attribute_id', 'entity_id' => 'custom_entity_id']
            )
            ->where('custom_entity.product_id IN (?)', $entityIds)
            ->where('custom_entity.attribute_id IN (?)', $attributeIds);
        foreach ($this->getCustomEntityAttributes() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $joinStoreValuesConditionClauses = [
                't_default_'.$attributeCode.'.entity_id = t_store_'.$attributeCode.'.entity_id',
                't_default_'.$attributeCode.'.attribute_id = t_store_'.$attributeCode.'.attribute_id',
                't_store_'.$attributeCode.'.store_id= ?',
            ];
            $joinStoreValuesCondition = $this->connection->quoteInto(
                implode(' AND ', $joinStoreValuesConditionClauses),
                $storeId
            );
            $columnValueExpression = $this->expressionFactory->create(
                ['expression' => 'COALESCE(t_store_'.$attributeCode.'.value, t_default_'.$attributeCode.'.value)']
            );

            $select
                ->joinInner(
                    ['t_default_'.$attributeCode => $attribute->getBackendTable()],
                    'custom_entity.custom_entity_id = t_default_'.$attributeCode.'.entity_id',
                    []
                )
                ->joinLeft(['t_store_'.$attributeCode => $attribute->getBackendTable()], $joinStoreValuesCondition, [])
                ->where('t_default_'.$attributeCode.'.store_id=?', 0)
                ->where('t_default_'.$attributeCode.'.attribute_id=?', $attribute->getAttributeId())
                ->columns([$attributeCode => $columnValueExpression->getExpression()]);
        }

        // @codingStandardsIgnoreLine (MEQP1.Performance.InefficientMethods.FoundFetchAll)
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Return custom entity attributes for indexing.
     *
     * @return DataObject[]
     */
    private function getCustomEntityAttributes(): array
    {
        /** @var \Smile\CustomEntity\Model\ResourceModel\CustomEntity\Attribute\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('attribute_code', ['name']);

        return $collection->getItems();
    }
}

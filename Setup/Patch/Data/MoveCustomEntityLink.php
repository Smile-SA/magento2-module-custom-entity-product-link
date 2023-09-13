<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Setup\Patch\Data;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class MoveCustomEntityLink implements DataPatchInterface
{
    /**
     * Resource connection.
     */
    protected ResourceConnection $resourceConnection;

    /**
     * Constructor.
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->resourceConnection->getConnection();
        $customEntityLink = $connection->getTableName('catalog_product_custom_entity_link');

        if ($connection->isTableExists($customEntityLink)) {
            $catalogText = $connection->getTableName('catalog_product_entity_text');
            $fields = ['attribute_id', 'value', 'entity_id'];

            $select = $connection->select()
                ->from(
                    $customEntityLink,
                    [
                        'attribute_id' => 'attribute_id',
                        'value' => 'GROUP_CONCAT(custom_entity_id SEPARATOR ",")',
                        'entity_id' => 'product_id',
                    ]
                )
                ->group(['product_id', 'attribute_id']);

            if ($connection->tableColumnExists($catalogText, 'row_id')) {
                $fields = ['attribute_id', 'value', 'row_id'];
                $select = $connection->select()
                    ->from(
                        ['link' => $customEntityLink],
                        [
                            'attribute_id' => 'link.attribute_id',
                            'value' => 'GROUP_CONCAT(link.custom_entity_id SEPARATOR ",")',
                        ]
                    )
                    ->join(
                        ['product' => $connection->getTableName('catalog_product_entity')],
                        'product.entity_id = link.product_id',
                        ['row_id' => 'product.row_id']
                    )
                    ->group(['link.product_id', 'link.attribute_id']);
            }

            $connection->query(
                $connection->insertFromSelect($select, $catalogText, $fields, AdapterInterface::INSERT_IGNORE)
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}

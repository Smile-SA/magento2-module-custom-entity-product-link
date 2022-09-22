<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Smile\ScopedEav\Setup\SchemaSetupFactory;

/**
 * Custom entity product link schema setup.
 */
class InstallSchema implements InstallSchemaInterface
{
    private SchemaSetupFactory $schemaSetupFactory;

    /**
     * Constructor.
     *
     * @param SchemaSetupFactory $schemaSetupFactory Scoped EAV schema setup factory.
     */
    public function __construct(SchemaSetupFactory $schemaSetupFactory)
    {
        $this->schemaSetupFactory = $schemaSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection  = $setup->getConnection();

        // Create relation table between product and custom entities.
        $table = $this->getProductLinkTable($setup);
        $connection->createTable($table);

         $setup->endSetup();
    }

    /**
     * Create the relation table between entities and products.
     *
     * @param SchemaSetupInterface $setup Setup.
     */
    private function getProductLinkTable(SchemaSetupInterface $setup): ?Table
    {
        $entityTable = 'smile_custom_entity';
        $connection  = $setup->getConnection();

        $table = $connection->newTable($setup->getTable('catalog_product_custom_entity_link'))
            ->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )
            ->addColumn(
                'custom_entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addIndex(
                $setup->getIdxName('catalog_product_custom_entity_link', 'product_id'),
                'product_id'
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'custom_entity_id', $entityTable, 'entity_id'),
                'custom_entity_id',
                $setup->getTable($entityTable),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product custom entities relations');

        return $table;
    }
}

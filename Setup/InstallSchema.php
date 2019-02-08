<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\CustomEntityProductLink
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\CustomEntityProductLink\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Custom entity product link schema setup.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Smile\ScopedEav\Setup\SchemaSetupFactory
     */
    private $schemaSetupFactory;

    /**
     * Constructor.
     *
     * @param \Smile\ScopedEav\Setup\SchemaSetupFactory $schemaSetupFactory Scoped EAV schema setup factory.
     */
    public function __construct(\Smile\ScopedEav\Setup\SchemaSetupFactory $schemaSetupFactory)
    {
        $this->schemaSetupFactory = $schemaSetupFactory;
    }

    /**
     * {@inheritdoc}
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
     *
     * @return \Magento\Framework\DB\Ddl\Table
     */
    private function getProductLinkTable(SchemaSetupInterface $setup)
    {
        $entityTable = 'smile_custom_entity';
        $connection  = $setup->getConnection();

        $table = $connection->newTable($setup->getTable('catalog_product_custom_entity_link'))
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )
            ->addColumn(
                'custom_entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('catalog_product_custom_entity_link', 'custom_entity_id', $entityTable, 'entity_id'),
                'custom_entity_id',
                $setup->getTable($entityTable),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Product custom entities relations');

        return $table;
    }
}

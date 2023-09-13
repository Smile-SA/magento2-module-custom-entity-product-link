<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Smile\CustomEntityProductLink\Model\Entity\Attribute\Source\CustomEntity as Source;

/**
 * Custom entity data setup.
 */
class UpdateProductCustomEntityAttribute implements DataPatchInterface
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
        $connection  = $this->resourceConnection->getConnection();

        $connection->update(
            $connection->getTableName('eav_attribute'),
            ['backend_model' => ArrayBackend::class, 'backend_type' => 'text', 'source_model' => Source::class],
            ['frontend_input = ?' => 'smile_custom_entity']
        );

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

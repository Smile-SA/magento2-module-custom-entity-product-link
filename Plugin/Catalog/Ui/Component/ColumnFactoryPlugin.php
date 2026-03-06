<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Catalog\Ui\Component;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\Component\ColumnFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ColumnFactoryPlugin
{
    private const SMILE_CUSTOM_ENTITY_FRONTEND_INPUT_TYPE = 'smile_custom_entity';

    /**
     * @param array<string, int|string> $config
     * @return array{ProductAttributeInterface|ContextInterface|array<string, int|string>}
     */
    public function beforeCreate(
        ColumnFactory $subject,
        ProductAttributeInterface $attribute,
        ContextInterface $context,
        array $config = []
    ): array {
        if ($attribute->getFrontendInput() === self::SMILE_CUSTOM_ENTITY_FRONTEND_INPUT_TYPE) {
            $config['dataType'] = 'select';
            $config['component'] = 'Magento_Ui/js/grid/columns/select';
            if (isset($config['filter']) && $config['filter'] === 'text') {
                $config['filter'] = 'select';
            }
        }

        return [$attribute, $context, $config];
    }
}

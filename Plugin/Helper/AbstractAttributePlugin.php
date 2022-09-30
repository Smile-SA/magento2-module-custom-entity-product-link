<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Helper;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface;
use Smile\CustomEntity\Model\CustomEntity;
use Smile\ElasticsuiteCatalog\Helper\ProductAttribute;

/**
 * ElasticSuite product attributes helper plugin.
 */
class AbstractAttributePlugin
{
    /**
     * Returns field use for filtering for an smile_custom_entity attribute.
     *
     * @param ProductAttribute $source Product attribute helper.
     * @param string $field Field name.
     * @param AttributeInterface $attribute Product attribute.
     */
    public function afterGetFilterField(ProductAttribute $source, string $field, AttributeInterface $attribute): ?string
    {
        /** @var AbstractAttribute $attribute */
        if ($attribute->getFrontendInput() == CustomEntity::CACHE_TAG) {
            $field = $source->getOptionTextFieldName($field);
        }

        return $field;
    }
}

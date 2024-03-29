<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Model;

use Magento\Catalog\Model\Product;
use Smile\CustomEntity\Model\CustomEntity;
use Smile\CustomEntityProductLink\Helper\Data;

/**
 * Product model plugin.
 */
class ProductPlugin
{
    private Data $helper;

    /**
     * ProductPlugin constructor.
     *
     * @param Data $helper Helper.
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Append custom entity identities when update product attributes.
     *
     * @param Product $source Product model.
     * @param array $identities Identities.
     * @return array|null
     */
    public function afterGetIdentities(Product $source, array $identities): ?array
    {
        $customEntityProductAttributes = $this->helper->getCustomEntityProductAttributes();

        foreach ($customEntityProductAttributes as $customEntityProductAttribute) {
            $attributeCode = $customEntityProductAttribute->getAttributeCode();
            if (!$source->hasData($attributeCode) || !$source->dataHasChangedFor($attributeCode)) {
                continue;
            }
            $customEntityIds = $source->getData($attributeCode);
            if (is_string($customEntityIds)) {
                $customEntityIds = explode(',', $customEntityIds);
            }
            foreach ($customEntityIds ?? [] as $customEntityId) {
                $identities[] = CustomEntity::CACHE_TAG . '_' . $customEntityId;
            }
        }

        return array_unique($identities);
    }
}

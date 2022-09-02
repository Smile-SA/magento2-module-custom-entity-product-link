<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Block\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * List product block plugin.
 */
class ListProductPlugin
{
    /**
     * Append custom entities identities.
     *
     * @param ListProduct $source List product block.
     * @param array $identities Identities
     *
     * @return array|null
     */
    public function afterGetIdentities(ListProduct $source, array $identities): ?array
    {
        /** @var ProductInterface $product */
        foreach ($source->getLoadedProductCollection() as $product) {
            $customEntities = $product->getExtensionAttributes()->getCustomEntities() ?? [];
            /** @var CustomEntityInterface $customEntity */
            foreach ($customEntities as $customEntity) {
                $identities = array_merge($identities, $customEntity->getIdentities());
            }
        }

        return $identities;
    }
}

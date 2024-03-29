<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Block\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ListProduct;

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
     * @return array|null
     */
    public function afterGetIdentities(ListProduct $source, array $identities): ?array
    {
        /** @var ProductInterface $product */
        foreach ($source->getLoadedProductCollection() as $product) {
            // @phpstan-ignore-next-line
            $customEntities = $product->getExtensionAttributes()->getCustomEntities();
            $identities = [];
            if ($customEntities) {
                foreach ($customEntities as $customEntity) {
                    // @codingStandardsIgnoreLine
                    $identities = array_merge($identities, $customEntity->getIdentities());
                }
            }
        }

        return $identities;
    }
}

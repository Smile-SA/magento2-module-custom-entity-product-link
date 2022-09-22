<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Block\Product;

use Magento\Catalog\Block\Product\View;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * View product block plugin.
 */
class ViewPlugin
{
    /**
     * Append custom entities identities.
     *
     * @param View $source View product block.
     * @param array $identities Identities
     * @return array|null
     */
    public function afterGetIdentities(View $source, array $identities): ?array
    {
        // @todo Optimization: only custom entities if is visible on front
        $customEntities = $source->getProduct()->getExtensionAttributes()->getCustomEntities();
        if ($customEntities) {
            /** @var CustomEntityInterface $customEntity */
            foreach ($customEntities as $customEntity) {
                $identities = array_merge($identities, $customEntity->getIdentities());
            }
        }

        return $identities;
    }
}

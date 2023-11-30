<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Block\Product;

use Magento\Catalog\Block\Product\View;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntity\Model\CustomEntity;
use Magento\Framework\Serialize\Serializer\Json  as Serializer;

/**
 * View product block plugin.
 */
class ViewPlugin
{

    private Serializer $serializer;

    public function __construct(
        Serializer $serializer
    ) {
        $this->serializer = $serializer;
    }

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
        // @phpstan-ignore-next-line
        $customEntities = $source->getProduct()->getExtensionAttributes()->getCustomEntities();
        if ($customEntities) {
            /** @var CustomEntityInterface $customEntity */
            $customEntityIdentities = [];
            foreach ($customEntities as $customEntity) {
                // @codingStandardsIgnoreLine
                $customEntityIdentities[] = $customEntity->getIdentities();
            }

            $identities[] = sprintf(
                "%s_%s",
                CustomEntity::CACHE_TAG,
                sha1($this->serializer->serialize($customEntityIdentities))
            );
        }

        return $identities;
    }
}

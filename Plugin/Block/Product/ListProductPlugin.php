<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\CustomEntityProductLink
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\CustomEntityProductLink\Plugin\Block\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * List product block plugin.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class ListProductPlugin
{
    /**
     * Append custom entities identities.
     *
     * @param ListProduct $source     List product block.
     * @param array       $identities Identities
     *
     * @return array
     */
    public function afterGetIdentities(ListProduct $source, array $identities)
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

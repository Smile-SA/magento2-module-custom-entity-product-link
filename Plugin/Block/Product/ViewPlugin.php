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

use Magento\Catalog\Block\Product\View;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * View product block plugin.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class ViewPlugin
{
    /**
     * Append custom entities identities.
     *
     * @param View  $source     View product block.
     * @param array $identities Identities
     *
     * @return array
     */
    public function afterGetIdentities(View $source, array $identities)
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

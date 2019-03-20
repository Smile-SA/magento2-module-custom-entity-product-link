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
namespace Smile\CustomEntityProductLink\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * Custom entity product helper.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class Product extends AbstractHelper
{
    /**
     * Return custom entities for product and attribute code.
     *
     * @param ProductInterface $product       Product.
     * @param string           $attributeCode Attribuce code.
     *
     * @return CustomEntityInterface[]
     */
    public function getCustomEntities(ProductInterface $product, string $attributeCode): array
    {
        $result = [];
        $customEntities = $product->getExtensionAttributes()->getCustomEntities() ?? [];
        foreach ($customEntities as $customEntity) {
            if ($customEntity->getProductAttributeCode() !== $attributeCode) {
                continue;
            }
            $result[] = $customEntity;
        }

        return $result;
    }
}

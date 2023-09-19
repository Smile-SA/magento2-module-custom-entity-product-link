<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Api;

use Magento\Catalog\Model\Product;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * Custom entity product link management.
 *
 * @api
 */
interface CustomEntityProductLinkManagementInterface
{
    /**
     * Return custom entities assigned to a product.
     *
     * @param Product $product Product.
     * @return CustomEntityInterface[][]|null
     */
    public function getCustomEntities(Product $product): ?array;
}

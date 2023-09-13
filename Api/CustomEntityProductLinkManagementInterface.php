<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Api;

use Magento\Catalog\Api\Data\ProductInterface;
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
     * @param ProductInterface $product Product.
     * @return CustomEntityInterface[][]|null
     */
    public function getCustomEntities(ProductInterface $product): ?array;
}

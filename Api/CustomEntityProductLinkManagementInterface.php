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
     *
     * return CustomEntityInterface[]
     */
    public function getCustomEntities(ProductInterface $product);

    /**
     * Return custom entities assigned to all product ids.
     *
     * @param array $productIds     Product ids.
     * @param array $attributeCodes Attribute codes filter.
     *
     * @return mixed
     */
    public function getCustomEntitiesByProductIds(array $productIds, array $attributeCodes = []);

    /**
     * Persists custom entities product links.
     *
     * @param ProductInterface $product Product.
     *
     * @return ProductInterface
     */
    public function saveCustomEntities(ProductInterface $product);
}

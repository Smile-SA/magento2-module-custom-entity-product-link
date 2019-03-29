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
use Magento\Catalog\Model\Layer\Category\FilterableAttributeList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
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
     * @var array
     */
    private $filterableAttributeCodes = [];

    /**
     * @var FilterableAttributeList
     */
    private $filterableAttributeList;

    /**
     * Product constructor.
     *
     * @param Context                 $context                 Context.
     * @param FilterableAttributeList $filterableAttributeList Filterable attribute list.
     */
    public function __construct(
        Context $context,
        FilterableAttributeList $filterableAttributeList
    ) {
        parent::__construct($context);
        $this->filterableAttributeList = $filterableAttributeList;
    }

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
            if ($customEntity->getProductAttributeCode() !== $attributeCode || !$customEntity->getIsActive()) {
                continue;
            }
            $result[] = $customEntity;
        }

        return $result;
    }

    /**
     * Return filterable attribute code for a custom entity.
     *
     * @param CustomEntityInterface $customEntity Custom entity.
     *
     * @return string
     */
    public function getFilterableAttributeCode(CustomEntityInterface $customEntity)
    {
        if (!array_key_exists($customEntity->getId(), $this->filterableAttributeCodes)) {
            $this->filterableAttributeCodes[$customEntity->getId()] = '';
            foreach ($this->filterableAttributeList->getList() as $attribute) {
                if (
                    $attribute->getFrontendInput() == 'smile_custom_entity' &&
                    $attribute->getCustomEntityAttributeSetId() == $customEntity->getAttributeSetId()
                ) {
                    $this->filterableAttributeCodes[$customEntity->getId()] = $attribute->getAttributeCode();
                    break;
                }
            }
        }

        return $this->filterableAttributeCodes[$customEntity->getId()];
    }
}

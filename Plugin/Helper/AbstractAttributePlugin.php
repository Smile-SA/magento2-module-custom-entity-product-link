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
namespace Smile\CustomEntityProductLink\Plugin\Helper;

use Magento\Eav\Model\Entity\Attribute\AttributeInterface;
use Smile\ElasticsuiteCatalog\Helper\ProductAttribute;

/**
 * ElasticSuite product attributes helper plugin.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class AbstractAttributePlugin
{
    /**
     * Returns field use for filtering for an smile_custom_entity attribute.
     *
     * @param ProductAttribute   $source    Product attribute helper.
     * @param string             $field     Field name.
     * @param AttributeInterface $attribute Product attribute.
     *
     * @return string
     */
    public function afterGetFilterField(ProductAttribute $source, string $field, AttributeInterface $attribute)
    {
        if ($attribute->getFrontendInput() == 'smile_custom_entity') {
            $field = $source->getOptionTextFieldName($field);
        }

        return $field;
    }
}

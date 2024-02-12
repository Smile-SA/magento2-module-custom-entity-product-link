<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as EavModifier;
use Magento\Eav\Model\Attribute;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * EAV form modifier plugin used to manage custom entity form field.
 */
class EavPlugin
{
    private ArrayManager $arrayManager;

    /**
     * Constructor.
     *
     * @param ArrayManager $arrayManager Array manager util.
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Fix custom entity field meta.
     *
     * @param EavModifier $subject Object.
     * @param callable $proceed Original method.
     * @param ProductAttributeInterface $attribute Attribute.
     * @param string $groupCode Group code.
     * @param int $sortOrder Sort order.
     * @return array|null
     */
    public function aroundSetupAttributeMeta(
        EavModifier $subject,
        callable $proceed,
        ProductAttributeInterface $attribute,
        string $groupCode,
        int $sortOrder
    ): ?array {
        $meta = $proceed($attribute, $groupCode, $sortOrder);

        if ($attribute->getFrontendInput() == "smile_custom_entity") {
            $configPath = ltrim($subject::META_CONFIG_PATH, ArrayManager::DEFAULT_PATH_DELIMITER);

            /** @var Attribute $attribute */
            $fieldConfig = [
                'component'     => 'Magento_Ui/js/form/element/ui-select',
                'formElement'   => 'multiselect',
                'elementTmpl'   => 'ui/grid/filters/elements/ui-select',
                'filterOptions' => true,
                'multiple'      => true,
                'options'       => $attribute->getSource()->getAllOptions(),
                'disableLabel'  => true,
                'required'      => false,
            ];

            $meta = $this->arrayManager->merge($configPath, $meta, $fieldConfig);
        }

        return $meta;
    }
}

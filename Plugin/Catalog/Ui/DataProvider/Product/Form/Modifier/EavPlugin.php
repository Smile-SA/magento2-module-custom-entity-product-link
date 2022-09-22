<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav as EavModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Smile\CustomEntity\Model\ResourceModel\CustomEntity\CollectionFactory as EntityCollectionFactory;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * EAV form modifier plugin used to manage custom entity form field.
 */
class EavPlugin
{
    const COMPONENT_NAME = 'Magento_Ui/js/form/element/ui-select';

    const ELEMENT_TEMPLATE = 'ui/grid/filters/elements/ui-select';

    private ArrayManager $arrayManager;

    private EntityCollectionFactory $entityCollectionFactory;

    /**
     * Constructor.
     *
     * @param EntityCollectionFactory $entityCollectionFactory Custom entity collection factory.
     * @param ArrayManager $arrayManager Array manager util.
     */
    public function __construct(EntityCollectionFactory $entityCollectionFactory, ArrayManager $arrayManager)
    {
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Fix custom entity field meta.
     *
     * @param EavModifier               $subject   Object.
     * @param callable                  $proceed   Original method.
     * @param ProductAttributeInterface $attribute Attribute.
     * @param string                    $groupCode Group code.
     * @param int                       $sortOrder Sort order.
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

            $fieldConfig = [
                'component'     => self::COMPONENT_NAME,
                'options'       => $this->getOptions($attribute),
                'disableLabel'  => true,
                'elementTmpl'   => self::ELEMENT_TEMPLATE,
                'filterOptions' => true,
                'multiple'      => true,
                'required'      => false,
            ];

            $meta = $this->arrayManager->merge($configPath, $meta, $fieldConfig);
        }

        return $meta;
    }

    /**
     * List of custom entities.
     *
     * @param ProductAttributeInterface $attribute Attribute.
     * @return array|null
     */
    private function getOptions(ProductAttributeInterface $attribute): ?array
    {
        $attributeSetId = $attribute->getCustomEntityAttributeSetId();

        /**
         * @var \Smile\CustomEntity\Model\ResourceModel\CustomEntity\Collection $collection
         */
        $collection = $this->entityCollectionFactory->create();
        $collection->addAttributeToSelect(EntityInterface::NAME);
        $collection->addFieldToFilter(EntityInterface::ATTRIBUTE_SET_ID, $attributeSetId);
        $collection->setOrder(EntityInterface::NAME, $collection::SORT_ORDER_ASC);

        $items = [];

        foreach ($collection as $entity) {
            if ($entity->getName()) {
                $items[] = ['value' => $entity->getId(), 'label' => $entity->getName()];
            }
        }

        return $items;
    }
}

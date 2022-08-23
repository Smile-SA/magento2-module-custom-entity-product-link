<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Adminhtml;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Excluded smile custom entity attributes to form mass edit.
 */
class CatalogProductFormExcludedAttributesObserver implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    private $attributeAction;

    /**
     * CatalogProductFormExcludedAttributesObserver constructor.
     *
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction Attribute action helper.
     */
    public function __construct(\Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction)
    {
        $this->attributeAction = $attributeAction;
    }

    /**
     * Excluded smile custom entity attributes.
     *
     * @param Observer $observer Observer.
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Attributes $attributesTab */
        $attributesTab = $observer->getEvent()->getData('object');
        $attributesTab->setFormExcludedFieldList(
            array_merge($attributesTab->getFormExcludedFieldList(), $this->getCustomEntityAttributeCodes())
        );
    }

    /**
     * Return attribute codes.
     *
     * @return array
     */
    private function getCustomEntityAttributeCodes()
    {
        return array_map(function ($attribute) {
            return $attribute->getAttributeCode();
        }, $this->getCustomEntityAttributes());
    }

    /**
     * Return custom entity attributes.
     *
     * @return array|\Magento\Framework\DataObject[]
     */
    private function getCustomEntityAttributes()
    {
        $attributes = $this->attributeAction->getAttributes()->getItems();

        return array_filter($attributes, function ($attribute) {
            return $attribute->getFrontendInput() == 'smile_custom_entity';
        });
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Adminhtml;

use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntityProductLink\Model\Entity\Attribute\Frontend\CustomEntity;
use Smile\CustomEntityProductLink\Model\Entity\Attribute\Source\CustomEntity as Source;

/**
 * Product attribute before save observer.
 */
class CustomEntityAttributeSaveBeforeObserver implements ObserverInterface
{
    /**
     * Add frontend model for smile custom entity attribute.
     *
     * @param Observer $observer Observer.
     */
    public function execute(Observer $observer): void
    {
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $observer->getEvent()->getData('attribute');

        if ($attribute->getFrontendInput() == 'smile_custom_entity') {
            $attribute->setBackendType('text');
            $attribute->setFrontendModel(CustomEntity::class);
            $attribute->setBackendModel(ArrayBackend::class);
            $attribute->setSourceModel(Source::class);
        }
    }
}

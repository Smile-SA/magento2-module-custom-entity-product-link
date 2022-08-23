<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntityProductLink\Model\Entity\Attribute\Frontend\CustomEntity;

/**
 * Product attribute before save observer.
 */
class CustomEntityAttributeSaveBeforeObserver implements ObserverInterface
{
    /**
     * Add frontend model for smile custom entity attribute.
     *
     * @param Observer $observer Observer.
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        $attribute = $observer->getEvent()->getData('attribute');
        if ($attribute->getFrontendInput() == 'smile_custom_entity') {
            $attribute->setFrontendModel(CustomEntity::class);
        }
    }
}

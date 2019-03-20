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
namespace Smile\CustomEntityProductLink\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Smile\CustomEntityProductLink\Model\Entity\Attribute\Frontend\CustomEntity;

/**
 * Product attribute before save observer.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
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


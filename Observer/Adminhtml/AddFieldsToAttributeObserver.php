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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Smile\CustomEntity\Model\CustomEntity\AttributeSet;
use Magento\Framework\Module\Manager;

/**
 * Add custom_entity_attribute_set_id field into base fieldset.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class AddFieldsToAttributeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var AttributeSet\Options
     */
    private $attributeSetOptions;

    /**
     * Constructor.
     *
     * @param Manager              $moduleManager       Module manager.
     * @param AttributeSet\Options $attributeSetOptions Attribute set options.
     */
    public function __construct(Manager $moduleManager, AttributeSet\Options $attributeSetOptions)
    {
        $this->moduleManager = $moduleManager;
        $this->attributeSetOptions = $attributeSetOptions;
    }

    /**
     * Append custom_entity_attribute_set_id field.
     *
     * @param \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('Smile_CustomEntityProductLink')) {
            return;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            'custom_entity_attribute_set_id',
            'select',
            [
                'name'  => 'custom_entity_attribute_set_id',
                'label'  => __('Custom entity type'),
                'title'  => __('Custom entity type'),
                'values' => $this->attributeSetOptions->toOptionArray(),
            ]
        );
    }
}

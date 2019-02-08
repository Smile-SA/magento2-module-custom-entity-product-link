<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @category  Smile
* @package   Smile\CustomEntityProductLink
* @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
* @copyright 2019 Smile
* @license   Open Software License ("OSL") v. 3.0
*/

namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

/**
 * Add smile_custom_entity to the available input types.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class AddCustomEntityAttributeTypeObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Module\Manager $moduleManager Magento module manager.
     */
    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('Smile_CustomEntityProductLink')) {
            return;
        }

        /** @var \Magento\Framework\DataObject $response */
        $response = $observer->getEvent()->getResponse();
        $types = $response->getTypes();

        $types[] = [
            'value' => 'smile_custom_entity',
            'label' => __('Custom Entity'),
            'hide_fields' => [
                'is_unique',
                'is_required',
                'frontend_class',
                '_default_value',
            ],
        ];

        $response->setTypes($types);
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Observer\Catalog\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Module\Manager;

/**
 * Add smile_custom_entity to the available input types.
 */
class AddCustomEntityAttributeTypeObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * Constructor.
     *
     * @param Manager $moduleManager Magento module manager.
     */
    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Add custom entities attribute type observer.
     *
     * @param Observer $observer Observer.
     *
     * @return void
     */
    public function execute(Observer $observer): void
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

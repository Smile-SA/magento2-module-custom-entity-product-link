<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Controller\Entity;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Smile\CustomEntity\Controller\Entity\View;
use Smile\CustomEntityProductLink\Helper\Product as ProductHelper;

/**
 * Custom entity view controller plugin.
 */
class ViewPlugin
{
    private Resolver $layerResolver;

    private Registry $registry;

    private ProductHelper $productHelper;

    /**
     * ViewPlugin constructor.
     *
     * @param Resolver $layerResolver Layer resolver.
     * @param Registry $registry Registry.
     * @param ProductHelper $productHelper Product link helper.
     */
    public function __construct(
        Resolver $layerResolver,
        Registry $registry,
        ProductHelper $productHelper
    ) {
        $this->layerResolver = $layerResolver;
        $this->registry = $registry;
        $this->productHelper = $productHelper;
    }

    /**
     * Create an layer context and add body class.
     *
     * @param View $subject Custom entity view controller.
     * @param callable $proceed Callable method.
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function aroundExecute(View $subject, callable $proceed)
    {
        $this->layerResolver->create('smile_custom_entity');
        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $proceed();
        if ($this->hasFilterableAttribute()) {
            $page->addPageLayoutHandles(['product' => 'list']);
            $page->getConfig()->addBodyClass('page-products');
        }

        return $page;
    }

    /**
     * Return true if has filterable attribute for current custom entity.
     */
    private function hasFilterableAttribute(): bool
    {
        return $this->productHelper->getFilterableAttributeCode(
            $this->registry->registry('current_custom_entity')
        ) !== '';
    }
}

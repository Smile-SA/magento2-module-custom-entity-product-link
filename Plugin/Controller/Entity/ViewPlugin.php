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
namespace Smile\CustomEntityProductLink\Plugin\Controller\Entity;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Registry;
use Smile\CustomEntityProductLink\Helper\Product as ProductHelper;

/**
 * Custom entity view controller plugin.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 */
class ViewPlugin
{
    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Product
     */
    private $productHelper;

    /**
     * ViewPlugin constructor.
     *
     * @param Resolver $layerResolver Layer resolver.
     * @param Registry $registry      Registry.
     * @param Product  $productHelper Product link helper.
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
     * @param \Smile\CustomEntity\Controller\Entity\View $subject Custom entity view controller.
     * @param callable                                   $proceed Callable method.
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function aroundExecute(\Smile\CustomEntity\Controller\Entity\View $subject, callable $proceed)
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
     *
     * @return bool
     */
    private function hasFilterableAttribute()
    {
        return $this->productHelper->getFilterableAttributeCode(
            $this->registry->registry('current_custom_entity')
        ) !== '';
    }
}

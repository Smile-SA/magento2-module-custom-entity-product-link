<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Entity\Attribute\Frontend;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
use Magento\Eav\Model\Entity\Attribute\Source\BooleanFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Store\Model\StoreManagerInterface;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;
use Smile\CustomEntityProductLink\Helper\Product as ProductHelper;

/**
 * Custom entity frontend model attribute.
 */
class CustomEntity extends AbstractFrontend
{
    /**
     * @var array
     */
    private $renderers;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * CustomEntity constructor.
     *
     * @param BooleanFactory             $attrBooleanFactory Attribute boolean factory.
     * @param ProductHelper              $productHelper      Product helper.
     * @param array                      $renderers          Renderers.
     * @param CacheInterface|null        $cache              Cache.
     * @param null                       $storeResolver      Store resolver.
     * @param array|null                 $cacheTags          Cache tags.
     * @param StoreManagerInterface|null $storeManager       Store manager.
     * @param Serializer|null            $serializer         Serializer.
     */
    public function __construct(
        BooleanFactory $attrBooleanFactory,
        ProductHelper $productHelper,
        array $renderers = [],
        CacheInterface $cache = null,
        $storeResolver = null,
        array $cacheTags = null,
        StoreManagerInterface $storeManager = null,
        Serializer $serializer = null
    ) {
        parent::__construct($attrBooleanFactory, $cache, $storeResolver, $cacheTags, $storeManager, $serializer);
        $this->renderers = $renderers;
        $this->productHelper = $productHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(\Magento\Framework\DataObject $object)
    {
        /** @var ProductInterface $object */
        $value = [];
        $customEntities = $this->productHelper->getCustomEntities($object, $this->getAttribute()->getAttributeCode());
        foreach ($customEntities as $entity) {
            $value[] = $this->getRenderer($entity)->toHtml();
        }

        return implode(' ', $value);
    }

    /**
     * Return custom entity renderer.
     *
     * @param CustomEntityInterface $customEntity Custom entity.
     *
     * @return \Smile\CustomEntityProductLink\Block\Entity\Attribute\CustomEntity\Renderer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRenderer(CustomEntityInterface $customEntity)
    {
        /** @var \Smile\CustomEntity\Model\CustomEntity $customEntity */
        $renderer = $this->renderers[$customEntity->getAttributeSetUrlKey()] ?? $this->renderers['default'];
        $renderer->setCustomEntity($customEntity);

        return $renderer;
    }
}

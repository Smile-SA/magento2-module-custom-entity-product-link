<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Block\Entity\Attribute\CustomEntity;

use Magento\Framework\View\Element\Template;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * Custom entity attribute renderer.
 *
 * @method Renderer setCustomEntity(CustomEntityInterface $customEntity)
 * @method CustomEntityInterface getCustomEntity()
 */
class Renderer extends Template
{
    /**
     * Return template name.
     */
    public function getTemplate(): string
    {
        return 'Smile_CustomEntityProductLink::entity/attribute/custom_entity/renderer.phtml';
    }
}

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
namespace Smile\CustomEntityProductLink\Block\Entity\Attribute\CustomEntity;

use Magento\Framework\View\Element\Template;
use Smile\CustomEntity\Api\Data\CustomEntityInterface;

/**
 * Custom entity attribute renderer.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Maxime LECLERCQ <maxime.leclercq@smile.fr>
 *
 * @method Renderer setCustomEntity(CustomEntityInterface $customEntity)
 * @method CustomEntityInterface getCustomEntity()
 */
class Renderer extends Template
{
    /**
     * Return template name.
     *
     * @return string
     */
    public function getTemplate()
    {
        return parent::getTemplate() ?? 'Smile_CustomEntityProductLink::entity/attribute/custom_entity/renderer.phtml';
    }
}

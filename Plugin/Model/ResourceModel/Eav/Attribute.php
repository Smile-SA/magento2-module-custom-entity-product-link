<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Model\ResourceModel\Eav;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute as Subject;

class Attribute
{
    /**
     * Whether allowed for rule condition
     */
    public function afterIsAllowedForRuleCondition(Subject $subject, bool $result): bool
    {
        if ($subject->getFrontendInput() === 'smile_custom_entity') {
            $result = (bool) $subject->getIsVisible();
        }

        return $result;
    }
}

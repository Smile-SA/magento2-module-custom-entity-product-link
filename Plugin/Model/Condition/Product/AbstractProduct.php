<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Model\Condition\Product;

use Magento\Rule\Model\Condition\Product\AbstractProduct as Subject;

/**
 * Plugin added to make customEntity attributes work as a multiselect attribute for indexing, virtual category rules,
 * cart price rules.
 */
class AbstractProduct
{
    public function afterGetInputType(Subject $subject, string $result): string
    {
        if (is_object($subject->getAttributeObject())) {
            if ($subject->getAttributeObject()->getFrontendInput() == 'smile_custom_entity') {
                $result = 'multiselect';
            }
        }

        return $result;
    }

    public function afterGetValueElementType(Subject $subject, string $result): string
    {
        if (is_object($subject->getAttributeObject())) {
            if ($subject->getAttributeObject()->getFrontendInput() == 'smile_custom_entity') {
                $result = 'multiselect';
            }
        }

        return $result;
    }
}

<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Plugin\Model\Condition\Product;

use Magento\Rule\Model\Condition\Product\AbstractProduct as Subject;

/**
 * To make customEntity attributes work as a multiselect attribute for indexing, virtual category, cart price rules.
 */
class AbstractProduct
{
    /**
     * Return multiselect for smile_custom_entity attribute
     */
    public function afterGetInputType(Subject $subject, string $result): string
    {
        if (is_object($subject->getAttributeObject())) {
            if ($subject->getAttributeObject()->getFrontendInput() == 'smile_custom_entity') {
                $result = 'multiselect';
            }
        }

        return $result;
    }

    /**
     * Return multiselect for smile_custom_entity attribute
     */
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

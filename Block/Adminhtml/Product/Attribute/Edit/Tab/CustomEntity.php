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

namespace Smile\CustomEntityProductLink\Block\Adminhtml\Product\Attribute\Edit\Tab;

/**
 * Append config fields for product attributes.
 *
 * TODO : manage field deps.
 *
 * @category Smile
 * @package  Smile\CustomEntityProductLink
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class CustomEntity extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Smile\CustomEntity\Model\CustomEntity\AttributeSet\Options
     */
    private $attributeSetOptions;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context                                 $context             Context.
     * @param \Magento\Framework\Registry                                             $registry            Registry.
     * @param \Magento\Framework\Data\FormFactory                                     $formFactory         Form factory.
     * @param \Smile\CustomEntity\Model\CustomEntity\AttributeSet\Options             $attributeSetOptions Custom entities sets.
     * @param array                                                                   $data                Additional data.
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Smile\CustomEntity\Model\CustomEntity\AttributeSet\Options $attributeSetOptions,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->attributeSetOptions = $attributeSetOptions;
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('entity_attribute');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Custom entity configuration')]);


        $fieldset->addField(
            'custom_entity_attribute_set_id',
            'select',
            [
                'name'  => 'custom_entity_attribute_set_id',
                'label'  => __('Custom entity type'),
                'title'  => __('Custom entity type'),
                'values' => $this->attributeSetOptions->toOptionArray(),
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

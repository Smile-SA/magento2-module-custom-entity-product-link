<?php

declare(strict_types=1);

namespace Smile\CustomEntityProductLink\Model\Product\Indexer\Fulltext\Datasource;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface as EntityAttributeInterface;
use Smile\CustomEntityProductLink\Model\ResourceModel\Product\Indexer\Fulltext\Datasource\CustomEntity as ResourceModel;
use Smile\ElasticsuiteCatalog\Helper\AbstractAttribute as AttributeHelper;
use Smile\ElasticsuiteCore\Api\Index\DatasourceInterface;
use Smile\ElasticsuiteCore\Api\Index\Mapping\DynamicFieldProviderInterface;
use Smile\ElasticsuiteCore\Api\Index\Mapping\FieldInterface;
use Smile\ElasticsuiteCore\Helper\Mapping as MappingHelper;
use Smile\ElasticsuiteCore\Index\Mapping\FieldFactory;

/**
 * Datasource used to append custom entities data to product during indexing.
 */
class CustomEntity implements DatasourceInterface, DynamicFieldProviderInterface
{
    private AttributeHelper $attributeHelper;

    /**
     * @var AttributeInterface[]
     */
    private array $attributeById = [];

    private ResourceModel $resourceModel;

    /**
     * @var FieldInterface[]
     */
    private array $fields = [];

    private FieldFactory $fieldFactory;

    private MappingHelper $mappingHelper;

    /**
     * CustomEntity constructor.
     *
     * @param ResourceModel $resourceModel Resource model
     * @param FieldFactory $fieldFactory Field factory.
     * @param AttributeHelper $attributeHelper Attribute helper.
     * @param MappingHelper $mappingHelper Mapping helper.
     */
    public function __construct(
        ResourceModel $resourceModel,
        FieldFactory $fieldFactory,
        AttributeHelper $attributeHelper,
        MappingHelper $mappingHelper
    ) {
        $this->resourceModel = $resourceModel;
        $this->fieldFactory = $fieldFactory;
        $this->attributeHelper = $attributeHelper;
        $this->mappingHelper = $mappingHelper;
        $this->initAttributes();
    }

    /**
     * @inheritdoc
     */
    public function addData($storeId, array $indexData): array
    {
        $productIds   = array_keys($indexData);
        $customEntitiesData = $this->loadCustomEntityRowData($storeId, $productIds, array_keys($this->attributeById));

        foreach ($customEntitiesData as $customEntityData) {
            $productId = (int) $customEntityData['product_id'];
            unset($customEntityData['product_id']);

            $attribute = $this->attributeById[$customEntityData['attribute_id']];
            $indexData[$productId][$attribute->getAttributeCode()][] = $customEntityData['entity_id'];
            $optionAttributeCode = $this->mappingHelper->getOptionTextFieldName($attribute->getAttributeCode());
            $indexData[$productId][$optionAttributeCode][] = $customEntityData['name'];

            if (!isset($indexData[$productId]['indexed_attributes'])) {
                $indexData[$productId]['indexed_attributes'] = [$attribute->getAttributeCode()];
            } elseif (!in_array($attribute->getAttributeCode(), $indexData[$productId]['indexed_attributes'])) {
                $indexData[$productId]['indexed_attributes'][] = $attribute->getAttributeCode();
            }
        }

        return $indexData;
    }

    /**
     * Return a list of mapping fields.
     *
     * @return FieldInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Initialize smile_custom_entity attributes.
     *
     * @return $this
     */
    private function initAttributes(): self
    {
        $attributeCollection = $this->attributeHelper->getAttributeCollection();
        $attributeCollection->addFieldToFilter('frontend_input', 'smile_custom_entity');
        $this->resourceModel->addIndexedFilterToAttributeCollection($attributeCollection);

        foreach ($attributeCollection as $attribute) {
            $this->attributeById[$attribute->getId()] = $attribute;
            $this->addField($attribute);
        }

        return $this;
    }

    /**
     * Return custom entities row data.
     *
     * @param int|string $storeId Current store id.
     * @param array $productIds Product ids.
     * @param array $attributeIds Attribute ids.
     * @return array
     */
    private function loadCustomEntityRowData($storeId, array $productIds, array $attributeIds): array
    {
        return $this->resourceModel->loadCustomEntity($storeId, $productIds, $attributeIds);
    }

    /**
     * Add mapping fields.
     *
     * @param AttributeInterface $attribute Atribute.
     * @return $this
     */
    private function addField(AttributeInterface $attribute): self
    {
        $fieldName = $attribute->getAttributeCode();

        /** @var AttributeHelper $fieldConfig */
        /** @var EntityAttributeInterface $attribute */
        $fieldConfig = $this->attributeHelper->getMappingFieldOptions($attribute);

        $optionFieldName = $this->attributeHelper->getOptionTextFieldName($fieldName);
        $fieldType = FieldInterface::FIELD_TYPE_TEXT;
        $fieldOptions = ['name' => $optionFieldName, 'type' => $fieldType, 'fieldConfig' => $fieldConfig];
        $this->fields[$optionFieldName] = $this->fieldFactory->create($fieldOptions);

        // Reset parent field values : only the option text field should be used for spellcheck and autocomplete.
        $fieldConfig['is_used_in_spellcheck'] = false;
        $fieldConfig['is_searchable'] = false;

        /** @var AbstractAttribute $attribute */
        $fieldType = $this->attributeHelper->getFieldType($attribute->getAttributeId());
        $fieldOptions = ['name' => $fieldName, 'type' => $fieldType, 'fieldConfig' => $fieldConfig];

        $this->fields[$fieldName] = $this->fieldFactory->create($fieldOptions);

        return $this;
    }
}

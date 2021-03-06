<?php

namespace BlueBear\EngineBundle\Factory;

use BlueBear\EngineBundle\Behavior\HasException;
use BlueBear\EngineBundle\Entity\EntityModel;
use BlueBear\EngineBundle\Engine\Entity\EntityBehavior;
use BlueBear\EngineBundle\Engine\Entity\EntityType;
use BlueBear\EngineBundle\Engine\Entity\EntityTypeAttribute;
use Exception;

class EntityTypeFactory
{
    use HasException;

    /**
     * @var EntityTypeAttribute[]
     */
    protected $entityTypeAttributes = [];

    /**
     * @var EntityType[]
     */
    protected $entityTypes = [];

    protected $entityBehaviors = [];

    /**
     * Entity model behaviors. Sort by name
     *
     * @var EntityModel[]
     */
    protected $entityModels = [];

    /**
     * Create entity types from configuration
     *
     * @param array $entityTypesConfig
     * @param array $entityAttributesConfig
     * @param array $entityBehaviorsConfig
     * @throws Exception
     */
    public function createEntityTypes(array $entityTypesConfig, array $entityAttributesConfig, array $entityBehaviorsConfig)
    {
        // check if configuration is not empty
        $this->throwUnless(count($entityTypesConfig), 'Empty entity types configuration');
        $this->throwUnless(count($entityAttributesConfig), 'Empty entity attribute configuration');
        $this->throwUnless(count($entityBehaviorsConfig), 'Empty entity behaviors configuration');
        // creating available entity attributes
        foreach ($entityAttributesConfig as $name => $entityAttributeConfig) {
            $attribute = new EntityTypeAttribute();
            $attribute->setName($name);
            $attribute->setLabel($entityAttributeConfig['label']);
            $attribute->setType($entityAttributeConfig['type']);
            $this->entityTypeAttributes[$name] = $attribute;
        }
        // creating available entity behaviors
        foreach ($entityBehaviorsConfig as $name => $listener) {
            $behavior = new EntityBehavior();
            $behavior->setName($name);
            $behavior->setListener($listener);
            $this->entityBehaviors[$name] = $behavior;
        }
        $toInherit = [];
        // creating available entity types
        foreach ($entityTypesConfig as $name => $entityTypeConfig) {
            $entityType = new EntityType();
            $entityType->setName($name);
            $entityType->setLabel($entityTypeConfig['label']);
            // adding current entity type attributes
            foreach ($entityTypeConfig['attributes'] as $attributeName) {
                if (!array_key_exists($attributeName, $this->entityTypeAttributes)) {
                    throw new Exception('Unknown entity attribute type : ' . $attributeName);
                }
                $entityType->addAttribute($this->entityTypeAttributes[$attributeName]);
            }
            // behaviors are optional for entity
            if (array_key_exists('behaviors', $entityTypeConfig)) {
                // adding current entity type behaviors
                foreach ($entityTypeConfig['behaviors'] as $behaviorName) {
                    if (!array_key_exists($behaviorName, $this->entityBehaviors)) {
                        throw new Exception('Unknown entity attribute behavior : ' . $behaviorName);
                    }
                    $entityType->addBehavior($this->entityBehaviors[$behaviorName]);
                }
            }
            if (array_key_exists('class', $entityTypeConfig)) {
                if (!class_exists($entityTypeConfig['class'])) {
                    throw new \UnexpectedValueException("Cannot load class: '{$entityTypeConfig['class']}'");
                }
                $entityType->setClass($entityTypeConfig['class']);
            }
            if (array_key_exists('parent', $entityTypeConfig)) {
                $toInherit[$name] = $entityTypeConfig;
            }
            $this->entityTypes[$name] = $entityType;

        }
        foreach ($toInherit as $name => $entityTypeConfig) {
            if (!array_key_exists($entityTypeConfig['parent'], $this->entityTypes)) {
                throw new Exception("Invalid parent name : {$entityTypeConfig['parent']}");
            }
            $entityType = $this->entityTypes[$name];
            $entityTypeParent = $this->entityTypes[$entityTypeConfig['parent']];

            foreach ($entityTypeParent->getBehaviors() as $behavior) {
                $entityType->addBehavior($behavior);
            }
            if (!array_key_exists('label', $entityTypeConfig)) {
                $entityType->setLabel($entityTypeParent->getLabel());
            }
            if (!array_key_exists('class', $entityTypeConfig)) {
                $entityType->setClass($entityTypeParent->getClass());
            }
        }
    }

    /**
     * @return EntityType[]
     */
    public function getEntityTypes()
    {
        return $this->entityTypes;
    }

    /**
     * @param $entityTypeName
     * @return EntityType
     * @throws Exception
     */
    public function getEntityType($entityTypeName)
    {
        if (!array_key_exists($entityTypeName, $this->entityTypes)) {
            throw new Exception("Invalid entity type name : {$entityTypeName}");
        }
        return $this->entityTypes[$entityTypeName];
    }

    /**
     * @return EntityTypeAttribute[]
     */
    public function getEntityTypeAttributes()
    {
        return $this->entityTypeAttributes;
    }

    /**
     * @param EntityTypeAttribute[] $entityTypeAttributes
     */
    public function setEntityTypeAttributes($entityTypeAttributes)
    {
        $this->entityTypeAttributes = $entityTypeAttributes;
    }

    /**
     * @return EntityBehavior[]
     */
    public function getEntityBehaviors()
    {
        return $this->entityBehaviors;
    }
}

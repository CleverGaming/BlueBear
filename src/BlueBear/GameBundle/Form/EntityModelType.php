<?php

namespace BlueBear\GameBundle\Form;

use BlueBear\GameBundle\Entity\EntityModel;
use BlueBear\GameBundle\Game\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var EntityModel $entityModel */
        $entityModel = $options['data'];

        $builder->add('name', 'text', [
            'help_block' => 'Name of the unit'
        ]);

        if ($entityModel->getId()) {
            $builder->add('type', 'choice', [
                'choices' => $this->sortEntityTypes($options['entity_types']),
                'attr' => [
                    'disabled' => 'disabled'
                ]
            ]);
            $builder->add('attributes', 'attribute_collection', [
                'type' => 'entity_model_attribute',
                'allow_add' => true,
                'widget_add_btn' => [
                    'label' => 'Add attribute'
                ]
            ]);
        } else {
            $builder->add('type', 'choice', [
                'choices' => $this->sortEntityTypes($options['entity_types'])
            ]);
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'BlueBear\GameBundle\Entity\EntityModel',
            'entity_types' => []
        ]);
    }

    public function getName()
    {
        return 'entity_model';
    }

    protected function sortEntityTypes(array $entityTypes)
    {
        $sorted = [];
        /** @var EntityType $entityType */
        foreach ($entityTypes as $entityType) {
            $sorted[$entityType->getName()] = $entityType;
        }
        return $entityTypes;
    }
}
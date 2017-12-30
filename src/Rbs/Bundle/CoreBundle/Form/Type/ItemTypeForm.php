<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemTypeForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('itemType', 'choice', array(
                'choices'  => array(
                    'Chick' => ItemType::Chick,
                    'Cattle' => ItemType::Cattle,
                    'Fish' => ItemType::Fish,
                    'Poultry' => ItemType::Poultry,
                )
            ))
            ->add('bundles', null, array('label' => 'Modules'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\ItemType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_itemtype';
    }
}

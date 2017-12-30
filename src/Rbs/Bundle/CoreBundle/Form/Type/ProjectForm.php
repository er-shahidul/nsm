<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('isHeadOffice', null, array(
                'required' => false
            ))*/
            ->add('projectName', null, array(
                'label' => 'Name'
            ))
            ->add('address')
            /*->add('costCenterNumber')
            ->add('projectHead', null, array(
                'attr' => array('class' => 'select2me')
            ))
            ->add('projectContactPerson', null, array(
                'attr' => array('class' => 'select2me')
            ))*/
            ->add('projectArea', null, array(
                'attr' => array('class' => 'select2me'),
                'label' => 'Area'
            ))
            ->add('projectCategory', null, array(
                'attr' => array('class' => 'select2me'),
                'label' => 'Factory Type'
            ))
            //->add('users')
            ->add('bundles', null, array('label' => 'Modules'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_project';
    }
}

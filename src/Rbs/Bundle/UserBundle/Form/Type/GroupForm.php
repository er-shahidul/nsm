<?php

namespace Rbs\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupForm extends AbstractType
{
    private $class;
    private $permissionBuilder;

    public function __construct($class, $permissionBuilder)
    {
        $this->class = $class;
        $this->permissionBuilder = $permissionBuilder;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'label' => 'Name',
            'attr'  => array('class' => 'span5')
        ));

        $builder->add('description', 'textarea', array(
            'label'    => 'Description',
            'required' => false,
            'attr'     => array('class' => 'span5', 'rows' => 3),
        ));

        $builder->add('roles', 'choice', array(
            'choices'  => $this->permissionBuilder->getPermissionHierarchyForChoiceField(),
            'multiple' => true,
            'constraints' => array(
                new NotBlank(array('message'=>'Roles should not be blank'))
            ),
        ));

        $builder
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'group',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fos_user_group';
    }
}

<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TargetForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zilla', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 4)->orderBy('a.name');
                },
                'attr' => array(
                    'class' => 'zilla-selector select2me',
                    'id' => 'user_level1'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Zilla should not be blank'
                    )),
                ),
                'required' => true
            ))
            ->add('upozilla', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 5)->orderBy('a.name');
                },
                'attr' => array(
                    'class' => 'thana-selector select2me',
                    'id' => 'user_level2'
                ),
                'required' => true
            ))
            ->add('child_entities', 'collection', array(
                'type' => new TargetSubForm(),
            ))
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Target'
        ));
    }

    public function getName()
    {
        return 'target';
    }
}

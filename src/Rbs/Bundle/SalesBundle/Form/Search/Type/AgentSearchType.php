<?php

namespace Rbs\Bundle\SalesBundle\Form\Search\Type;

use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentSearchType extends AbstractType
{
    private $agents;
    function __construct($agents)
    {
        $this->agents = $agents;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('agent', 'choice', array(
                'choices' => $this->agents,
                'attr' => array(
                    'class' => 'select2me input-medium'
                )
            ))
            ->add('start_date', 'text', array(
                'attr' => array(
                    'class' => 'date-picker input-small form-control'
                )
            ))
            ->add('end_date', 'text', array(
                'attr' => array(
                    'class' => 'date-picker input-small form-control'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    public function getName()
    {
        return 'search';
    }
}

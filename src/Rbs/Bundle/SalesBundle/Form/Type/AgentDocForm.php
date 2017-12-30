<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Entity\AgentDoc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentDocForm extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileType', 'choice', array(
                'choices'  => array(
                    'TIN_CERTIFICATE' => AgentDoc::TIN_CERTIFICATE,
                    'TRADE_LICENCE' => AgentDoc::TRADE_LICENCE,
                    'UNION_CERTIFICATE' => AgentDoc::UNION_CERTIFICATE
                ),
                'data' => AgentDoc::TIN_CERTIFICATE
            ))
            ->add('remark')
            ->add('file')
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\AgentDoc'
        ));
    }

    public function getName()
    {
        return 'agent_doc';
    }
}

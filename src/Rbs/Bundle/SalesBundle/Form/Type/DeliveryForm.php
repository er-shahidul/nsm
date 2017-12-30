<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\DepoRepository;
use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\SalesBundle\Repository\SmsRepository;
use Rbs\Bundle\UserBundle\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contactName')
            ->add('contactNo')
            ->add('otherInfo')
            ->add('transportGiven', 'choice', array(
                'choices'  => array(
                    'NOURISH' => Delivery::NOURISH,
                    'AGENT' => Delivery::AGENT,
                ),
                'data' => Delivery::NOURISH
            ))
            ->add('depo', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Depo',
                'property' => 'name',
                'required' => false,
                'empty_value' => 'Select Depo',
                'empty_data' => null,
                'query_builder' => function (DepoRepository $repository)
                {
                    return $repository->createQueryBuilder('p')
                        ->andWhere('p.deletedAt IS NULL')
                        ;
                }
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Delivery'
        ));
    }

    public function getName()
    {
        return 'delivery';
    }
}

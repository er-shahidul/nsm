<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\DepoRepository;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryAddForm extends AbstractType
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
            ->add('depo', 'entity', array(
                'class' => 'RbsCoreBundle:Depo',
                'property' => 'name',
                'required' => true,
                'empty_data' => null,
                'query_builder' => function (DepoRepository $repository)
                {
                    return $repository->createQueryBuilder('d')
                        ->join('d.users', 'u')
                        ->where('d.deletedAt IS NULL')
                        ->andWhere('u.id = :user')
                        ->andWhere('d.deletedAt IS NULL')
                        ->setParameter('user', $this->user->getId());
                }
            ))
            ->add('orders', 'entity', array(
                'class' => 'RbsSalesBundle:Order',
                'property' => 'id',
                'required' => true,
                'multiple' => true,
                'empty_value' => 'Select Order',
                'empty_data' => null,
                'query_builder' => function (OrderRepository $repository)
                {
                    return $repository->createQueryBuilder('o')
                        ->join('o.agent', 'a')
                        ->join('a.user', 'u')
                        ->where('o.deliveryState != :COMPLETE')
                        ->andWhere('o.orderState != :CANCEL')
                        ->andWhere('o.orderState != :PENDING')
                        ->andWhere('o.deliveryState != :DELIVERY_STATE_PENDING')
                        ->setParameters(array('COMPLETE'=>Order::DELIVERY_STATE_SHIPPED, 'CANCEL'=>Order::ORDER_STATE_CANCEL,
                            'PENDING'=>Order::ORDER_STATE_PENDING, 'DELIVERY_STATE_PENDING'=>Order::DELIVERY_STATE_PENDING));
                }
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

        ));
    }

    public function getName()
    {
        return 'delivery_add_form';
    }
}

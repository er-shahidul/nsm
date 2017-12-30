<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Repository\DeliveryRepository;
use Rbs\Bundle\SalesBundle\Repository\VehicleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleDeliveryForm extends AbstractType
{
    private $user;
    private $truckInfo;

    public function __construct($user, $truckInfo)
    {
        $this->user = $user;
        $this->truckInfo = $truckInfo;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vehicle', 'entity', array(
                'class' => 'Rbs\Bundle\SalesBundle\Entity\Vehicle',
                'property' => 'truckInformation',
                'mapped' => false,
                'query_builder' => function (VehicleRepository $repository)
                {
                    return $repository->createQueryBuilder('t')
                        ->where('t.id = :truckInfoId')
                        ->setParameter('truckInfoId', $this->truckInfo->getId());
                }
            ));
            $builder
                ->add('deliveries', 'entity', array(
                    'class' => 'RbsSalesBundle:Delivery',
                    'property' => 'id',
//                    'property' => 'orderRef',
                    'required' => true,
                    'multiple' => true,
                    'query_builder' => function (DeliveryRepository $repository)
                    {
                        return $repository->createQueryBuilder('deliveries')
                            ->join('deliveries.depo', 'd')
                            ->join('deliveries.orders', 'o')
                            ->join('d.users', 'u')
                            ->where('u =:user')
                            ->andWhere('o.deliveryState IN (:READY) OR o.deliveryState IN (:PARTIALLY_SHIPPED)')
//                            ->andWhere('o.id IN (:ordersId)')
                            ->setParameters(array('user'=>$this->user->getId(), 'READY'=>Order::DELIVERY_STATE_READY,
                                'PARTIALLY_SHIPPED'=>Order::DELIVERY_STATE_PARTIALLY_SHIPPED));
//                                'PARTIALLY_SHIPPED'=>Order::DELIVERY_STATE_PARTIALLY_SHIPPED, 'ordersId'=> ($this->truckInfo->getOrdersId())));
                    }
                ));
//        }
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Vehicle'
        ));
    }

    public function getName()
    {
        return 'vehicle';
    }
}

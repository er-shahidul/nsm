<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Entity\Delivery;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\Vehicle;
use Rbs\Bundle\SalesBundle\Repository\DeliveryRepository;
use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Rbs\Bundle\SalesBundle\Repository\VehicleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class VehicleDeliverySetForm extends AbstractType
{
    private $user;
    private $vehicleId;

    public function __construct($user, $vehicleId)
    {
        $this->user = $user;
        $this->vehicleId = $vehicleId;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vehicle', 'entity', array(
                'class' => 'RbsSalesBundle:Vehicle',
                'property' => 'truckInformation',
                'query_builder' => function (VehicleRepository $repository)
                {
                    return $repository->createQueryBuilder('v')
                        ->where('v.id = :vehicleId')
                        ->setParameter('vehicleId', $this->vehicleId);
                }
            ))
            ->add('orders', 'entity', array(
                'class' => 'RbsSalesBundle:Order',
                'property' => 'getOrderInfoWithAgent',
                'required' => true,
                'multiple' => true,
                'query_builder' => function (OrderRepository $repository)
                {
                    return $repository->createQueryBuilder('o')
                        ->join('o.depo', 'd')
                        ->join('d.users', 'u')
                        ->where('o.deliveryState = :PARTIALLY_SHIPPED OR o.deliveryState = :READY')
                        ->andWhere('u.id = :user')
                        ->setParameters(array('PARTIALLY_SHIPPED'=>Order::DELIVERY_STATE_PARTIALLY_SHIPPED, 'READY'=>Order::DELIVERY_STATE_READY,
                            'user' => $this->user->getId()))
                        ->orderBy('o.id', 'desc');
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
        return 'vehicle_delivery_form';
    }
}

<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashReceiveForm extends AbstractType
{
    private $depoId;

    public function __construct($depoId)
    {
        $this->depoId = $depoId;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

//        if($this->depoId != 0){
//            $builder
//            ->add('orderRef', 'entity', array(
//                'class' => 'RbsSalesBundle:Order',
//                'attr' => array(
//                    'class' => 'select2me'
//                ),
//                'property' => 'orderIdAndAgent',
//                'required' => true,
//                'empty_value' => 'Select Order',
//                'empty_data' => null,
//                'query_builder' => function (OrderRepository $repository)
//                {
//                    return $repository->createQueryBuilder('o')
//                        ->join('o.agent', 'a')
//                        ->where('o.deliveryState != :deliveryState')
//                        ->andWhere('o.orderState != :orderState')
//                        ->andWhere('o.paymentState != :paymentState')
//                        ->andWhere('o.depo = :depoId')
//                        ->orderBy('o.id', 'DESC')
//                        ->setParameters(
//                            array(
//                                'deliveryState' => Order::DELIVERY_STATE_PENDING,
//                                'orderState'    => Order::ORDER_STATE_CANCEL,
//                                'paymentState'  => Order::PAYMENT_STATE_PAID,
//                                'depoId'        => $this->depoId,
//                            )
//                        );
//                }
//            ));
//        }else{
//            $builder
//            ->add('orderRef', 'entity', array(
//                'class' => 'RbsSalesBundle:Order',
//                'attr' => array(
//                    'class' => 'select2me'
//                ),
//                'property' => 'orderIdAndAgent',
//                'required' => true,
//                'empty_value' => 'Select Order',
//                'empty_data' => null,
//                'query_builder' => function (OrderRepository $repository)
//                {
//                    return $repository->createQueryBuilder('o')
//                        ->join('o.agent', 'a')
//                        ->where('o.orderState != :CANCEL')
//                        ->andWhere('o.orderState != :PROCESSING')
//                        ->setParameters(array('CANCEL'=>Order::ORDER_STATE_CANCEL, 'PROCESSING'=>Order::ORDER_STATE_PROCESSING));
//                }
//            ));
//        }
        $builder
            ->add('agent', 'entity', array(
                'class' => 'RbsSalesBundle:Agent',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'getIdName',
                'empty_value' => 'Select Agent',
                'empty_data' => null,
                'query_builder' => function (AgentRepository $repository)
                {
                    return $repository->createQueryBuilder('c')
                        ->join('c.user', 'u')
                        ->join('u.profile', 'p')
                        ->where('u.deletedAt IS NULL')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.userType = :AGENT')
                        ->setParameter('AGENT', 'AGENT')
                        ->orderBy('p.fullName','ASC');
                }
            ))
            ->add('amount', null, array(
                'attr' => array(
                    'class' => 'input-small input-mask-amount'
                )
            ))
            ->add('depositor')
            ->add('remark')
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\CashReceive'
        ));
    }

    public function getName()
    {
        return 'cash_receive';
    }
}

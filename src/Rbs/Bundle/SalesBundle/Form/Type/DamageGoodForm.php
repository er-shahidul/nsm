<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Form\DataTransformer\OrderIdDataTransformer;
use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DamageGoodForm extends AbstractType
{
    private $location;
    private $em;

    public function __construct($location, $em)
    {
        $this->location = $location;
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', 'text', array(
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Order should not be blank'
                    ))
                ),
                'attr' => array(
                    'class' => 'input-small input-mask-amount'
                )
            ))
            ->add('remark')
            ->add('orderRef', 'text')
            /*->add('orderRef', 'entity', array(
                'class' => 'RbsSalesBundle:Order',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'orderInfoForDamageGoods',
                'required' => true,
                'empty_value' => 'Select Order',
                'empty_data' => null,
                'query_builder' => function (OrderRepository $repository)
                {
                    return  $repository->createQueryBuilder('o')
                        ->join('o.agent', 'a')
                        ->join('a.user', 'u')
                        ->where('o.orderState = :COMPLETE')
                        ->andWhere('u.zilla = :location')
                        ->orderBy('o.id', 'DESC')
                        ->setParameter('location', $this->location)
                        ->setParameter('COMPLETE', Order::ORDER_STATE_COMPLETE);
                },
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Order should not be blank'
                    )),
                )
            ))*/
            ->add('file')
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
            ))
        ;

        $builder->get('orderRef')->addModelTransformer(new OrderIdDataTransformer($this->em));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\DamageGood'
        ));
    }

    public function getName()
    {
        return 'damage_good';
    }
}

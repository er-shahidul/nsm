<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\DepoRepository;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\SalesBundle\Repository\SmsRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderForm extends AbstractType
{
    private $refSms;

    public function __construct($refSms)
    {
        $this->refSms = $refSms;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Agent $agent */
        $agent = $options['data']->getAgent();
        $refSMS = $options['data']->getRefSms();

        if (!$agent) {
            $builder
                ->add('agent', 'entity', array(
                    'class' => 'RbsSalesBundle:Agent',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
                    'property' => 'getIdName',
                    'required' => true,
                    'empty_value' => 'Select Agent',
                    'query_builder' => function (AgentRepository $repository)
                    {
                        return $repository->createQueryBuilder('c')
                            ->join('c.user', 'u')
                            ->join('u.profile', 'p')
                            ->where('u.deletedAt IS NULL')
                            ->andWhere('u.enabled = 1')
                            ->andWhere('u.userType = :AGENT')
                            ->setParameter('AGENT', User::AGENT)
                            ->orderBy('p.fullName','ASC');
                    },
                    'constraints' => array(
                        new NotBlank(array(
                            'message'=>'Agent should not be blank'
                        )),
                    ),
                ))
                ->add('depo', 'entity', array(
                    'class' => 'Rbs\Bundle\CoreBundle\Entity\Depo',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
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

        $builder
            ->add('totalAmount', 'text', array(
                'read_only' => true
            ));

        if ($this->refSms) {

            $builder
                ->add('refSMS', 'entity', array(
                    'class' => 'RbsSalesBundle:Sms',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
                    'required' => true,
                    'property' => 'mobileNoAndMsg',
                    'query_builder' => function (SmsRepository $repository) use ($refSMS) {
                        $query = $repository->createQueryBuilder('sms')
                            ->where('sms.deletedAt IS NULL')
                            ->andWhere('sms.status = :UNREAD')
                            ->andWhere('sms.order IS NULL')
                            ->andWhere('sms.id = :refSms')
                            ->setParameter('refSms', $this->refSms)
                            ->setParameter('UNREAD', 'UNREAD')
                            ->orderBy('sms.id', 'DESC');
                        if ($refSMS) {
                            $query->orWhere("sms.id = :smsId")->setParameter("smsId", $refSMS->getId());
                        }
                        return $query;
                    }
                ))
                ->add('depo', 'entity', array(
                    'class' => 'Rbs\Bundle\CoreBundle\Entity\Depo',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
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
        }else{
            $builder
                ->add('refSMS', 'entity', array(
                    'class' => 'RbsSalesBundle:Sms',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
                    'property' => 'mobileNoAndMsg',
                    'required' => false,
                    'empty_value' => 'Select SMS',
                    'empty_data' => null,
                    'query_builder' => function (SmsRepository $repository) use ($refSMS) {
                        $query = $repository->createQueryBuilder('sms')
                            ->where('sms.deletedAt IS NULL')
                            ->andWhere('sms.status = :UNREAD')
                            ->andWhere('sms.order IS NULL')
                            ->setParameter('UNREAD', 'UNREAD')
                            ->orderBy('sms.id', 'DESC');
                        if ($refSMS) {
                            $query->orWhere("sms.id = :smsId")->setParameter("smsId", $refSMS->getId());
                        }
                        return $query;
                    }
                ))
                ->add('depo', 'entity', array(
                    'class' => 'Rbs\Bundle\CoreBundle\Entity\Depo',
                    'attr' => array(
                        'class' => 'select2me'
                    ),
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

        $builder
            ->add('remark');
        $builder
            ->add('orderItems', 'collection', array(
                'type'         => new OrderItemForm($agent),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype'    => true,
                'label_attr'   => array(
                    'class' => 'hidden',
                ),
            ))
        ;
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Order'
        ));
    }

    public function getName()
    {
        return 'order';
    }
}
